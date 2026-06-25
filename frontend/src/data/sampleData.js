// -----------------------------------------------------------------------------
// Sample data layer.
//
// The data model now links each item to a single space via a `space_id` foreign
// key (see db/init/01_schema.sql and ItemBase::$spaceId), which the `spaceId`
// on each mock item below mirrors. The backend does not yet expose entity routes
// (only the demo /api/items), so this file still supplies the data.
//
// To go live later: replace the exports below with async fetches against the
// backend (e.g. GET /api/spaces, GET /api/items). Components only import
// `properties`, `items`, and `spaceIndex`, so this file is the single swap point.
// -----------------------------------------------------------------------------

// Item categories (belongings) — keys match item.category.
export const CATEGORIES = [
  { key: 'device', label: 'Devices' },
  { key: 'furniture', label: 'Furniture' },
  { key: 'instrument', label: 'Instruments' },
  { key: 'sport', label: 'Sport Equipment' },
  { key: 'plant', label: 'Plants' },
  { key: 'vehicle', label: 'Vehicles' },
]

// Space types within a property — keys match space.spaceType.
export const SPACE_TYPES = [
  { key: 'room', label: 'Rooms' },
  { key: 'yard', label: 'Yards' },
  { key: 'garage', label: 'Garages' },
  { key: 'deck', label: 'Decks' },
]

// Properties (House / Apartment) with their nested spaces.
export const properties = [
  {
    id: 'h1',
    kind: 'house',
    name: 'Maple House',
    address: '742 Maple Ave',
    propertyType: 'residential',
    area: 220,
    floors: 2,
    yearBuilt: 2008,
    spaces: [
      { id: 's-living', spaceType: 'room', name: 'Living Room', type: 'living', floorLevel: 1, area: 35 },
      { id: 's-master', spaceType: 'room', name: 'Master Bedroom', type: 'bedroom', floorLevel: 2, area: 22 },
      { id: 's-kitchen', spaceType: 'room', name: 'Kitchen', type: 'kitchen', floorLevel: 1, area: 18 },
      { id: 's-backyard', spaceType: 'yard', name: 'Backyard', surfaceType: 'grass', fenced: true, area: 120 },
      { id: 's-garage1', spaceType: 'garage', name: 'Two-Car Garage', capacity: 2, attached: true, area: 36 },
      { id: 's-deck', spaceType: 'deck', name: 'Back Deck', material: 'wood', covered: false, area: 20 },
    ],
  },
  {
    id: 'a1',
    kind: 'apartment',
    name: 'Downtown Loft',
    address: '55 Center St',
    propertyType: 'residential',
    area: 75,
    unitNumber: '4B',
    floorLevel: 4,
    spaces: [
      { id: 's-studio', spaceType: 'room', name: 'Studio Room', type: 'studio', floorLevel: 4, area: 40 },
      { id: 's-bath', spaceType: 'room', name: 'Bathroom', type: 'bathroom', floorLevel: 4, area: 6 },
      { id: 's-spot12', spaceType: 'garage', name: 'Parking Spot 12', capacity: 1, attached: false, area: 12 },
    ],
  },
]

// Items (belongings). `spaceId` locates each item within a space (mock-only).
export const items = [
  // Devices
  { id: 'd1', category: 'device', name: 'Smart TV', brand: 'LG', model: 'OLED55', status: 'working', spaceId: 's-living' },
  { id: 'd2', category: 'device', name: 'Wi-Fi Router', brand: 'Asus', status: 'working', spaceId: 's-living' },
  { id: 'd3', category: 'device', name: 'Refrigerator', brand: 'Samsung', status: 'working', spaceId: 's-kitchen' },
  { id: 'd4', category: 'device', name: 'Robot Vacuum', brand: 'iRobot', status: 'broken', spaceId: 's-studio' },

  // Furniture
  { id: 'f1', category: 'furniture', name: 'Sectional Sofa', material: 'fabric', spaceId: 's-living' },
  { id: 'f2', category: 'furniture', name: 'King Bed', material: 'wood', spaceId: 's-master' },
  { id: 'f3', category: 'furniture', name: 'Dining Table', material: 'oak', spaceId: 's-kitchen' },
  { id: 'f4', category: 'furniture', name: 'Standing Desk', material: 'composite', spaceId: 's-studio' },

  // Instruments
  { id: 'i1', category: 'instrument', name: 'Acoustic Guitar', type: 'string', brand: 'Yamaha', spaceId: 's-living' },
  { id: 'i2', category: 'instrument', name: 'Digital Piano', type: 'keyboard', brand: 'Roland', spaceId: 's-living' },

  // Sport equipment
  { id: 'sp1', category: 'sport', name: 'Treadmill', sport: 'running', condition: 'used', spaceId: 's-garage1' },
  { id: 'sp2', category: 'sport', name: 'Mountain Bike', sport: 'cycling', condition: 'new', spaceId: 's-garage1' },
  { id: 'sp3', category: 'sport', name: 'Yoga Mat', sport: 'yoga', condition: 'new', spaceId: 's-studio' },

  // Plants
  { id: 'p1', category: 'plant', name: 'Monstera', species: 'Monstera deliciosa', wateringFrequencyDays: 7, spaceId: 's-living' },
  { id: 'p2', category: 'plant', name: 'Boston Fern', species: 'Nephrolepis exaltata', wateringFrequencyDays: 3, spaceId: 's-backyard' },
  { id: 'p3', category: 'plant', name: 'Snake Plant', species: 'Sansevieria', wateringFrequencyDays: 14, spaceId: 's-studio' },

  // Vehicles
  { id: 'v1', category: 'vehicle', name: 'Honda Civic', type: 'car', make: 'Honda', model: 'Civic', year: 2020, licensePlate: 'ABC-123', spaceId: 's-garage1' },
  { id: 'v2', category: 'vehicle', name: 'City Scooter', type: 'scooter', make: 'Vespa', year: 2021, spaceId: 's-spot12' },
]

// Lookup: spaceId -> { space, property }. Used to label an item's location.
export const spaceIndex = {}
for (const property of properties) {
  for (const space of property.spaces) {
    spaceIndex[space.id] = { space, property }
  }
}

export function itemsInSpace(spaceId) {
  return items.filter((item) => item.spaceId === spaceId)
}
