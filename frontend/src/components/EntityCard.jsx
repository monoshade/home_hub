import FieldList from './FieldList'
import styles from './EntityCard.module.css'
import shared from '../styles/shared.module.css'

// A titled card showing an entity's fields, with an optional leading icon,
// a badge, and arbitrary children (e.g. a nested list of related items).
// `badgeClass` is a caller-supplied (module-scoped) variant class.
export default function EntityCard({ title, icon, badge, badgeClass = '', obj, hidden = [], children }) {
  return (
    <div className={styles.card}>
      <div className={styles.cardHead}>
        <div className={styles.cardTitleWrap}>
          {icon && <span className={styles.cardIcon} aria-hidden="true">{icon}</span>}
          <h4 className={styles.cardTitle}>{title}</h4>
        </div>
        {badge && <span className={`${shared.badge} ${badgeClass}`}>{badge}</span>}
      </div>
      {obj && <FieldList obj={obj} hidden={hidden} />}
      {children}
    </div>
  )
}
