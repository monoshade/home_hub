import { useState } from 'react'
import styles from './Tabs.module.css'

// Generic tab strip. `items` is [{ id, label, render: () => node }].
// `variant` selects the visual style ('pill' | 'underline'). Only the active
// panel is rendered. Remount (via a `key` prop) to reset the active tab when
// the underlying set of tabs changes.
export default function Tabs({ items, variant = 'pill' }) {
  const [activeId, setActiveId] = useState(items[0]?.id)
  const current = items.find((t) => t.id === activeId) ?? items[0]

  if (!current) return null

  return (
    <div className={`${styles.tabs} ${styles[variant] ?? ''}`}>
      <nav className={styles.tabBar} role="tablist">
        {items.map((tab) => (
          <button
            key={tab.id}
            role="tab"
            aria-selected={tab.id === current.id}
            className={`${styles.tab} ${tab.id === current.id ? styles.tabActive : ''}`}
            onClick={() => setActiveId(tab.id)}
          >
            {tab.label}
          </button>
        ))}
      </nav>
      <div className={styles.tabPanel} role="tabpanel">
        {current.render()}
      </div>
    </div>
  )
}
