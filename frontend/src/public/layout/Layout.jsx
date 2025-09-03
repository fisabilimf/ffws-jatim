import React, { useState } from 'react'
import Header from './Header'
import Footer from './Footer'
import Dashboard from '../pages/Dashboard'
import FloodWarningTicker from '../components/FloodTicker'
import MapboxMap from '../components/MapboxMap'

const Layout = ({ children }) => {
  const [tickerData, setTickerData] = useState(null)

  return (
    <div className="min-h-screen bg-gray-50">
      <FloodWarningTicker onDataUpdate={setTickerData} />
      {tickerData && (
        <div className="w-full px-6 py-4">
          <MapboxMap tickerData={tickerData} />
        </div>
      )}
      <main className="w-full p-6">
        {children || <Dashboard />}
      </main>
      <Footer />
    </div>
  )
}

export default Layout
