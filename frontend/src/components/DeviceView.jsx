import Tabs from './Tabs'
import EntityCard from './EntityCard'
import { CATEGORIES, CATEGORY_ICONS } from '../config'
import styles from './DeviceView.module.css'
import shared from '../styles/shared.module.css'

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
    <div className={styles.view}>
      <p className={styles.viewHint}>Every belonging across all spaces — browse by category.</p>
      <Tabs items={tabs} variant="underline" />
    </div>
  )
}

function ItemGrid({ items, spaceIndex }) {
  if (!items.length) return <p className={shared.empty}>No items in this category.</p>

  return (
    <div className={shared.grid}>
      {items.map((item) => {
        const location = item.space_id != null ? spaceIndex[item.space_id] : null
        return (
          <EntityCard
            key={item.id}
            title={item.name}
            icon={CATEGORY_ICONS[item.category]}
            badge={item.category}
            badgeClass={shared[`badge--${item.category}`]}
            obj={item}
            hidden={HIDDEN}
          >
            <p className={styles.cardLocation}>
              {location ? (
                <>
                  📍 {location.property.name} · {location.space.name}
                </>
              ) : (
                <span className={shared.muted}>Unassigned</span>
              )}
            </p>
          </EntityCard>
        )
      })}
    </div>
  )
}
