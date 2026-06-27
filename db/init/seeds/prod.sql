-- Seed data for the `prod` database. Not auto-applied (lives in a subdirectory
-- the Postgres entrypoint ignores); loaded explicitly by 03_create_extra_dbs.sh.
-- Distinct from the demo fixtures so it's clear which database you're on.

INSERT INTO items (name) VALUES ('hello from prod');

-- ===========================================================================
-- Seed: a single-family house with a few rooms and belongings.
-- (Names are unique here, so child rows can look up parents by name.)
-- ===========================================================================
INSERT INTO spaces (space_type, name, area, address, property_type, floors, year_built)
VALUES ('house', 'Oak Residence', 260, '18 Oak Street', 'residential', 2, 2015);

INSERT INTO spaces (space_type, name, area, type, floor_level, parent_space_id)
VALUES ('room', 'Kitchen', 28, 'kitchen', 1, (SELECT id FROM spaces WHERE name = 'Oak Residence'));

INSERT INTO spaces (space_type, name, area, type, floor_level, parent_space_id)
VALUES ('room', 'Master Bedroom', 32, 'bedroom', 2, (SELECT id FROM spaces WHERE name = 'Oak Residence'));

INSERT INTO spaces (space_type, name, area, capacity, attached, parent_space_id)
VALUES ('garage', 'Garage', 40, 2, true, (SELECT id FROM spaces WHERE name = 'Oak Residence'));

INSERT INTO devices (name, brand, model, status, space_id)
VALUES ('Refrigerator', 'Samsung', 'RF28', 'working', (SELECT id FROM spaces WHERE name = 'Kitchen'));

INSERT INTO furniture (name, material, dimensions, space_id)
VALUES ('Oak Dining Table', 'wood', '180x90x75 cm', (SELECT id FROM spaces WHERE name = 'Kitchen'));

INSERT INTO furniture (name, material, dimensions, space_id)
VALUES ('Queen Bed', 'wood', '210x160x50 cm', (SELECT id FROM spaces WHERE name = 'Master Bedroom'));

INSERT INTO vehicles (name, type, make, model, year, space_id)
VALUES ('Toyota RAV4', 'car', 'Toyota', 'RAV4', 2021, (SELECT id FROM spaces WHERE name = 'Garage'));
