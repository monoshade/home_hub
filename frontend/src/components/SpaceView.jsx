import { useState } from 'react'
import Tabs from './Tabs'
import EntityCard from './EntityCard'
import FieldList from './FieldList'
import { SPACE_TYPES, SPACE_ICONS, CATEGORY_ICONS } from '../config'

const PROP_ICONS = { house: '🏡', apartment: '🏢' }

const PROP_HIDDEN = ['id', 'space_type', 'name', 'spaces', 'items', 'parent_space_id', 'created_at']
const SPACE_HIDDEN = ['id', 'space_type', 'name', 'items', 'parent_space_id', 'created_at']

// Browse properties, drill into their spaces, and see the items located there.
export default function SpaceView({ data }) {
  const { properties } = data
  const [selectedId, setSelectedId] = useState(properties[0]?.id)
  const property = properties.find((p) => p.id === selectedId) ?? properties[0]

  if (!property) return <p className="empty">No properties yet.</p>

  return (
    <div className="space-view">
      <aside className="prop-list">
        <h3 className="sidebar-title">Properties</h3>
        {properties.map((p) => (
          <button
            key={p.id}
            className={`prop-item ${p.id === property.id ? 'prop-item--active' : ''}`}
            onClick={() => setSelectedId(p.id)}
          >
            <span className="prop-item-top">
              <span className="prop-name">
                <span className="prop-icon" aria-hidden="true">{PROP_ICONS[p.space_type] ?? '🏠'}</span>
                {p.name}
              </span>
              <span className={`badge badge--${p.space_type}`}>{p.space_type}</span>
            </span>
            <span className="prop-addr">{p.address}</span>
          </button>
        ))}
      </aside>

      <section className="prop-detail">
        <div className="prop-header">
          <h2>
            {property.name}{' '}
            <span className={`badge badge--${property.space_type}`}>{property.space_type}</span>
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

  if (!tabs.length) return <p className="empty">No spaces recorded for this property.</p>

  return <Tabs items={tabs} className="sub-tabs" />
}

function SpaceGrid({ spaces }) {
  return (
    <div className="grid">
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
            <div className="space-items">
              {located.length ? (
                located.map((item) => (
                  <span className="item-chip" key={`${item.category}-${item.id}`}>
                    <span className="chip-icon" aria-hidden="true">{CATEGORY_ICONS[item.category] ?? '•'}</span>
                    {item.name}
                  </span>
                ))
              ) : (
                <p className="muted">No items located here.</p>
              )}
            </div>
          </EntityCard>
        )
      })}
    </div>
  )
}
