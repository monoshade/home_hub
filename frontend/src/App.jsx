import Tabs from './components/Tabs'
import SpaceView from './components/SpaceView'
import DeviceView from './components/DeviceView'
import { useHomeData } from './hooks/useHomeData'
import styles from './App.module.css'
import shared from './styles/shared.module.css'

export default function App() {
  const data = useHomeData()

  const tabs = [
    { id: 'space', label: 'Space View', render: () => <SpaceView data={data} /> },
    { id: 'devices', label: 'Full Device View', render: () => <DeviceView data={data} /> },
  ]

  return (
    <div className={styles.app}>
      <header className={styles.appHeader}>
        <div className={styles.brand}>
          <span className={styles.brandMark} aria-hidden="true">🏠</span>
          <div>
            <h1 className={styles.title}>Home Hub</h1>
            <p className={styles.tagline}>Browse your spaces and belongings</p>
          </div>
        </div>
      </header>
      <main className={styles.appMain}>
        {data.error ? (
          <p className={shared.empty}>Couldn’t load data: {data.error}</p>
        ) : data.loading ? (
          <div className={styles.loading}>
            <span className={styles.spinner} aria-hidden="true" />
            <span>Loading your home…</span>
          </div>
        ) : (
          <Tabs items={tabs} variant="pill" />
        )}
      </main>
    </div>
  )
}
