# Kinkoza Marketplace POC

A B2B marketplace for professional assets including machinery, vehicles, commercial property, and intangible assets. This is a time-boxed technical-challenge proof of concept, not a completed production system.

## Setup

Docker provides a consistent local environment, so the application and its supporting services can be started without installing or configuring each dependency manually.

1. Clone the repository.
2. Copy the environment file:

   ```sh
   cp .env.example .env
   ```

3. Add the separately shared AWS S3 credentials to `.env`:

   ```dotenv
   AWS_ACCESS_KEY_ID=
   AWS_SECRET_ACCESS_KEY=
   AWS_DEFAULT_REGION=us-east-1
   AWS_BUCKET=
   AWS_USE_PATH_STYLE_ENDPOINT=false
   ```

4. Start the application:

   ```sh
   docker compose up -d
   ```

5. Open http://localhost:8000.

The application container creates the SQLite database if needed, generates an application key when one is absent, runs migrations, seeds the demo data once, and imports the Typesense index during startup. The seed marker is stored in the Docker database volume, so later container restarts do not create duplicate demo data. The compose configuration uses `xyz` as the local Typesense API key unless `TYPESENSE_API_KEY` is set in the shell or environment.

## Demo Credentials

| Account | Email | Password |
| --- | --- | --- |
| Seller A | seller-a@example.com | password |
| Seller B | seller-b@example.com | password |

The accounts belong to different companies. Use them to verify ownership boundaries: Seller A must not be able to access or modify Seller B's listings.

## What I Built

- Typesense-powered search, filters, sorting, and paginated public marketplace browsing.
- Public listing detail pages with images, Open Graph tags, and JSON-LD structured data.
- Authenticated seller dashboard and company-scoped listing create, read, update, and publish flows.
- Authentication-gated contact reveal, with own-company listings handled separately.
- S3-backed listing media with temporary signed URLs.
- Route and action-level throttling for the public marketplace and contact reveal.
- Docker-based local environment, realistic seed data, and Pest feature tests.

## Product Approach

I treated the task as an MVP for a B2B marketplace. The primary goal was to make the important marketplace loop complete and defensible:

```text
Discover listing -> filter/search -> open listing -> assess asset -> reveal seller contact
```

I deliberately did not spend disproportionate time on visual refinement. The time went into correctness, ownership/security boundaries, search, and a maintainable domain model. That is a prioritisation choice for a challenge POC, rather than a claim that visual polish is unimportant in a finished product.

## Data Boundaries

Each seller belongs to a company. Public listing information is distinct from company contact information:

```text
Public discovery: Listing title, description, category, price, location
Private seller data: Company email and phone
```

This matters because listings must be searchable, while seller contact details are commercially sensitive. Contact details are kept on the `Company` model rather than being included in the public listing search document. This limits the chance that a search index or public listing response accidentally becomes a source of harvested contact data.

The POC models company verification with a KYB status. A production onboarding flow would need proper document collection, verification, review, and audit handling.

## Search

The marketplace uses Laravel Scout with Typesense. A marketplace is expected to be read-heavy: buyers browse, filter, and search far more often than sellers create or edit listings. Typesense provides better discovery behavior than simple database text matching and gives a future path to capabilities such as fuzzy matching, facets, and geo-spatial search. Those future capabilities are not all implemented in this POC.

The relational database remains the source of truth; Typesense is a derived search index:

```text
Database (authoritative records) -> Typesense (searchable projection)
```

This introduces eventual consistency. A database write can complete before the search result reflects it. That is an intentional trade-off: search supports discovery but does not own business data. The index can be rebuilt from the database with Scout's import command.

## Caching

The public listing detail page uses Laravel `Cache::flexible()` for listing media data. This stale-while-revalidate approach serves cached data during the stale window while Laravel refreshes it, reducing repeated work for frequently viewed listings.

Listing updates explicitly clear the corresponding cache entry through `Cache::forget()`. The write path therefore owns cache invalidation for data it changes.

This is intentionally different from search:

| Concern | Consistency model |
| --- | --- |
| Listing detail media | Database-backed cache with explicit invalidation on update |
| Marketplace search | Derived Typesense index with eventual consistency |

The POC uses SQLite as its cache store.

## Database and Infrastructure

SQLite is used for the application database and cache. It makes this POC simple to run and keeps the challenge focused on application design rather than local infrastructure operations. I did not introduce PostgreSQL and Redis merely to make the stack appear more complex.

The application uses Laravel abstractions, so this can evolve without changing the domain design. At production scale, the likely direction is PostgreSQL for relational data, Redis for shared cache/session/rate-limit workloads, and PgBouncer when database connection concurrency justifies it.

## Security and Contact Harvesting

A public marketplace has an inherent scraping risk: listings need to be public, but seller contact details have commercial value. If contact information is exposed in a public page response or search document, it can be collected without a buyer behaving like a legitimate user.

The implemented controls are:

- Company contact details are separate from public listing data.
- Contact details are not included in the Typesense search document.
- Contact reveal requires an authenticated user.
- Contact reveal is rate-limited per user.
- A seller viewing their own company's listing can access its contact information without using the public reveal path.

Rate limiting does not make scraping impossible. For a production deployment, I would add a CDN/WAF, bot detection, IP or ASN reputation signals, progressive throttling, reveal/search behavior monitoring, suspicious-account detection, and challenge mechanisms where appropriate. These are production directions, not POC features.

## Rate Limits

