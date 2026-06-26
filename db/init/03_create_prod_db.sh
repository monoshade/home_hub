#!/bin/bash
# Runs once on first DB init (after 01_schema.sql / 02_seed.sql, which target
# the default `demo` database). Creates the `prod` database alongside it and
# loads the same table schema — but not the demo seed data — into it.
set -euo pipefail

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" \
    -c "CREATE DATABASE prod;"

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname prod \
    -f /docker-entrypoint-initdb.d/01_schema.sql
