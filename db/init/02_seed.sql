-- Demo seed data. Auto-applied to the default database only (POSTGRES_DB = demo).
-- The `prod` database (created by 03_create_prod_db.sh) gets the schema but not
-- this fixture data.

INSERT INTO items (name) VALUES ('hello from postgres');

-- ===========================================================================
-- Seed: a house with a couple spaces + items, and an apartment with a garage.
-- (Names are unique here, so child rows can look up parents by name.)
-- ===========================================================================
INSERT INTO spaces (space_type, name, area, address, property_type, floors, year_built)
VALUES ('house', 'Maple House', 220, '742 Maple Ave', 'residential', 2, 2008);

INSERT INTO spaces (space_type, name, area, type, floor_level, parent_space_id)
VALUES ('room', 'Living Room', 35, 'living', 1, (SELECT id FROM spaces WHERE name = 'Maple House'));

INSERT INTO spaces (space_type, name, area, surface_type, fenced, parent_space_id)
VALUES ('yard', 'Backyard', 120, 'grass', true, (SELECT id FROM spaces WHERE name = 'Maple House'));

INSERT INTO spaces (space_type, name, area, capacity, attached, parent_space_id)
VALUES ('garage', 'Two-Car Garage', 36, 2, true, (SELECT id FROM spaces WHERE name = 'Maple House'));

INSERT INTO spaces (space_type, name, area, address, property_type, unit_number, floor_level)
VALUES ('apartment', 'Downtown Loft', 75, '55 Center St', 'residential', '4B', 4);

INSERT INTO spaces (space_type, name, area, capacity, attached, parent_space_id)
VALUES ('garage', 'Parking Spot 12', 12, 1, false, (SELECT id FROM spaces WHERE name = 'Downtown Loft'));

INSERT INTO devices (name, brand, status, space_id)
VALUES ('Smart TV', 'LG', 'working', (SELECT id FROM spaces WHERE name = 'Living Room'));

INSERT INTO plants (name, species, watering_frequency_days, space_id)
VALUES ('Boston Fern', 'Nephrolepis exaltata', 3, (SELECT id FROM spaces WHERE name = 'Backyard'));

INSERT INTO vehicles (name, type, make, model, year, space_id)
VALUES ('Honda Civic', 'car', 'Honda', 'Civic', 2020, (SELECT id FROM spaces WHERE name = 'Two-Car Garage'));
