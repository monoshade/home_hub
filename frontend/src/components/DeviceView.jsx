import Tabs from './Tabs'
import EntityCard from './EntityCard'
import { CATEGORIES, items as allItems, spaceIndex } from '../data/sampleData'

const HIDDEN = ['id', 'category', 'spaceId', 'name']

// Full inventory of belongings, browsable by category via sub-tabs.
export default function DeviceView() {
  const tabs = CATEGORIES.map((category) => {
    const list = allItems.filter((item) => item.category === category.key)
    return {
      id: category.key,
      label: `${category.label} (${list.length})`,
      render: () => <ItemGrid items={list} />,
    }
  })

  return (
    <div className="view">
      <p className="view-hint">Every belonging across all spaces — browse by category.</p>
      <Tabs items={tabs} className="sub-tabs" />
    </div>
  )
}

function ItemGrid({ items }) {
  if (!items.length) return <p className="empty">No items in this category.</p>

  return (
    <div className="grid">
      {items.map((item) => {
        const location = item.spaceId ? spaceIndex[item.spaceId] : null
        return (
          <EntityCard
            key={item.id}
            title={item.name}
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
