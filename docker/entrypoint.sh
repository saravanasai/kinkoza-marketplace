#!/usr/bin/env sh
set -eu

mkdir -p \
  storage/framework/cache \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache \
  database

if [ ! -f database/database.sqlite ]; then
  touch database/database.sqlite
fi

# Generate APP_KEY if not present (from env vars or .env file)
if [ -z "${APP_KEY:-}" ]; then
  if [ ! -f .env ]; then
    cp .env.example .env
  fi
  php artisan key:generate --force --no-interaction || true
fi

ROLE="${1:-serve}"

# The queue worker only consumes jobs; migrations and search import run once,
# on the "serve" role, so both roles can start concurrently without racing.
if [ "$ROLE" = "worker" ]; then
  exec php artisan queue:work --tries=3 --sleep=3 --max-time=3600
fi

php artisan migrate --force --no-interaction

if [ "${SCOUT_DRIVER:-}" = "typesense" ]; then
  php artisan scout:import "App\Models\Listing" --no-interaction || true
fi

exec php artisan serve --host=0.0.0.0 --port=8000
