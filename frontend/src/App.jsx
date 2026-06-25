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
        <h1>Home Hub</h1>
        <p className="tagline">Browse your spaces and belongings</p>
      </header>
      <main className="app-main">
        {data.error ? (
          <p className="empty">Couldn’t load data: {data.error}</p>
        ) : data.loading ? (
          <p className="empty">Loading…</p>
        ) : (
          <Tabs items={tabs} className="root-tabs" />
        )}
      </main>
    </div>
  )
}
