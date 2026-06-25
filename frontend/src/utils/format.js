// Turn a property key (camelCase or snake_case) into a human label.
// e.g. "purchaseDate" -> "Purchase Date", "floor_level" -> "Floor Level"
export function labelize(key) {
  return String(key)
    .replace(/_/g, ' ')
    .replace(/([a-z0-9])([A-Z])/g, '$1 $2')
    .replace(/\b\w/g, (c) => c.toUpperCase())
}

// Render a scalar value for display.
export function displayValue(value) {
  if (typeof value === 'boolean') return value ? 'Yes' : 'No'
  return String(value)
}

// Entries of an object worth displaying: skips hidden keys and empty values.
export function visibleEntries(obj, hidden = []) {
  return Object.entries(obj).filter(
    ([key, value]) =>
      !hidden.includes(key) &&
      value !== null &&
      value !== undefined &&
      value !== '' &&
      !(Array.isArray(value) && value.length === 0),
  )
}
