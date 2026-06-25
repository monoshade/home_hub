import { useState } from 'react'
import Tabs from './Tabs'
import EntityCard from './EntityCard'
import FieldList from './FieldList'
import { properties, SPACE_TYPES, itemsInSpace } from '../data/sampleData'

// Browse properties, drill into their spaces, and see the items located there.
export default function SpaceView() {
  const [selectedId, setSelectedId] = useState(properties[0]?.id)
  const property = properties.find((p) => p.id === selectedId) ?? properties[0]

  if (!property) return <p className="empty">No properties recorded.</p>

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
              <span className="prop-name">{p.name}</span>
              <span className={`badge badge--${p.kind}`}>{p.kind}</span>
            </span>
            <span className="prop-addr">{p.address}</span>
          </button>
        ))}
      </aside>

      <section className="prop-detail">
        <div className="prop-header">
          <h2>
            {property.name} <span className={`badge badge--${property.kind}`}>{property.kind}</span>
          </h2>
          <FieldList obj={property} hidden={['id', 'kind', 'name', 'spaces']} />
        </div>
        {/* key forces the sub-tabs to reset when switching property */}
        <SpaceTabs key={property.id} property={property} />
      </section>
    </div>
  )
}

function SpaceTabs({ property }) {
  const tabs = SPACE_TYPES.map((type) => ({
    ...type,
    spaces: property.spaces.filter((s) => s.spaceType === type.key),
  }))
    .filter((type) => type.spaces.length > 0)
    .map((type) => ({
      id: type.key,
      label: `${type.label} (${type.spaces.length})`,
      render: () => <SpaceGrid spaces={type.spaces} />,
    }))

  if (!tabs.length) return <p className="empty">No spaces recorded for this property.</p>

  return <Tabs items={tabs} className="sub-tabs" />
}

function SpaceGrid({ spaces }) {
  return (
    <div className="grid">
      {spaces.map((space) => {
        const located = itemsInSpace(space.id)
        return (
          <EntityCard
            key={space.id}
            title={space.name}
            badge={`${located.length} item${located.length !== 1 ? 's' : ''}`}
            obj={space}
            hidden={['id', 'spaceType', 'name']}
          >
            <div className="space-items">
              {located.length ? (
                located.map((item) => (
                  <span className="item-chip" key={item.id}>
                    <span className={`dot dot--${item.category}`} />
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
