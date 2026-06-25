# Home Hub

A three-tier skeleton:

- **PostgreSQL** — storage, data persisted to a local file directory (`db/data/`)
- **PHP 8.3** — backend API
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
│   ├── Dockerfile
│   ├── composer.json
│   ├── public/index.php    # front controller + minimal router
│   └── src/Database.php    # PDO connection factory
└── frontend/               # React app
    ├── Dockerfile
    ├── package.json
    └── src/
        ├── main.jsx
        ├── App.jsx
        └── api.js          # backend client
```

## Run

```bash
cp .env.example .env
docker compose up --build
```

Then:

- Frontend: http://localhost:5173
- Backend health: http://localhost:8080/api/health
- Backend items: http://localhost:8080/api/items
- PostgreSQL: localhost:5432

## Notes

- Database data lives in `db/data/` on the host (bind mount). Delete that
  directory to reset the database; `db/init/*.sql` re-runs on a fresh start.
- CORS is wide open in `backend/public/index.php` for local dev — tighten it
  before deploying.
