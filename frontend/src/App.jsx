import Tabs from './components/Tabs'
import SpaceView from './components/SpaceView'
import DeviceView from './components/DeviceView'

export default function App() {
  const tabs = [
    { id: 'space', label: 'Space View', render: () => <SpaceView /> },
    { id: 'devices', label: 'Full Device View', render: () => <DeviceView /> },
  ]

  return (
    <div className="app">
      <header className="app-header">
        <h1>Home Hub</h1>
        <p className="tagline">Browse your spaces and belongings</p>
      </header>
      <main className="app-main">
        <Tabs items={tabs} className="root-tabs" />
      </main>
    </div>
  )
}
