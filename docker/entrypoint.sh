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

php artisan migrate --force --no-interaction

exec php artisan serve --host=0.0.0.0 --port=8000
