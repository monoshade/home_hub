// Runtime context for the frontend, supplied at startup via Vite env vars
// (set in docker-compose.yml / .env), mirroring the backend's App\Context:
//   VITE_APP_DB  -> 'prod' | 'demo' | 'test'   (default: 'demo')
//   VITE_APP_ENV -> 'prod' | 'demo'            (default: 'demo')
// Unknown values fall back to the safe demo defaults.
const DB_VALUES = ['prod', 'demo', 'test']
const ENV_VALUES = ['prod', 'demo']

const pick = (value, allowed, fallback) =>
  allowed.includes(value) ? value : fallback

export const context = {
  db: pick(import.meta.env.VITE_APP_DB, DB_VALUES, 'demo'),
  environment: pick(import.meta.env.VITE_APP_ENV, ENV_VALUES, 'demo'),
}

export const isProd = context.environment === 'prod'
