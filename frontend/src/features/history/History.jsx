import React, { useState } from 'react'
import Card from '../../components/common/Card'

const History = () => {
  const [selectedPeriod, setSelectedPeriod] = useState('7d')
  const [selectedLocation, setSelectedLocation] = useState('dhompo')
  
  // Data riwayat air sungai
  const historyData = [
    {
      id: 1,
      location: 'Sungai Dhompo',
      timestamp: '2025-09-03 08:00',
      waterLevel: 0.27,
      status: 'Aman',
      rainfall: 0.0,
      temperature: 28.5,
      humidity: 75
    },
    {
      id: 2,
      location: 'Sungai Dhompo',
      timestamp: '2025-09-03 07:00',
      waterLevel: 0.24,
      status: 'Aman',
      rainfall: 0.0,
      temperature: 27.8,
      humidity: 78
    },
    {
      id: 3,
      location: 'Sungai Dhompo',
      timestamp: '2025-09-03 06:00',
      waterLevel: 0.23,
      status: 'Aman',
      rainfall: 0.0,
      temperature: 26.2,
      humidity: 82
    },
    {
      id: 4,
      location: 'Sungai Dhompo',
      timestamp: '2025-09-03 05:00',
      waterLevel: 0.27,
      status: 'Aman',
      rainfall: 0.0,
      temperature: 25.5,
      humidity: 85
    },
    {
      id: 5,
      location: 'Sungai Dhompo',
      timestamp: '2025-09-03 04:00',
      waterLevel: 0.29,
      status: 'Aman',
      rainfall: 0.0,
      temperature: 24.8,
      humidity: 88
    },
    {
      id: 6,
      location: 'Sungai Dhompo',
      timestamp: '2025-09-03 03:00',
      waterLevel: 0.26,
      status: 'Aman',
      rainfall: 0.0,
      temperature: 24.2,
      humidity: 90
    },
    {
      id: 7,
      location: 'Sungai Dhompo',
      timestamp: '2025-09-03 02:00',
      waterLevel: 0.23,
      status: 'Aman',
      rainfall: 0.0,
      temperature: 23.8,
      humidity: 92
    },
    {
      id: 8,
      location: 'Sungai Dhompo',
      timestamp: '2025-09-03 01:00',
      waterLevel: 0.57,
      status: 'Siaga',
      rainfall: 15.2,
      temperature: 23.5,
      humidity: 95
    },
    {
      id: 9,
      location: 'Sungai Dhompo',
      timestamp: '2025-09-03 00:00',
      waterLevel: 0.66,
      status: 'Siaga',
      rainfall: 25.8,
      temperature: 23.2,
      humidity: 96
    },
    {
      id: 10,
      location: 'Sungai Dhompo',
      timestamp: '2025-09-02 23:00',
      waterLevel: 0.14,
      status: 'Aman',
      rainfall: 0.0,
      temperature: 23.0,
      humidity: 94
    }
  ]

  // Data statistik
  const statistics = {
    totalRecords: historyData.length,
    averageWaterLevel: (historyData.reduce((sum, item) => sum + item.waterLevel, 0) / historyData.length).toFixed(2),
    maxWaterLevel: Math.max(...historyData.map(item => item.waterLevel)),
    minWaterLevel: Math.min(...historyData.map(item => item.waterLevel)),
    totalRainfall: historyData.reduce((sum, item) => sum + item.rainfall, 0).toFixed(1)
  }

  const getStatusColor = (status) => {
    switch (status) {
      case 'Aman':
        return 'bg-green-100 text-green-800'
      case 'Siaga':
        return 'bg-yellow-100 text-yellow-800'
      case 'Waspada':
        return 'bg-orange-100 text-orange-800'
      case 'Bahaya':
        return 'bg-red-100 text-red-800'
      default:
        return 'bg-gray-100 text-gray-800'
    }
  }

  const getStatusIcon = (status) => {
    switch (status) {
      case 'Aman':
        return '✓'
      case 'Siaga':
        return '⚠'
      case 'Waspada':
        return '⚡'
      case 'Bahaya':
        return '🚨'
      default:
        return '•'
    }
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50">
      {/* Header */}
      <div className="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Riwayat Monitoring</h1>
            <p className="text-gray-600">Data historis kondisi air sungai dan cuaca</p>
          </div>
          <div className="flex items-center space-x-4">
            <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
              Export Data
            </button>
            <button className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
              Generate Report
            </button>
          </div>
        </div>
      </div>

      <div className="p-6 space-y-6">
        {/* Filters and Controls */}
        <Card className="bg-white shadow-lg border-0">
          <div className="p-6">
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
              <div className="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                  <select
                    value={selectedPeriod}
                    onChange={(e) => setSelectedPeriod(e.target.value)}
                    className="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="1d">24 Jam Terakhir</option>
                    <option value="7d">7 Hari Terakhir</option>
                    <option value="30d">30 Hari Terakhir</option>
                    <option value="90d">90 Hari Terakhir</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                  <select
                    value={selectedLocation}
                    onChange={(e) => setSelectedLocation(e.target.value)}
                    className="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="dhompo">Sungai Dhompo</option>
                    <option value="brantas">Sungai Brantas</option>
                    <option value="surabaya">Sungai Surabaya</option>
                  </select>
                </div>
              </div>
              <div className="flex items-center space-x-2">
                <span className="text-sm text-gray-500">Update terakhir:</span>
                <span className="text-sm font-medium text-gray-900">
                  {new Date().toLocaleString('id-ID')}
                </span>
              </div>
            </div>
          </div>
        </Card>

        {/* Statistics Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
          <Card className="bg-white shadow-lg border-0">
            <div className="p-4 text-center">
              <div className="text-2xl font-bold text-blue-600">{statistics.totalRecords}</div>
              <div className="text-sm text-gray-500">Total Record</div>
            </div>
          </Card>
          <Card className="bg-white shadow-lg border-0">
            <div className="p-4 text-center">
              <div className="text-2xl font-bold text-green-600">{statistics.averageWaterLevel}m</div>
              <div className="text-sm text-gray-500">Rata-rata TMA</div>
            </div>
          </Card>
          <Card className="bg-white shadow-lg border-0">
            <div className="p-4 text-center">
              <div className="text-2xl font-bold text-orange-600">{statistics.maxWaterLevel}m</div>
              <div className="text-sm text-gray-500">TMA Tertinggi</div>
            </div>
          </Card>
          <Card className="bg-white shadow-lg border-0">
            <div className="p-4 text-center">
              <div className="text-2xl font-bold text-blue-600">{statistics.minWaterLevel}m</div>
              <div className="text-sm text-gray-500">TMA Terendah</div>
            </div>
          </Card>
          <Card className="bg-white shadow-lg border-0">
            <div className="p-4 text-center">
              <div className="text-2xl font-bold text-purple-600">{statistics.totalRainfall}mm</div>
              <div className="text-sm text-gray-500">Total Curah Hujan</div>
            </div>
          </Card>
        </div>

        {/* History Table */}
        <Card className="bg-white shadow-lg border-0">
          <div className="p-6">
            <div className="flex items-center justify-between mb-6">
              <h3 className="text-xl font-bold text-gray-900">Data Riwayat Monitoring</h3>
              <div className="flex items-center space-x-2">
                <span className="text-sm text-gray-500">Menampilkan:</span>
                <span className="text-sm font-medium text-gray-900">{historyData.length} data</span>
              </div>
            </div>
            
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Waktu
                    </th>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Lokasi
                    </th>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Tinggi Muka Air
                    </th>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Curah Hujan
                    </th>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Suhu
                    </th>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Kelembaban
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {historyData.map((item) => (
                    <tr key={item.id} className="hover:bg-gray-50 transition-colors">
                      <td className="px-4 py-3 whitespace-nowrap">
                        <div className="text-sm font-medium text-gray-900">{item.timestamp}</div>
                      </td>
                      <td className="px-4 py-3 whitespace-nowrap">
                        <div className="text-sm text-gray-900">{item.location}</div>
                      </td>
                      <td className="px-4 py-3 whitespace-nowrap">
                        <div className="text-sm font-medium text-gray-900">{item.waterLevel} m</div>
                      </td>
                      <td className="px-4 py-3 whitespace-nowrap">
                        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(item.status)}`}>
                          <span className="mr-1">{getStatusIcon(item.status)}</span>
                          {item.status}
                        </span>
                      </td>
                      <td className="px-4 py-3 whitespace-nowrap">
                        <div className="text-sm text-gray-900">{item.rainfall} mm</div>
                      </td>
                      <td className="px-4 py-3 whitespace-nowrap">
                        <div className="text-sm text-gray-900">{item.temperature}°C</div>
                      </td>
                      <td className="px-4 py-3 whitespace-nowrap">
                        <div className="text-sm text-gray-900">{item.humidity}%</div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
            
            {/* Pagination */}
            <div className="mt-6 flex items-center justify-between">
              <div className="text-sm text-gray-700">
                Menampilkan <span className="font-medium">1</span> sampai <span className="font-medium">{historyData.length}</span> dari <span className="font-medium">{historyData.length}</span> hasil
              </div>
              <div className="flex items-center space-x-2">
                <button className="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                  Sebelumnya
                </button>
                <span className="px-3 py-2 text-sm font-medium text-gray-700 bg-blue-50 border border-blue-200 rounded-md">
                  1
                </span>
                <button className="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                  Selanjutnya
                </button>
              </div>
            </div>
          </div>
        </Card>
      </div>
    </div>
  )
}

export default History
