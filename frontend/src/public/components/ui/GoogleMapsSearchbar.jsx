import React, { useState } from 'react';

const GoogleMapsSearchbar = ({ onSearch, placeholder = "Cari di Maps", isSidebarOpen = false }) => {
  const [searchValue, setSearchValue] = useState('');
  const [isFocused, setIsFocused] = useState(false);
  
  const handleSearch = (e) => {
    e.preventDefault();
    if (onSearch) {
      onSearch(searchValue);
    }
  };
  
  return (
    <div className={`fixed top-4 z-[70] transition-all duration-300 ease-in-out ${
      isSidebarOpen 
        ? 'left-4 transform translate-x-0' // Tetap di posisi semula saat sidebar terbuka
        : 'left-4 transform translate-x-0'
    }`}>
      <div className="w-92">
        <form onSubmit={handleSearch} className="relative">
          <div className={`bg-white rounded-lg shadow-lg transition-all duration-200 p-1.5 sm:p-2 ${
            isFocused ? 'shadow-xl ring-2 ring-blue-500' : ''
          }`}>
            <div className="flex items-center py-1 sm:py-1.5">
              {/* Search Icon */}
              <div className="flex-shrink-0 mr-2">
                <svg 
                  className="w-4 h-4 text-gray-400" 
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path 
                    strokeLinecap="round" 
                    strokeLinejoin="round" 
                    strokeWidth={2} 
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" 
                  />
                </svg>
              </div>
              
              {/* Search Input */}
              <input
                type="text"
                value={searchValue}
                onChange={(e) => setSearchValue(e.target.value)}
                onFocus={() => setIsFocused(true)}
                onBlur={() => setIsFocused(false)}
                placeholder={placeholder}
                className="flex-1 text-gray-900 placeholder-gray-500 bg-transparent border-none outline-none text-sm leading-none"
              />
              
              {/* Direction Icon */}
              <div className="flex-shrink-0 ml-2">
                <button
                  type="button"
                  className="p-1 hover:bg-gray-100 rounded-full transition-colors"
                  title="Petunjuk arah"
                >
                  <svg 
                    className="w-4 h-4 text-blue-600" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                  >
                    <path 
                      strokeLinecap="round" 
                      strokeLinejoin="round" 
                      strokeWidth={2} 
                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" 
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  );
};

export default GoogleMapsSearchbar;
