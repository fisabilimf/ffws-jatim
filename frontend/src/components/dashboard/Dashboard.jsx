import React from 'react'
import Card from '../common/Card'
import Button from '../common/Button'

const Dashboard = () => {
  const summaryCards = [
    {
      title: 'Total Users',
      value: '3',
      icon: '👥',
      color: 'bg-blue-500',
      textColor: 'text-blue-600'
    },
    {
      title: 'Active Users',
      value: '3',
      icon: '✅',
      color: 'bg-green-500',
      textColor: 'text-green-600'
    },
    {
      title: 'Pending Users',
      value: '0',
      icon: '⏰',
      color: 'bg-yellow-500',
      textColor: 'text-yellow-600'
    },
    {
      title: 'Total Revenue',
      value: 'Rp 0',
      icon: '📊',
      color: 'bg-purple-500',
      textColor: 'text-purple-600'
    }
  ]

  const quickActions = [
    { icon: '👤', label: 'Kelola Users', action: () => console.log('Kelola Users') },
    { icon: '⚙️', label: 'Pengaturan Sistem', action: () => console.log('Pengaturan Sistem') },
    { icon: '👨‍💼', label: 'Profil Saya', action: () => console.log('Profil Saya') }
  ]

  const recentActivities = [
    { 
      avatar: 'J', 
      name: 'Jane Smith', 
      email: 'jane@example.com', 
      status: 'Active',
      statusColor: 'bg-green-100 text-green-800'
    },
    { 
      avatar: 'A', 
      name: 'Administrator', 
      email: 'admin@example.com', 
      status: 'Active',
      statusColor: 'bg-green-100 text-green-800'
    },
    { 
      avatar: 'J', 
      name: 'John Doe', 
      email: 'john@example.com', 
      status: 'Active',
      statusColor: 'bg-green-100 text-green-800'
    }
  ]

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="text-center">
        <h1 className="text-3xl font-bold text-gray-900 mb-2">Admin Dashboard</h1>
        <p className="text-gray-600">
          Selamat datang di panel administrasi. Anda memiliki akses penuh ke semua fitur admin.
        </p>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {summaryCards.map((card, index) => (
          <Card key={index} className="text-center">
            <div className="flex flex-col items-center">
              <div className={`w-12 h-12 ${card.color} rounded-full flex items-center justify-center text-white text-2xl mb-3`}>
                {card.icon}
              </div>
              <h3 className="text-lg font-medium text-gray-900 mb-1">{card.title}</h3>
              <p className={`text-2xl font-bold ${card.textColor}`}>{card.value}</p>
            </div>
          </Card>
        ))}
      </div>

      {/* Bottom Section - Two Columns */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Quick Actions */}
        <Card title="Quick Actions">
          <div className="space-y-3">
            {quickActions.map((action, index) => (
              <button
                key={index}
                onClick={action.action}
                className="w-full flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors text-left"
              >
                <span className="text-2xl">{action.icon}</span>
                <span className="text-gray-700 font-medium">{action.label}</span>
              </button>
            ))}
          </div>
        </Card>

        {/* Recent Activities */}
        <Card title="Recent Activities">
          <div className="space-y-4">
            {recentActivities.map((activity, index) => (
              <div key={index} className="flex items-center space-x-3">
                <div className="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-sm font-semibold text-gray-700">
                  {activity.avatar}
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium text-gray-900 truncate">{activity.name}</p>
                  <p className="text-sm text-gray-500 truncate">{activity.email}</p>
                </div>
                <span className={`px-2 py-1 text-xs font-medium rounded-full ${activity.statusColor}`}>
                  {activity.status}
                </span>
              </div>
            ))}
          </div>
        </Card>
      </div>

      {/* Additional Content for Testing Scroll */}
      <div className="space-y-6">
        <Card title="Additional Information">
          <div className="space-y-4">
            <p className="text-gray-600">
              Ini adalah konten tambahan untuk menguji sticky positioning pada Flood Warning Ticker. 
              Scroll ke bawah untuk melihat efek sticky pada ticker.
            </p>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              {Array.from({ length: 9 }, (_, i) => (
                <div key={i} className="bg-gray-100 p-4 rounded-lg">
                  <h4 className="font-medium text-gray-900 mb-2">Item {i + 1}</h4>
                  <p className="text-sm text-gray-600">
                    Deskripsi untuk item {i + 1}. Ini membantu menambah konten untuk testing scroll.
                  </p>
                </div>
              ))}
            </div>
          </div>
        </Card>
      </div>
    </div>
  )
}

export default Dashboard



