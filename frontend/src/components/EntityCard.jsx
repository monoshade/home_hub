import FieldList from './FieldList'

// A titled card showing an entity's fields, with an optional leading icon,
// a badge, and arbitrary children (e.g. a nested list of related items).
export default function EntityCard({ title, icon, badge, badgeClass = '', obj, hidden = [], children }) {
  return (
    <div className="card">
      <div className="card-head">
        <div className="card-title-wrap">
          {icon && <span className="card-icon" aria-hidden="true">{icon}</span>}
          <h4 className="card-title">{title}</h4>
        </div>
        {badge && <span className={`badge ${badgeClass}`}>{badge}</span>}
      </div>
      {obj && <FieldList obj={obj} hidden={hidden} />}
      {children}
    </div>
  )
}
