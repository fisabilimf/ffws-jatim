import React from 'react'
import Card from '../components/common/Card'

const Home = () => {
  const features = [
    {
      icon: '🌊',
      title: 'Monitoring Real-time',
      description: 'Pemantauan kondisi air sungai secara real-time dengan update setiap jam'
    },
    {
      icon: '🔮',
      title: 'Prediksi Cerdas',
      description: 'Sistem prediksi banjir menggunakan teknologi AI dan machine learning'
    },
    {
      icon: '📱',
      title: 'Notifikasi Otomatis',
      description: 'Peringatan otomatis ketika kondisi air sungai mencapai level berbahaya'
    },
    {
      icon: '📊',
      title: 'Analisis Data',
      description: 'Analisis data historis untuk pengambilan keputusan yang lebih baik'
    }
  ]

  const stats = [
    { label: 'Sungai Terpantau', value: '15+', color: 'text-blue-600' },
    { label: 'Update per Jam', value: '24x', color: 'text-green-600' },
    { label: 'Akurasi Prediksi', value: '95%', color: 'text-purple-600' },
    { label: 'Wilayah Cakupan', value: 'Jawa Timur', color: 'text-orange-600' }
  ]

  const recentAlerts = [
    {
      location: 'Sungai Dhompo',
      status: 'Aman',
      time: '2 jam yang lalu',
      waterLevel: '0.27m'
    },
    {
      location: 'Sungai Brantas',
      status: 'Siaga',
      time: '4 jam yang lalu',
      waterLevel: '1.85m'
    },
    {
      location: 'Sungai Surabaya',
      status: 'Aman',
      time: '6 jam yang lalu',
      waterLevel: '0.45m'
    }
  ]

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
      {/* Hero Section */}
      <div className="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div className="max-w-7xl mx-auto px-6 py-16">
          <div className="text-center">
            <h1 className="text-4xl md:text-6xl font-bold mb-6">
              FFWS JATIM
            </h1>
            <p className="text-xl md:text-2xl mb-8 text-blue-100">
              Flood Forecasting & Weather System Jawa Timur
            </p>
            <p className="text-lg text-blue-200 mb-8 max-w-3xl mx-auto">
              Sistem monitoring dan prediksi banjir cerdas untuk melindungi masyarakat Jawa Timur 
              dari ancaman bencana banjir dengan teknologi AI dan data real-time.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <a 
                href="/dashboard" 
                className="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors"
              >
                Lihat Dashboard
              </a>
              <a 
                href="/history" 
                className="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors"
              >
                Riwayat Data
              </a>
            </div>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-6 py-16">
        {/* Statistics */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
          {stats.map((stat, index) => (
            <Card key={index} className="text-center bg-white shadow-lg border-0">
              <div className="p-6">
                <div className={`text-3xl font-bold ${stat.color} mb-2`}>
                  {stat.value}
                </div>
                <div className="text-gray-600 text-sm">
                  {stat.label}
                </div>
              </div>
            </Card>
          ))}
        </div>

        {/* Features */}
        <div className="mb-16">
          <h2 className="text-3xl font-bold text-gray-900 text-center mb-12">
            Fitur Unggulan
          </h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {features.map((feature, index) => (
              <Card key={index} className="text-center bg-white shadow-lg border-0 hover:shadow-xl transition-shadow">
                <div className="p-6">
                  <div className="text-4xl mb-4">{feature.icon}</div>
                  <h3 className="text-xl font-semibold text-gray-900 mb-3">
                    {feature.title}
                  </h3>
                  <p className="text-gray-600">
                    {feature.description}
                  </p>
                </div>
              </Card>
            ))}
          </div>
        </div>

        {/* Recent Alerts */}
        <div className="mb-16">
          <h2 className="text-3xl font-bold text-gray-900 text-center mb-12">
            Peringatan Terbaru
          </h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {recentAlerts.map((alert, index) => (
              <Card key={index} className="bg-white shadow-lg border-0">
                <div className="p-6">
                  <div className="flex items-center justify-between mb-4">
                    <h3 className="text-lg font-semibold text-gray-900">
                      {alert.location}
                    </h3>
                    <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(alert.status)}`}>
                      {alert.status}
                    </span>
                  </div>
                  <div className="space-y-2">
                    <div className="flex justify-between">
                      <span className="text-gray-600">Tinggi Air:</span>
                      <span className="font-medium text-gray-900">{alert.waterLevel}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-600">Update:</span>
                      <span className="font-medium text-gray-900">{alert.time}</span>
                    </div>
                  </div>
                </div>
              </Card>
            ))}
          </div>
        </div>

        {/* About Section */}
        <div className="bg-white rounded-2xl shadow-lg p-8 md:p-12">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl font-bold text-gray-900 mb-6">
                Tentang FFWS JATIM
              </h2>
              <p className="text-gray-600 mb-6 leading-relaxed">
                FFWS JATIM adalah sistem monitoring dan prediksi banjir yang dikembangkan khusus 
                untuk wilayah Jawa Timur. Sistem ini menggabungkan teknologi sensor IoT, 
                machine learning, dan analisis data untuk memberikan peringatan dini yang akurat.
              </p>
              <p className="text-gray-600 mb-6 leading-relaxed">
                Dengan cakupan 15+ sungai utama di Jawa Timur, FFWS JATIM membantu 
                pemerintah dan masyarakat dalam pengambilan keputusan untuk mitigasi bencana banjir.
              </p>
              <div className="flex flex-col sm:flex-row gap-4">
                <button className="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                  Pelajari Lebih Lanjut
                </button>
                <button className="border-2 border-blue-600 text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                  Hubungi Kami
                </button>
              </div>
            </div>
            <div className="relative">
              <div className="bg-gradient-to-br from-blue-100 to-green-100 rounded-2xl p-8">
                <div className="text-center">
                  <div className="text-6xl mb-4">🏛️</div>
                  <h3 className="text-xl font-semibold text-gray-900 mb-2">
                    Pemerintah Provinsi Jawa Timur
                  </h3>
                  <p className="text-gray-600">
                    Dinas Pekerjaan Umum dan Penataan Ruang
                  </p>
                  <p className="text-gray-600">
                    Bidang Sumber Daya Air
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Home
