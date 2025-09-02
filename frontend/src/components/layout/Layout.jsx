import React from 'react'
import Header from './Header'
import Footer from './Footer'
import Dashboard from '../dashboard/Dashboard'
import FloodWarningTicker from '../common/StockTicker'

const Layout = ({ children }) => {
  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="w-full py-2 bg-blue-50">
        <FloodWarningTicker />
      </div>
      <main className="w-full p-6">
        {children || <Dashboard />}
      </main>
      <Footer />
    </div>
  )
}

export default Layout
