-- Table schema (structure only — seed data lives in seeds/).
--
-- Runs automatically the first time the database initializes (when ./db/data
-- is empty). Auto-applied to the default database (POSTGRES_DB = demo); also
-- applied to the `prod` and `test` databases by 03_create_extra_dbs.sh.
--
-- Spaces use single-table inheritance: every space (house, apartment, room,
-- yard, garage, deck) is one row in `spaces`, discriminated by space_type.
-- Containment (a house has rooms, an apartment has a garage, ...) is modeled
-- with the self-referencing parent_space_id.
--
-- Items use table-per-concrete-class. Each item carries a single space_id
-- foreign key, so an item belongs to at most one location.
--
-- Column names are the snake_case form of the entity properties, matching
-- EntityRowMapper's convention.

-- ---------------------------------------------------------------------------
-- Demo table (no entity) — backs the placeholder /api/items route.
-- Remove once that route points at a real entity.
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS items (
    id          BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name        TEXT NOT NULL,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- ===========================================================================
-- Spaces (single table for the whole Space hierarchy)
-- ===========================================================================
CREATE TABLE spaces (
    id              BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    space_type      TEXT NOT NULL
                    CHECK (space_type IN ('house', 'apartment', 'room', 'yard', 'garage', 'deck')),
    name            TEXT NOT NULL,

    -- Containment: a child space points at its parent space. Deleting a
    -- parent (e.g. a house) cascades to all its contained spaces.
    parent_space_id BIGINT REFERENCES spaces(id) ON DELETE CASCADE,

    -- Common (Space)
    area            NUMERIC,
    description     TEXT,

    -- Property (house / apartment)
    address         TEXT,
    lot_size        NUMERIC,
    property_type   TEXT,           -- residential, commercial, land, ...

    -- House
    floors          INTEGER,
    year_built      INTEGER,

    -- Apartment / Room
    unit_number     TEXT,           -- apartment
    floor_level     INTEGER,        -- apartment + room

    -- Room
    type            TEXT,           -- bedroom, kitchen, bathroom, ...

    -- Yard
    surface_type    TEXT,           -- grass, gravel, concrete, ...
    fenced          BOOLEAN,

    -- Garage
    capacity        INTEGER,        -- number of vehicles
    attached        BOOLEAN,

    -- Deck
    material        TEXT,           -- wood, composite, concrete, ...
    covered         BOOLEAN,

    created_at      TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX idx_spaces_parent ON spaces(parent_space_id);
CREATE INDEX idx_spaces_type   ON spaces(space_type);

-- An apartment has at most one garage. A single-column FK can't express this
-- cross-row rule, so enforce it with a trigger. (Houses may have many garages.)
CREATE OR REPLACE FUNCTION enforce_apartment_single_garage() RETURNS trigger AS $$
BEGIN
    IF NEW.space_type = 'garage' AND NEW.parent_space_id IS NOT NULL
       AND (SELECT space_type FROM spaces WHERE id = NEW.parent_space_id) = 'apartment'
       AND EXISTS (
           SELECT 1 FROM spaces
           WHERE parent_space_id = NEW.parent_space_id
             AND space_type = 'garage'
             AND id <> NEW.id
       )
    THEN
        RAISE EXCEPTION 'Apartment % already has a garage', NEW.parent_space_id;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_apartment_single_garage
    BEFORE INSERT OR UPDATE ON spaces
    FOR EACH ROW EXECUTE FUNCTION enforce_apartment_single_garage();

-- ===========================================================================
-- Items (belongings) — each located in at most one space via space_id.
-- ON DELETE SET NULL: removing a space leaves the item, just unplaced.
-- ===========================================================================

CREATE TABLE devices (
    id            BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name          TEXT NOT NULL,
    brand         TEXT,
    model         TEXT,
    status        TEXT,           -- working, broken, retired, ...
    purchase_date DATE,
    space_id      BIGINT REFERENCES spaces(id) ON DELETE SET NULL,
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE furniture (
    id            BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name          TEXT NOT NULL,
    material      TEXT,
    dimensions    TEXT,
    purchase_date DATE,
    space_id      BIGINT REFERENCES spaces(id) ON DELETE SET NULL,
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE instruments (
    id            BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name          TEXT NOT NULL,
    type          TEXT,           -- string, percussion, wind, ...
    brand         TEXT,
    purchase_date DATE,
    space_id      BIGINT REFERENCES spaces(id) ON DELETE SET NULL,
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE sport_equipments (
    id            BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name          TEXT NOT NULL,
    sport         TEXT,
    condition     TEXT,           -- new, used, worn, ...
    purchase_date DATE,
    space_id      BIGINT REFERENCES spaces(id) ON DELETE SET NULL,
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE plants (
    id                       BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name                     TEXT NOT NULL,
    species                  TEXT,
    watering_frequency_days  INTEGER,
    last_watered             DATE,
    space_id                 BIGINT REFERENCES spaces(id) ON DELETE SET NULL,
    created_at               TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE vehicles (
    id            BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name          TEXT NOT NULL,
    type          TEXT,           -- car, motorcycle, bicycle, ...
    make          TEXT,
    model         TEXT,
    year          INTEGER,
    license_plate TEXT,
    space_id      BIGINT REFERENCES spaces(id) ON DELETE SET NULL,
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX idx_devices_space    ON devices(space_id);
CREATE INDEX idx_furniture_space  ON furniture(space_id);
CREATE INDEX idx_instruments_space ON instruments(space_id);
CREATE INDEX idx_sport_space      ON sport_equipments(space_id);
CREATE INDEX idx_plants_space     ON plants(space_id);
CREATE INDEX idx_vehicles_space   ON vehicles(space_id);
