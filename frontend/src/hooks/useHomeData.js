import { useEffect, useState } from 'react'
import { api } from '../api'

// Loads everything the UI needs from the API:
//   - properties: houses/apartments with nested spaces + located items
//   - items: flat list of all belongings (each tagged with category)
//   - spaceIndex: space_id -> { space, property } for location lookups
export function useHomeData() {
  const [state, setState] = useState({
    loading: true,
    error: null,
    properties: [],
    items: [],
    spaceIndex: {},
  })

  useEffect(() => {
    let active = true

    Promise.all([api.properties(), api.items()])
      .then(([properties, items]) => {
        if (!active) return
        const spaceIndex = {}
        for (const property of properties) {
          for (const space of property.spaces ?? []) {
            spaceIndex[space.id] = { space, property }
          }
        }
        setState({ loading: false, error: null, properties, items, spaceIndex })
      })
      .catch((e) => {
        if (active) setState((s) => ({ ...s, loading: false, error: e.message }))
      })

    return () => {
      active = false
    }
  }, [])

  return state
}
