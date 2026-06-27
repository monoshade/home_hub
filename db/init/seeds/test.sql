-- Seed data for the `test` database. Not auto-applied (lives in a subdirectory
-- the Postgres entrypoint ignores); loaded explicitly by 03_create_extra_dbs.sh.
-- Deliberately minimal and predictable so tests can rely on fixed fixtures.

INSERT INTO items (name) VALUES ('hello from test');

-- A single room with one device — just enough for tests to exercise the
-- space/item relationship without depending on a large dataset.
INSERT INTO spaces (space_type, name, area, type, floor_level)
VALUES ('room', 'Test Room', 20, 'living', 1);

INSERT INTO devices (name, brand, status, space_id)
VALUES ('Test Device', 'Acme', 'working', (SELECT id FROM spaces WHERE name = 'Test Room'));
