import React from 'react'

export const PredictionChart = ({ data }) => {
  return (
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
  )
}
