import Tabs from './components/Tabs'
import SpaceView from './components/SpaceView'
import DeviceView from './components/DeviceView'
import { useHomeData } from './hooks/useHomeData'

export default function App() {
  const data = useHomeData()

  const tabs = [
    { id: 'space', label: 'Space View', render: () => <SpaceView data={data} /> },
    { id: 'devices', label: 'Full Device View', render: () => <DeviceView data={data} /> },
  ]

  return (
    <div className="app">
      <header className="app-header">
        <div className="brand">
          <span className="brand-mark" aria-hidden="true">🏠</span>
          <div>
            <h1>Home Hub</h1>
            <p className="tagline">Browse your spaces and belongings</p>
          </div>
        </div>
      </header>
      <main className="app-main">
        {data.error ? (
          <p className="empty">Couldn’t load data: {data.error}</p>
        ) : data.loading ? (
          <div className="loading">
            <span className="spinner" aria-hidden="true" />
            <span>Loading your home…</span>
          </div>
        ) : (
          <Tabs items={tabs} className="root-tabs" />
        )}
      </main>
    </div>
  )
}
