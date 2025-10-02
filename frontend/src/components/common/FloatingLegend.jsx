import React from 'react';

const FloatingLegend = () => {
  const statusLevels = ['Aman', 'Waspada', 'Bahaya'];

  // Komponen SVG untuk ikon panah
  const ArrowIcon = () => (
    <svg 
      className="w-3 h-3 text-gray-600" 
      fill="currentColor" 
      viewBox="0 0 20 20" 
      xmlns="http://www.w3.org/2000/svg"
    >
      {/* PERUBAHAN DI SINI: Mengganti path untuk panah ke bawah */}
      <path d="M10 16l6-12H4l6 12z" /> 
    </svg>
  );

  return (
    <div 
      className="w-80 rounded-lg bg-white/20 backdrop-blur-lg p-4 sm:p-1 border border-white/30 flex flex-col gap-2 overflow-hidden"
    >
      
      <div className="flex items-center justify-center gap-2">
        <h3 
            className="text-sm sm:text-base font-semibold text-gray-800 text-center"
        >
          Level Ketinggian Air
        </h3>
      </div>
      
      <div className="flex flex-col gap-3">
        <div className="h-4 rounded-full shadow-inner">
          <div 
            className="h-full rounded-full"
            style={{ background: 'linear-gradient(to right, #10b981, #f59e0b, #ef4444)' }}
          ></div>
        </div>
        
        <div className="grid grid-cols-3 text-center -mx-2">
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