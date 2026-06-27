#!/bin/bash
# Runs once on first DB init (after 01_schema.sql, which creates the table schema
# in the default `demo` database). Creates the prod + test databases, loads the
# same schema into each, then seeds every database from its own fixture file:
#
#   demo ($POSTGRES_DB) — schema (from 01_schema.sql) + seeds/demo.sql
#   prod               — schema + seeds/prod.sql
#   test               — schema + seeds/test.sql
#
# The seed files live in seeds/ — a subdirectory the Postgres entrypoint ignores
# — so each is applied only here, to its own database.
set -euo pipefail

for dbname in prod test; do
    psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" \
        -c "CREATE DATABASE ${dbname};"

    psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "${dbname}" \
        -f /docker-entrypoint-initdb.d/01_schema.sql
done

# Seed each database from its fixture file. The default database ($POSTGRES_DB)
# gets the demo fixtures; prod and test get their own.
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" \
    -f /docker-entrypoint-initdb.d/seeds/demo.sql

for dbname in prod test; do
    psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "${dbname}" \
        -f "/docker-entrypoint-initdb.d/seeds/${dbname}.sql"
done
