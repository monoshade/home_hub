import FieldList from './FieldList'

// A titled card showing an entity's fields, with an optional badge and
// arbitrary children (e.g. a nested list of related items).
export default function EntityCard({ title, badge, badgeClass = '', obj, hidden = [], children }) {
  return (
    <div className="card">
      <div className="card-head">
        <h4 className="card-title">{title}</h4>
        {badge && <span className={`badge ${badgeClass}`}>{badge}</span>}
      </div>
      {obj && <FieldList obj={obj} hidden={hidden} />}
      {children}
    </div>
  )
}
