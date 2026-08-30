---
paths:
  - 'app/Livewire/Listings/**,app/Actions/Listings/**,app/Policies/ListingPolicy.php'
---

# Policies

## Keep listing edits tenant-scoped
Listing edit/delete flows must resolve the listing through the authenticated user's company boundary and return 404 for foreign-company records. Do not rely on route model binding alone for ownership checks; preserve tenant isolation at the component/action boundary.
