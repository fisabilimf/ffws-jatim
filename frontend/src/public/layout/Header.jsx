import React from 'react'

const Header = () => {
  return (
    <header className="bg-white shadow-sm border-b border-gray-200">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          <div className="flex items-center">
            <h1 className="text-xl font-semibold text-gray-900">
              Laravel
            </h1>
          </div>
          <div className="flex items-center space-x-4">
            <span className="text-gray-700">
              Selamat datang, Administrator!
            </span>
            <nav className="flex space-x-4">
              <a href="#" className="text-blue-600 hover:text-blue-800 px-3 py-2 text-sm font-medium">
                Admin Panel
              </a>
              <a href="#" className="text-red-600 hover:text-red-800 px-3 py-2 text-sm font-medium">
                Logout
              </a>
            </nav>
          </div>
        </div>
      </div>
    </header>
  )
}

export default Header
