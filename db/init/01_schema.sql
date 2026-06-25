-- Runs automatically the first time the database initializes
-- (when ./db/data is empty). Add your schema here.

CREATE TABLE IF NOT EXISTS items (
    id          BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name        TEXT NOT NULL,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Seed row so the API returns something on first boot.
INSERT INTO items (name) VALUES ('hello from postgres');
