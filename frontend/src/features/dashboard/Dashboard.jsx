import React, { useState } from 'react'
import Card from '../../components/common/Card'

const Dashboard = () => {
  const [currentTime] = useState(new Date())
  
  // Data aktual air sungai
  const actualData = {
    status: 'Aman',
    waterLevel: 0.27,
    timestamp: '08:40',
    date: 'Rabu, 2025-09-03'
  }
  
  // Data prediksi
  const predictionData = {
    status: 'Aman',
    waterLevel: 0.53,
    timestamp: '08:40',
    date: 'Rabu, 2025-09-03'
  }
  
  // Konfigurasi prediksi
  const predictionConfig = {
    model: 'LSTM',
    period: '1 Jam'
  }
  
  // Data tabel prediksi
  const predictionTableData = [
    { time: '08:00', actual: 0.27, prediction: 0.53 },
    { time: '07:00', actual: 0.24, prediction: 0.48 },
    { time: '06:00', actual: 0.23, prediction: 0.45 },
    { time: '05:00', actual: 0.27, prediction: 0.42 },
    { time: '04:00', actual: 0.29, prediction: 0.39 },
    { time: '03:00', actual: 0.26, prediction: 0.36 },
    { time: '02:00', actual: 0.23, prediction: 0.33 },
    { time: '01:00', actual: 0.57, prediction: 0.30 },
    { time: '00:00', actual: 0.66, prediction: 0.27 },
    { time: '23:00', actual: 0.14, prediction: 0.24 },
    { time: '22:00', actual: 0.79, prediction: 0.21 },
    { time: '21:00', actual: 1.12, prediction: 0.18 },
    { time: '20:00', actual: 0.50, prediction: 0.15 },
    { time: '19:00', actual: 0.44, prediction: 0.12 }
  ]

  const formatDate = (date) => {
    const options = { 
      weekday: 'long', 
      year: 'numeric', 
      month: '2-digit', 
      day: '2-digit' 
    }
    return date.toLocaleDateString('id-ID', options)
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

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50">
      {/* Header */}
      <div className="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Hi, 👋</h1>
            <p className="text-gray-600">Selamat memonitor air sungai!</p>
            <p className="text-sm text-gray-500 mt-1">{formatDate(currentTime)}</p>
          </div>
          <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Kirim Request untuk Notifikasi KLIK DISINI
          </button>
        </div>
      </div>

      <div className="p-6 space-y-6">
        {/* Top Row - Two Cards */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* Perkembangan Air Sungai Dhompo Aktual */}
          <Card className="bg-white shadow-lg border-0">
            <div className="p-6">
              <div className="flex items-center justify-between mb-4">
                <div>
                  <h3 className="text-xl font-bold text-gray-900 mb-2">
                    Perkembangan Air Sungai Dhompo Aktual
                  </h3>
                  <p className="text-gray-600 text-sm">Informasi kondisi sungai saat ini</p>
                </div>
                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(actualData.status)}`}>
                  <span className="mr-1">✓</span>
                  {actualData.status}
                </span>
              </div>
              
              <div className="grid grid-cols-2 gap-4 mb-6">
                <div className="text-center">
                  <div className="text-2xl font-bold text-blue-600">{actualData.timestamp}</div>
                  <div className="text-sm text-gray-500">Waktu</div>
                </div>
                <div className="text-center">
                  <div className="text-2xl font-bold text-blue-600">{actualData.waterLevel} m</div>
                  <div className="text-sm text-gray-500">Tinggi Muka Air</div>
                </div>
              </div>

              {/* Simple Chart Visualization */}
              <div className="bg-gray-50 rounded-lg p-4">
                <div className="text-sm text-gray-600 mb-2">Level Air</div>
                <div className="relative h-32 bg-white rounded border">
                  {/* Tanggul Level */}
                  <div className="absolute top-4 left-0 right-0 h-1 bg-gray-300"></div>
                  <div className="absolute top-8 left-0 right-0 h-1 bg-gray-300"></div>
                  <div className="absolute top-12 left-0 right-0 h-1 bg-gray-300"></div>
                  <div className="absolute top-16 left-0 right-0 h-1 bg-gray-300"></div>
                  <div className="absolute top-20 left-0 right-0 h-1 bg-gray-300"></div>
                  
                  {/* Water Level Line */}
                  <svg className="w-full h-full" viewBox="0 0 100 32">
                    <polyline
                      fill="none"
                      stroke="#3B82F6"
                      strokeWidth="2"
                      points="0,28 10,26 20,25 30,24 40,23 50,22 60,21 70,20 80,19 90,18 100,17"
                    />
                  </svg>
                  
                  {/* Current Water Level Indicator */}
                  <div className="absolute bottom-2 right-4 bg-blue-600 text-white text-xs px-2 py-1 rounded">
                    {actualData.waterLevel}m
                  </div>
                </div>
                <div className="text-xs text-gray-500 mt-2">Tinggi Air (m)</div>
              </div>
            </div>
          </Card>

          {/* Konfigurasi Prediksi */}
          <Card className="bg-white shadow-lg border-0">
            <div className="p-6">
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-xl font-bold text-gray-900">Konfigurasi Prediksi</h3>
                <button className="text-blue-600 hover:text-blue-700">
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </button>
              </div>
              
              <p className="text-gray-600 text-sm mb-6">
                Atur komponen prediksi dengan menekan tombol pada masing-masing opsi konfigurasi.
              </p>

              <div className="space-y-4 mb-6">
                <div className="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                  <span className="text-sm font-medium text-gray-700">Model</span>
                  <span className="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                    {predictionConfig.model}
                  </span>
                </div>
                <div className="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                  <span className="text-sm font-medium text-gray-700">Periode</span>
                  <span className="bg-green-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                    {predictionConfig.period}
                  </span>
                </div>
              </div>

              {/* Prediction Chart */}
              <div className="bg-gray-50 rounded-lg p-4">
                <div className="text-sm text-gray-600 mb-2">Level Air Prediksi</div>
                <div className="relative h-32 bg-white rounded border">
                  {/* Tanggul Level */}
                  <div className="absolute top-4 left-0 right-0 h-1 bg-gray-300"></div>
                  <div className="absolute top-8 left-0 right-0 h-1 bg-gray-300"></div>
                  <div className="absolute top-12 left-0 right-0 h-1 bg-gray-300"></div>
                  <div className="absolute top-16 left-0 right-0 h-1 bg-gray-300"></div>
                  <div className="absolute top-20 left-0 right-0 h-1 bg-gray-300"></div>
                  
                  {/* Prediction Water Level Line */}
                  <svg className="w-full h-full" viewBox="0 0 100 32">
                    <polyline
                      fill="none"
                      stroke="#10B981"
                      strokeWidth="2"
                      points="0,26 10,25 20,24 30,23 40,22 50,21 60,20 70,19 80,18 90,17 100,16"
                    />
                  </svg>
                  
                  {/* Predicted Water Level Indicator */}
                  <div className="absolute bottom-2 right-4 bg-green-600 text-white text-xs px-2 py-1 rounded">
                    {predictionData.waterLevel}m
                  </div>
                </div>
                <div className="text-xs text-gray-500 mt-2">Tinggi Air (m)</div>
              </div>
            </div>
          </Card>
        </div>

        {/* Bottom Row - Two Cards */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* Prediksi Perkembangan Air Sungai Dhompo */}
          <Card className="bg-white shadow-lg border-0">
            <div className="p-6">
              <div className="flex items-center justify-between mb-4">
                <div>
                  <h3 className="text-xl font-bold text-gray-900 mb-2">
                    Prediksi Perkembangan Air Sungai Dhompo
                  </h3>
                  <p className="text-gray-600 text-sm">
                    Informasi kondisi sungai yang akan datang berdasarkan konfigurasi prediksi.
                  </p>
                </div>
                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(predictionData.status)}`}>
                  <span className="mr-1">✓</span>
                  {predictionData.status}
                </span>
              </div>
              
              <div className="grid grid-cols-2 gap-4 mb-6">
                <div className="text-center">
                  <div className="text-2xl font-bold text-green-600">{predictionData.timestamp}</div>
                  <div className="text-sm text-gray-500">Waktu</div>
                </div>
                <div className="text-center">
                  <div className="text-2xl font-bold text-green-600">{predictionData.waterLevel} m</div>
                  <div className="text-sm text-gray-500">Tinggi Muka Air</div>
                </div>
              </div>

              {/* Advanced Chart */}
              <div className="bg-gray-50 rounded-lg p-4">
                <div className="text-sm text-gray-600 mb-2">Perkembangan Air</div>
                <div className="relative h-32 bg-white rounded border">
                  {/* Y-axis labels */}
                  <div className="absolute left-0 top-0 bottom-0 flex flex-col justify-between text-xs text-gray-500 px-1">
                    <span>3.5</span>
                    <span>2.8</span>
                    <span>2.1</span>
                    <span>1.4</span>
                    <span>0.7</span>
                    <span>0.0</span>
                  </div>
                  
                  {/* Grid lines */}
                  <div className="absolute left-8 right-0 top-0 bottom-0">
                    <div className="h-full w-full">
                      {[0, 1, 2, 3, 4, 5].map((i) => (
                        <div key={i} className="absolute left-0 right-0 h-px bg-gray-200" style={{ top: `${i * 20}%` }}></div>
                      ))}
                    </div>
                  </div>
                  
                  {/* Chart lines */}
                  <svg className="absolute left-8 right-0 top-0 bottom-0 w-full h-full" viewBox="0 0 100 32">
                    {/* Actual data line */}
                    <polyline
                      fill="none"
                      stroke="#3B82F6"
                      strokeWidth="2"
                      points="0,28 8,26 16,24 24,22 32,20 40,18 48,16 56,14 64,12 72,10 80,8 88,6 96,4 100,2"
                    />
                    {/* Prediction line */}
                    <polyline
                      fill="none"
                      stroke="#F59E0B"
                      strokeWidth="2"
                      points="0,26 8,24 16,22 24,20 32,18 40,16 48,14 56,12 64,10 72,8 80,6 88,4 96,2 100,0"
                    />
                  </svg>
                  
                  {/* Legend */}
                  <div className="absolute bottom-2 left-8 flex items-center space-x-4 text-xs">
                    <div className="flex items-center space-x-1">
                      <div className="w-3 h-0.5 bg-blue-500"></div>
                      <span className="text-gray-600">Aktual</span>
                    </div>
                    <div className="flex items-center space-x-1">
                      <div className="w-3 h-0.5 bg-orange-500"></div>
                      <span className="text-gray-600">Prediksi</span>
                    </div>
                  </div>
                </div>
                <div className="text-xs text-gray-500 mt-2">Periode Waktu</div>
              </div>
            </div>
          </Card>

          {/* Tabel Prediksi */}
          <Card className="bg-white shadow-lg border-0">
            <div className="p-6">
              <h3 className="text-xl font-bold text-gray-900 mb-4">
                Tabel Prediksi Perkembangan Air Sungai Dhompo
              </h3>
              
              <div className="overflow-hidden rounded-lg border border-gray-200">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Jam
                      </th>
                      <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aktual
                      </th>
                      <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Prediksi
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {predictionTableData.map((row, index) => (
                      <tr key={index} className="hover:bg-gray-50">
                        <td className="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                          {row.time}
                        </td>
                        <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                          {row.actual}
                        </td>
                        <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                          {row.prediction || '-'}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
              
              <div className="mt-4 text-center">
                <button className="text-blue-600 hover:text-blue-700 text-sm font-medium">
                  Lihat Semua Data →
                </button>
              </div>
            </div>
          </Card>
        </div>
      </div>
    </div>
  )
}

export default Dashboard