| Action | Limit | Rationale |
| --- | --- | --- |
| Home page | 60 requests/minute per IP | Allows normal browsing while limiting excessive requests. |
| Listing detail | 120 requests/minute per IP | Supports browsing multiple listings without making the endpoint unbounded. |
| Search/filter | 30 requests/minute per IP | Search is more expensive and easier to automate rapidly. |
| Contact reveal | 10 attempts/hour per authenticated user | This exposes sensitive business contact information, so it has the strictest limit. |

Different operations have different limits because they have different cost and abuse risk. Contact reveal is intentionally the most restricted action.

## Authorization and Validation

Authorization is enforced in the backend, not only by hiding interface controls. Listing policies compare the acting user's company to the listing's company for view, create, update, delete, and publish operations. A user cannot gain access to another company's listing by changing an identifier or crafting a request.

This is especially important with Livewire. Public Livewire component methods are network-accessible actions, so they are treated like controller endpoints: authorization is performed inside the relevant action, rather than inferred from the visible UI.

User input is validated server-side. Filter and sort values are constrained to known enums and allow-lists, and model fillable attributes control mass assignment. Validation and authorization are separate checks; valid input does not make an action permitted.

## Media Storage

Listing media is stored in S3. Media can grow independently from relational data and should not bind an application instance to local filesystem state. Images are delivered through temporary signed URLs with a 15-minute expiry rather than permanently public URLs.

At larger scale, this would likely be paired with image transformations, responsive formats, and a CDN. Those media-processing capabilities are not implemented in this POC.

## SEO

Public listing detail pages include Open Graph tags and JSON-LD structured data. Listings are discovery surfaces, so these help social platforms and search engines understand what is being shared or indexed.

Production additions would include an XML sitemap, explicit robots rules, and a defined strategy for canonical and expired-listing URLs. They are not part of the current implementation.

## Work Left Out Deliberately

Time-boxing favored depth in the core marketplace loop over breadth. The following are sensible production work, but were not the priority for this POC:

- A complete KYB integration and review workflow: a status field demonstrates the domain boundary without building a compliance system.
- Queued indexing, image processing, notifications, and external integrations: these should move off the request path when the workload justifies it. The current POC does not claim these operations are asynchronous.
- A complete transaction workflow, escrow, and e-signature: each is a substantial product domain beyond listing discovery and contact reveal.
- Advanced bot detection and CAPTCHA: rate limiting covers the baseline protection here; higher-friction controls need real traffic data to tune responsibly.
- Observability, CI/CD, and production infrastructure: valuable operational work, but not required to demonstrate the requested application behavior locally.
- A complete marketplace moderation/state-transition system: the core listing states exist, while a full review process is beyond this scope.

## Testing

The test suite uses Pest. It focuses on business and security risks rather than targeting a coverage percentage:

- Listing creation, update, browsing, and detail-page behavior.
- Ownership enforcement, including tests that Seller A cannot update, delete, or publish Seller B's listing.
- Dashboard contact-reveal counts scoped to the seller's company.
- Public marketplace search, filter, pagination, and sort behavior.

Run the test suite with:

```sh
php artisan test --compact
```

or:

```sh
vendor/bin/pest
```

## Trade-offs

| Decision | Why | Trade-off | Production direction |
| --- | --- | --- | --- |
| SQLite for database and cache | Simple local setup for a time-boxed POC | Limited concurrency and no multi-instance shared state | PostgreSQL and Redis as measured needs emerge |
| Typesense for search | Stronger read-heavy marketplace discovery than database text matching | Search is eventually consistent with the database | Scale independently and move indexing to jobs |
| `Cache::flexible()` for listing media | Reduces repeat work and supports explicit invalidation | Covers the listing-detail cache only | Redis-backed shared cache across instances |
| S3 media with temporary URLs | Keeps media out of application servers and avoids permanently public links | Requires AWS credentials during setup | Add processing pipelines and CDN delivery |
| Laravel rate limiting | Addresses obvious endpoint abuse with low complexity | Cannot stop a patient or distributed attacker by itself | Add WAF and behavior-based protections |
| Simple company onboarding | Models tenant boundaries without building KYB infrastructure | Verification is not a real verification process | Integrate KYB provider and human review workflow |

## Scaling to 100x Traffic

The POC is not presented as a 100x-scale deployment. The design keeps the important boundaries clear so the system can evolve in that direction:

```text
                         Internet
                            |
              CDN / WAF / Load Balancer
                            |
          +-----------------+-----------------+
          |                 |                 |
       Laravel           Laravel           Laravel
          |                 |                 |
          +-----------------+-----------------+
                            |
              +-------------+-------------+
              |                           |
         Typesense                    PostgreSQL
          cluster                       cluster
                                          |
                                     PgBouncer
```

- **Application:** make Laravel instances stateless and scale them horizontally behind a load balancer. The application tier should scale horizontally, not vertically.
- **Sessions and limits:** move session, cache, and rate-limit state to Redis so multiple application instances share the same state.
- **Search:** scale Typesense independently based on query volume, latency, CPU, memory, and index size. Move index updates to a queue.
- **Database:** replace SQLite with PostgreSQL. Scale vertically first where appropriate, then introduce read replicas for reads that can tolerate replication lag.
- **Connections:** use PgBouncer to avoid excessive database connections as the application fleet grows.
- **Media:** retain object storage and add a CDN and image-processing pipeline as media volume increases.

## Engineering Principle

The goal was to keep the POC easy to run and understand while establishing boundaries that make future change straightforward:

- The database is the source of truth; search is a derived, rebuildable representation.
- Private seller information is separated from public discovery data.
- Authorization is enforced at the backend boundary.
- Work moves off the request path when measured requirements justify it.
- Infrastructure is introduced because the system needs it, not because it makes a small POC look more elaborate.

The implementation intentionally balances simplicity today with a clear path to a production system tomorrow.
