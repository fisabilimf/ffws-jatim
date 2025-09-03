import React from 'react'
import Header from './Header'
import Footer from './Footer'
import Dashboard from '../pages/Dashboard'
import FloodWarningTicker from '../components/FloodTicker'

const Layout = ({ children }) => {
  return (
    <div className="min-h-screen bg-gray-50">
      <FloodWarningTicker />
      <main className="w-full p-6">
        {children || <Dashboard />}
      </main>
      <Footer />
    </div>
  )
}

export default Layout
