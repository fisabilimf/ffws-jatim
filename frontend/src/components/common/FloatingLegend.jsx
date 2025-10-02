import React from 'react';

const FloatingLegend = () => {
  const statusLevels = ['Aman', 'Waspada', 'Bahaya'];

  const bokehStyle = {
    backgroundImage: `
      radial-gradient(circle at 15% 25%, rgba(0, 0, 0, 0.15), transparent 100%),
      radial-gradient(circle at 80% 45%, rgba(0, 0, 0, 0.15), transparent 100%),
      radial-gradient(circle at 50% 85%, rgba(0, 0, 0, 0.15), transparent 100%)
    `
  };

  return (
    <div 
      // PERUBAHAN DI SINI: class "backdrop-blur-xl" telah dihapus
      className="w-80 bg-gray-900/60 rounded-xl shadow-lg p-4 sm:p-5 border border-white/10 flex flex-col gap-4 overflow-hidden"
      style={bokehStyle}
    >
      
      <div className="flex items-center justify-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" className="w-5 h-5">
          <path fillRule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.25a.75.75 0 0 0-1.5 0v4.59L7.3 9.24a.75.75 0 0 0-1.1 1.02l3.25 3.5a.75.75 0 0 0 1.1 0l3.25-3.5a.75.75 0 1 0-1.1-1.02l-1.95 2.1V6.75Z" clipRule="evenodd" />
        </svg>
        <h3 className="text-sm sm:text-base font-semibold text-gray-200 text-center">
          Level Ketinggian Air
        </h3>
      </div>
      
      <div className="flex flex-col gap-3">
        <div className="h-4 rounded-full shadow-inner bg-gray-900/50">
          <div 
            className="h-full rounded-full"
            style={{ background: 'linear-gradient(to right, #10b981, #f59e0b, #ef4444)' }}
          ></div>
        </div>
        
        <div className="grid grid-cols-3 text-center -mx-2">
          {statusLevels.map((level) => (
            <div key={level} className="flex flex-col items-center gap-1.5">
              <div className="w-1.5 h-1.5 bg-white rounded-full"></div>
              <span className="text-[11px] font-medium text-gray-300">
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