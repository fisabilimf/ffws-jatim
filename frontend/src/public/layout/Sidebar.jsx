import React from 'react'

const Sidebar = () => {
  return (
    <aside className="w-64 bg-white shadow-sm border-r border-gray-200 min-h-screen">
      <div className="p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Menu Utama</h2>
        <nav className="space-y-2">
          <a href="#" className="block px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-md">
            🏠 Dashboard
          </a>
          <a href="#" className="block px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 hover:text-gray-900">
            👥 Kelola Users
          </a>
          <a href="#" className="block px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 hover:text-gray-900">
            📊 Laporan & Analytics
          </a>
          <a href="#" className="block px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 hover:text-gray-900">
            ⚙️ Pengaturan Sistem
          </a>
          <a href="#" className="block px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 hover:text-gray-900">
            🔐 Manajemen Role
          </a>
          <a href="#" className="block px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 hover:text-gray-900">
            📝 Log Aktivitas
          </a>
          <a href="#" className="block px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 hover:text-gray-900">
            👨‍💼 Profil Saya
          </a>
        </nav>
      </div>
    </aside>
  )
}

export default Sidebar
