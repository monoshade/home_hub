#!/bin/bash
# Runs once on first DB init (after 01_schema.sql / 02_seed.sql, which target
# the default `demo` database). Creates the extra databases alongside it and
# loads the same table schema — but not the demo seed data — into each:
#
#   prod  — the production database (schema only, empty)
#   test  — an empty database for tests (schema only, empty)
set -euo pipefail

for dbname in prod test; do
    psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" \
        -c "CREATE DATABASE ${dbname};"

    psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "${dbname}" \
        -f /docker-entrypoint-initdb.d/01_schema.sql
done
