import { labelize, displayValue, visibleEntries } from '../utils/format'

// Renders an entity's scalar fields as a definition list.
export default function FieldList({ obj, hidden = [] }) {
  const rows = visibleEntries(obj, hidden)
  if (!rows.length) return null

  return (
    <dl className="fields">
      {rows.map(([key, value]) => (
        <div className="field" key={key}>
          <dt>{labelize(key)}</dt>
          <dd>{displayValue(value)}</dd>
        </div>
      ))}
    </dl>
  )
}
