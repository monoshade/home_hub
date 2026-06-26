import Tabs from './Tabs'
import EntityCard from './EntityCard'
import { CATEGORIES, CATEGORY_ICONS } from '../config'

const HIDDEN = ['id', 'category', 'space_id', 'name', 'created_at']

// Full inventory of belongings, browsable by category via sub-tabs.
export default function DeviceView({ data }) {
  const { items, spaceIndex } = data

  const tabs = CATEGORIES.map((category) => {
    const list = items.filter((item) => item.category === category.key)
    return {
      id: category.key,
      label: `${category.icon} ${category.label} (${list.length})`,
      render: () => <ItemGrid items={list} spaceIndex={spaceIndex} />,
    }
  })

  return (
    <div className="view">
      <p className="view-hint">Every belonging across all spaces — browse by category.</p>
      <Tabs items={tabs} className="sub-tabs" />
    </div>
  )
}

function ItemGrid({ items, spaceIndex }) {
  if (!items.length) return <p className="empty">No items in this category.</p>

  return (
    <div className="grid">
      {items.map((item) => {
        const location = item.space_id != null ? spaceIndex[item.space_id] : null
        return (
          <EntityCard
            key={item.id}
            title={item.name}
            icon={CATEGORY_ICONS[item.category]}
            badge={item.category}
            badgeClass={`badge--${item.category}`}
            obj={item}
            hidden={HIDDEN}
          >
            <p className="card-location">
              {location ? (
                <>
                  📍 {location.property.name} · {location.space.name}
                </>
              ) : (
                <span className="muted">Unassigned</span>
              )}
            </p>
          </EntityCard>
        )
      })}
    </div>
  )
}
