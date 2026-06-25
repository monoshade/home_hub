const BASE_URL = import.meta.env.VITE_API_URL ?? 'http://localhost:8080'

async function request(path) {
  const res = await fetch(`${BASE_URL}${path}`)
  if (!res.ok) {
    throw new Error(`Request failed: ${res.status}`)
  }
  return res.json()
}

export const api = {
  health: () => request('/api/health'),
  items: () => request('/api/items'),
}
