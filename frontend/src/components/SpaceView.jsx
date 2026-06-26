import { useState } from 'react'
import Tabs from './Tabs'
import EntityCard from './EntityCard'
import FieldList from './FieldList'
import { SPACE_TYPES, SPACE_ICONS, CATEGORY_ICONS } from '../config'
import styles from './SpaceView.module.css'
import shared from '../styles/shared.module.css'

const PROP_ICONS = { house: '🏡', apartment: '🏢' }

const PROP_HIDDEN = ['id', 'space_type', 'name', 'spaces', 'items', 'parent_space_id', 'created_at']
const SPACE_HIDDEN = ['id', 'space_type', 'name', 'items', 'parent_space_id', 'created_at']

// Browse properties, drill into their spaces, and see the items located there.
export default function SpaceView({ data }) {
  const { properties } = data
  const [selectedId, setSelectedId] = useState(properties[0]?.id)
  const property = properties.find((p) => p.id === selectedId) ?? properties[0]

  if (!property) return <p className={shared.empty}>No properties yet.</p>

  return (
    <div className={styles.spaceView}>
      <aside className={styles.propList}>
        <h3 className={styles.sidebarTitle}>Properties</h3>
        {properties.map((p) => (
          <button
            key={p.id}
            className={`${styles.propItem} ${p.id === property.id ? styles.propItemActive : ''}`}
            onClick={() => setSelectedId(p.id)}
          >
            <span className={styles.propItemTop}>
              <span className={styles.propName}>
                <span className={styles.propIcon} aria-hidden="true">{PROP_ICONS[p.space_type] ?? '🏠'}</span>
                {p.name}
              </span>
              <span className={`${shared.badge} ${shared[`badge--${p.space_type}`]}`}>{p.space_type}</span>
            </span>
            <span className={styles.propAddr}>{p.address}</span>
          </button>
        ))}
      </aside>

      <section className={styles.propDetail}>
        <div className={styles.propHeader}>
          <h2>
            {property.name}{' '}
            <span className={`${shared.badge} ${shared[`badge--${property.space_type}`]}`}>
              {property.space_type}
            </span>
          </h2>
          <FieldList obj={property} hidden={PROP_HIDDEN} />
        </div>
        {/* key forces the sub-tabs to reset when switching property */}
        <SpaceTabs key={property.id} property={property} />
      </section>
    </div>
  )
}

function SpaceTabs({ property }) {
  const spaces = property.spaces ?? []
  const tabs = SPACE_TYPES.map((type) => ({
    ...type,
    spaces: spaces.filter((s) => s.space_type === type.key),
  }))
    .filter((type) => type.spaces.length > 0)
    .map((type) => ({
      id: type.key,
      label: `${type.icon} ${type.label} (${type.spaces.length})`,
      render: () => <SpaceGrid spaces={type.spaces} />,
    }))

  if (!tabs.length) return <p className={shared.empty}>No spaces recorded for this property.</p>

  return <Tabs items={tabs} variant="underline" />
}

function SpaceGrid({ spaces }) {
  return (
    <div className={shared.grid}>
      {spaces.map((space) => {
        const located = space.items ?? []
        return (
          <EntityCard
            key={space.id}
            title={space.name}
            icon={SPACE_ICONS[space.space_type]}
            badge={`${located.length} item${located.length !== 1 ? 's' : ''}`}
            obj={space}
            hidden={SPACE_HIDDEN}
          >
            <div className={styles.spaceItems}>
              {located.length ? (
                located.map((item) => (
                  <span className={styles.itemChip} key={`${item.category}-${item.id}`}>
                    <span className={styles.chipIcon} aria-hidden="true">{CATEGORY_ICONS[item.category] ?? '•'}</span>
                    {item.name}
                  </span>
                ))
              ) : (
                <p className={shared.muted}>No items located here.</p>
              )}
            </div>
          </EntityCard>
        )
      })}
    </div>
  )
}
