import { useState } from 'react'

// Generic tab strip. `items` is [{ id, label, render: () => node }].
// Only the active panel is rendered. Remount (via a `key` prop) to reset
// the active tab when the underlying set of tabs changes.
export default function Tabs({ items, className = '' }) {
  const [activeId, setActiveId] = useState(items[0]?.id)
  const current = items.find((t) => t.id === activeId) ?? items[0]

  if (!current) return null

  return (
    <div className={`tabs ${className}`}>
      <nav className="tab-bar" role="tablist">
        {items.map((tab) => (
          <button
            key={tab.id}
            role="tab"
            aria-selected={tab.id === current.id}
            className={`tab ${tab.id === current.id ? 'tab--active' : ''}`}
            onClick={() => setActiveId(tab.id)}
          >
            {tab.label}
          </button>
        ))}
      </nav>
      <div className="tab-panel" role="tabpanel">
        {current.render()}
      </div>
    </div>
  )
}
