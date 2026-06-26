import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'
import { context, isProd } from './context.js'
import './index.css'

// Initiation: announce the runtime context (db + environment) we booted with.
// Kept quiet in prod to avoid leaking the profile into the console.
if (!isProd) {
  console.info(`[home-hub] starting — db=${context.db} environment=${context.environment}`)
}

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>,
)
