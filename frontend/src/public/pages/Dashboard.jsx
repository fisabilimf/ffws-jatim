import React from 'react'
import Card from '../components/Card'
import Button from '../components/Button'

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
}

export default Dashboard