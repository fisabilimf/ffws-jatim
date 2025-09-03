import React from 'react'
import Header from './Header'
import Footer from './Footer'
import Dashboard from '../dashboard/Dashboard'
import FloodWarningTicker from '../common/StockTicker'

const Layout = ({ children }) => {
  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <FloodWarningTicker />
      <main className="w-full p-6">
        {children || <Dashboard />}
      </main>
      <Footer />
    </div>
  )
}

export default Layout
