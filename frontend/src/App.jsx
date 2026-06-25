import { useEffect, useState } from 'react'
import { api } from './api'

export default function App() {
  const [items, setItems] = useState([])
  const [error, setError] = useState(null)

  useEffect(() => {
    api.items().then(setItems).catch((e) => setError(e.message))
  }, [])

  return (
    <main style={{ fontFamily: 'system-ui, sans-serif', padding: '2rem' }}>
      <h1>Home Hub</h1>
      <p>React + PHP + PostgreSQL skeleton.</p>

      {error && <p style={{ color: 'crimson' }}>Error: {error}</p>}

      <h2>Items</h2>
      <ul>
        {items.map((item) => (
          <li key={item.id}>{item.name}</li>
        ))}
      </ul>
    </main>
  )
}
