# Home Hub

A three-tier app for tracking your spaces (properties, rooms, ...) and the
belongings located in them.

- **PostgreSQL** — storage, data persisted to a local file directory (`db/data/`)
- **PHP 8.3** — backend REST API
- **React (Vite)** — frontend

## Layout

```
home_hub/
├── docker-compose.yml      # orchestrates db + backend + frontend
├── .env.example            # copy to .env
├── db/
│   ├── init/               # SQL run once on first DB init
│   │   └── 01_schema.sql
│   └── data/               # Postgres data files (gitignored)
├── backend/                # PHP API
│   ├── public/index.php    # front controller (CORS, dispatch)
│   └── src/
│       ├── Database.php     # PDO connection factory
│       ├── routes.php       # route + repository wiring
│       ├── Http/            # Router, Request, Response, HttpException
│       ├── Repository/      # generic CRUD repository
│       └── Entities/        # Items/ and Spaces/ domain models
└── frontend/               # React app
    └── src/
        ├── App.jsx          # root tabs: Space View / Full Device View
        ├── api.js           # backend client
        ├── hooks/           # useHomeData (fetches the API)
        └── components/      # views + reusable UI
```

## Run

```bash
cp .env.example .env
docker compose up --build
```

Then:

- Frontend: http://localhost:5173
- Backend health: http://localhost:8080/api/health
- PostgreSQL: localhost:5432

## API

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/api/health` | liveness check |
| GET | `/api/items` | all belongings, each tagged with `category` |
| GET | `/api/properties` | houses/apartments with nested spaces + located items |
| GET | `/api/spaces?type=&parent=` | spaces, optionally filtered |
| GET/POST/PUT/DELETE | `/api/spaces[/{id}]` | space CRUD (`space_type` in body) |
| GET/POST/PUT/DELETE | `/api/{category}[/{id}]` | item CRUD per category |

Item categories: `devices`, `furniture`, `instruments`, `sport-equipments`,
`plants`, `vehicles`.

## Data model

- **Spaces** (house, apartment, room, yard, garage, deck) live in a single
  `spaces` table, discriminated by `space_type`; containment is the
  self-referencing `parent_space_id`.
- **Items** use one table per category, each with a single `space_id` foreign
  key — so every item belongs to at most one location.

## Notes

- **Resetting / re-seeding the DB:** `db/init/*.sql` only runs when `db/data/`
  is empty. After changing the schema, reset with:
  ```bash
  docker compose down && rm -rf db/data && docker compose up --build
  ```
- CORS is wide open in `backend/public/index.php` for local dev — tighten it
  before deploying.
