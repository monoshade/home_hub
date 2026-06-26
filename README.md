# Home Hub

A three-tier app for tracking your spaces (properties, rooms, ...) and the
belongings located in them.

- **PostgreSQL** — storage, data persisted to a local file directory (`db/data/`)
- **PHP 8.3** — backend REST API
- **React (Vite)** — frontend

## Layout

```
home_hub/
├── docker-compose.yml      # full stack: includes the three files below
├── compose.db.yml          # db service      (independently startable)
├── compose.backend.yml     # backend service (independently startable)
├── compose.frontend.yml    # frontend service (independently startable)
├── .env.example            # copy to .env
├── db/
│   ├── init/               # run once on first DB init
│   │   ├── 01_schema.sql           # table schema (demo + prod)
│   │   ├── 02_seed.sql             # demo fixture data (demo only)
│   │   └── 03_create_prod_db.sh    # creates the prod database (schema only)
│   └── data/               # Postgres data files (gitignored)
├── backend/                # PHP API
│   ├── public/index.php    # front controller (CORS, dispatch)
│   └── src/
│       ├── Context.php      # runtime context (Db + Environment enums)
│       ├── Database.php     # PDO connection factory (db chosen by Context)
│       ├── routes.php       # composition root: wires per-entity controllers + views
│       ├── Http/            # Router, Request, Response, HttpException
│       │   └── Controllers/ # ResourceController base + one controller per entity
│       ├── Repository/      # data-access helper (raw rows; no formatting)
│       └── Entities/        # Items/ and Spaces/ — each defines its formatted toArray()
└── frontend/               # React app
    └── src/
        ├── App.jsx          # root tabs: Space View / Full Device View
        ├── api.js           # backend client
        ├── context.js       # runtime context (db + environment)
        ├── hooks/           # useHomeData (fetches the API)
        └── components/      # views + reusable UI
```

## Run

```bash
cp .env.example .env
docker compose up --build
```

### Services (start together or separately)

Each tier is its own compose file and can be initiated on its own. The backend
talks to Postgres over a published host port and the browser reaches the backend
the same way, so no service depends on another being in the same Docker network —
any tier can run standalone or as several independent instances.

```bash
# everything together (root file just includes the three below + adds ordering)
docker compose up --build

# each service on its own
docker compose -f compose.db.yml up
docker compose -f compose.backend.yml up
docker compose -f compose.frontend.yml up
```

Run multiple instances by giving each its own project name (`-p`), ports, and
context. For example, a demo stack and a prod stack side by side:

```bash
# demo backend + frontend (defaults: ports 8080 / 5173)
docker compose -p home_hub_demo up backend frontend

# a second, prod-context backend + frontend on different ports
APP_DB=prod APP_ENV=prod \
  BACKEND_PORT=8081 FRONTEND_PORT=5174 VITE_API_URL=http://localhost:8081 \
  docker compose -p home_hub_prod up backend frontend
```

### Runtime context

Each component boots with its own context, passed in at startup via env vars
(when run together they default to the same values):

| Var | Values | Meaning |
|-----|--------|---------|
| `APP_DB` | `prod` \| `demo` \| `test` | which database to connect to |
| `APP_ENV` | `prod` \| `demo` | which environment to run as |

The backend resolves these into `App\Context` (`backend/src/Context.php`, with the
`App\Db` and `App\Environment` enums) at its entry point (`backend/public/index.php`):
`APP_DB` selects the database name in `Database::connection()`, and `APP_ENV` gates
behaviour such as CORS. The frontend mirrors them as `VITE_APP_DB` / `VITE_APP_ENV`
(`frontend/src/context.js`), read at its entry point (`frontend/src/main.jsx`).

Two databases are created on first init:

- **`demo`** (`POSTGRES_DB`) — table schema **+** demo fixture data. The default
  `demo` db context is wired to it (`DB_NAME_DEMO`).
- **`prod`** (`POSTGRES_PROD_DB`) — table schema only, no fixtures. The `prod` db
  context is wired to it (`DB_NAME_PROD`).

Each `APP_DB` case maps to a database name via `DB_NAME_{PROD,DEMO,TEST}`, falling
back to the shared `DB_NAME`. Set `DB_NAME_TEST` to point the `test` context at its
own database.

```bash
# default (demo db + demo environment)
docker compose up --build

# prod profile: prod environment against the prod database
APP_DB=prod APP_ENV=prod docker compose up --build
```

Then:

- Frontend: http://localhost:5173
- Backend health: http://localhost:8080/api/health
- PostgreSQL: localhost:5432

## API

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/api/health` | liveness check (includes the runtime `db` + `environment`) |
| GET | `/api/context` | runtime context the backend booted with |
| GET | `/api/items` | all belongings, each tagged with `category` |
| GET | `/api/properties` | houses/apartments with nested spaces + located items |
| GET | `/api/spaces?type=&parent=` | spaces, optionally filtered |
| GET/POST/PUT/DELETE | `/api/spaces[/{id}]` | space CRUD (`space_type` in body) |
| GET/POST/PUT/DELETE | `/api/devices[/{id}]` | device CRUD |
| GET/POST/PUT/DELETE | `/api/furniture[/{id}]` | furniture CRUD |
| GET/POST/PUT/DELETE | `/api/instruments[/{id}]` | instrument CRUD |
| GET/POST/PUT/DELETE | `/api/sport-equipments[/{id}]` | sport equipment CRUD |
| GET/POST/PUT/DELETE | `/api/plants[/{id}]` | plant CRUD |
| GET/POST/PUT/DELETE | `/api/vehicles[/{id}]` | vehicle CRUD |

Each item resource has its own controller (`Http/Controllers/Items/`); the
`category` field in responses is intrinsic to each entity. Spaces share one
controller (`Http/Controllers/Spaces/`) that picks the entity by `space_type`.

## Data model

- **Spaces** (house, apartment, room, yard, garage, deck) live in a single
  `spaces` table, discriminated by `space_type`; containment is the
  self-referencing `parent_space_id`.
- **Items** use one table per category, each with a single `space_id` foreign
  key — so every item belongs to at most one location.

## Notes

- **Resetting / re-seeding the DB:** `db/init/*` only runs when `db/data/`
  is empty. After changing the schema (or adding a database), reset with:
  ```bash
  docker compose down && rm -rf db/data && docker compose up --build
  ```
- CORS is wide open in `backend/public/index.php` for local dev — tighten it
  before deploying.
