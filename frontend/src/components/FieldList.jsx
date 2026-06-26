import { labelize, displayValue, visibleEntries } from '../utils/format'
import styles from './FieldList.module.css'

// Renders an entity's scalar fields as a definition list.
export default function FieldList({ obj, hidden = [] }) {
  const rows = visibleEntries(obj, hidden)
  if (!rows.length) return null

  return (
    <dl className={styles.fields}>
      {rows.map(([key, value]) => (
        <div className={styles.field} key={key}>
          <dt>{labelize(key)}</dt>
          <dd>{displayValue(value)}</dd>
        </div>
      ))}
    </dl>
  )
}
