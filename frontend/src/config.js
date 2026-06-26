// Display config. Keys match the API's discriminator values:
//   item.category   (the resource path, e.g. "sport-equipments")
//   space.space_type
export const CATEGORIES = [
  { key: 'devices', label: 'Devices', icon: '📺' },
  { key: 'furniture', label: 'Furniture', icon: '🛋️' },
  { key: 'instruments', label: 'Instruments', icon: '🎸' },
  { key: 'sport-equipments', label: 'Sport Equipment', icon: '🏀' },
  { key: 'plants', label: 'Plants', icon: '🪴' },
  { key: 'vehicles', label: 'Vehicles', icon: '🚗' },
]

export const SPACE_TYPES = [
  { key: 'room', label: 'Rooms', icon: '🛏️' },
  { key: 'yard', label: 'Yards', icon: '🌳' },
  { key: 'garage', label: 'Garages', icon: '🚙' },
  { key: 'deck', label: 'Decks', icon: '🪑' },
]

// Lookup helpers so any component can resolve an icon from a discriminator.
export const CATEGORY_ICONS = Object.fromEntries(CATEGORIES.map((c) => [c.key, c.icon]))
export const SPACE_ICONS = Object.fromEntries(SPACE_TYPES.map((s) => [s.key, s.icon]))
