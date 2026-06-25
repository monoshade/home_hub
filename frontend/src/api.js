const BASE_URL = import.meta.env.VITE_API_URL ?? 'http://localhost:8080'

async function request(path, options) {
  const res = await fetch(`${BASE_URL}${path}`, options)
  if (!res.ok) {
    throw new Error(`Request failed: ${res.status}`)
  }
  return res.status === 204 ? null : res.json()
}

const json = (method, data) => ({
  method,
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify(data),
})

// CRUD helpers for a resource, e.g. resource('devices').list()
const resource = (name) => ({
  list: () => request(`/api/${name}`),
  get: (id) => request(`/api/${name}/${id}`),
  create: (data) => request(`/api/${name}`, json('POST', data)),
  update: (id, data) => request(`/api/${name}/${id}`, json('PUT', data)),
  remove: (id) => request(`/api/${name}/${id}`, { method: 'DELETE' }),
})

export const api = {
  health: () => request('/api/health'),
  items: () => request('/api/items'), // all belongings, aggregated
  properties: () => request('/api/properties'), // nested spaces + items
  spaces: (params = '') => request(`/api/spaces${params}`),
  resource,
}
