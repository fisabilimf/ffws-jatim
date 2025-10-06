import React from 'react';

const FloatingLegend = () => {
  const statusLevels = ['Aman', 'Waspada', 'Bahaya'];

  // Komponen SVG untuk ikon gelombang dengan warna berbeda dan lengkungan landai
  const WaveIcon = () => (
    <svg
      className="w-6 h-6"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 28 24"
      aria-hidden="true"
    >
      {/* Garis pertama: Biru muda (permukaan) */}
      <path
        stroke="#93c5fd" // Biru muda
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        // Mengubah nilai y pada kurva untuk membuatnya lebih landai
        d="M2 6C4 3, 6 3, 8 6s4 3, 6 0s4-3, 6 0s4 3, 6 0"
      />
      {/* Garis kedua: Biru sedang (tengah) */}
      <path
        stroke="#3b82f6" // Biru sedang
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        d="M2 12C4 9, 6 9, 8 12s4 3, 6 0s4-3, 6 0s4 3, 6 0"
      />
      {/* Garis ketiga: Biru tua (dasar) */}
      <path
        stroke="#1d4ed8" // Biru tua
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        d="M2 18C4 15, 6 15, 8 18s4 3, 6 0s4-3, 6 0s4 3, 6 0"
      />
    </svg>
  );

  const ArrowIcon = () => (
    <svg
      className="w-3 h-3 text-gray-600"
      fill="currentColor"
      viewBox="0 0 20 20"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path d="M10 16l6-12H4l6 12z" />
    </svg>
  );

  return (
    <div 
      className="w-80 rounded-lg bg-white/20 backdrop-blur-lg p-4 sm:p-1 border border-white/30 flex flex-col gap-2 overflow-hidden"
    >
      
      <div className="flex items-center justify-center gap-2">
        <WaveIcon />
        <h3 
            className="text-sm sm:text-base font-semibold text-gray-800 text-center"
        >
          Level Ketinggian Air
        </h3>
      </div>
      
      <div className="flex flex-col">
        <div className="h-8 rounded-md shadow-inner -mt-1">
          <div 
            className="h-full rounded-md"
            // Mengembalikan gradient status menjadi hijau, kuning, merah
            style={{ background: 'linear-gradient(to right, #10b981, #f59e0b, #ef4444)' }}
          ></div>
        </div>
        
        <div className="grid grid-cols-3 text-center -mx-2 -mt-1">
          {statusLevels.map((level) => (
            <div key={level} className="flex flex-col items-center gap-1.5">
              <ArrowIcon />
              <span 
                className="text-[11px] font-medium text-gray-700"
              >
                {level}
              </span>
            </div>
          ))}
        </div>
      </div>

    </div>
  );
};

export default FloatingLegend;