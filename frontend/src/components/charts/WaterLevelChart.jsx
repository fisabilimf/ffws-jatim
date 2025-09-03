import React from 'react'

export const WaterLevelChart = ({ waterLevel, data, type = 'actual' }) => {
  const isPrediction = type === 'prediction'
  const chartColor = isPrediction ? '#10B981' : '#3B82F6'
  
  return (
    <div className="bg-gray-50 rounded-lg p-4">
      <div className="text-sm text-gray-600 mb-2">
        Level Air {isPrediction ? 'Prediksi' : 'Aktual'}
      </div>
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
            stroke={chartColor}
            strokeWidth="2"
            points="0,28 10,26 20,25 30,24 40,23 50,22 60,21 70,20 80,19 90,18 100,17"
          />
        </svg>
        
        {/* Current Water Level Indicator */}
        <div 
          className={`absolute bottom-2 right-4 text-white text-xs px-2 py-1 rounded`}
          style={{ backgroundColor: chartColor }}
        >
          {waterLevel}m
        </div>
      </div>
      <div className="text-xs text-gray-500 mt-2">Tinggi Air (m)</div>
    </div>
  )
}
