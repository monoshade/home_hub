// Display config. Keys match the API's discriminator values:
//   item.category   (the resource path, e.g. "sport-equipments")
//   space.space_type
export const CATEGORIES = [
  { key: 'devices', label: 'Devices' },
  { key: 'furniture', label: 'Furniture' },
  { key: 'instruments', label: 'Instruments' },
  { key: 'sport-equipments', label: 'Sport Equipment' },
  { key: 'plants', label: 'Plants' },
  { key: 'vehicles', label: 'Vehicles' },
]

export const SPACE_TYPES = [
  { key: 'room', label: 'Rooms' },
  { key: 'yard', label: 'Yards' },
  { key: 'garage', label: 'Garages' },
  { key: 'deck', label: 'Decks' },
]
