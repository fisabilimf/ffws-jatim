import React, { useState } from 'react';

const GoogleMapsSearchbar = ({ onSearch, placeholder = "Cari di Maps" }) => {
  const [searchValue, setSearchValue] = useState('');
  const [isFocused, setIsFocused] = useState(false);

  const handleSearch = (e) => {
    e.preventDefault();
    if (onSearch) {
      onSearch(searchValue);
    }
  };

  return (
    <div className="absolute top-4 left-4 right-4 z-10">
      <div className="max-w-2xl mx-auto">
        <form onSubmit={handleSearch} className="relative">
          <div className={`bg-white rounded-lg shadow-lg transition-all duration-200 ${
            isFocused ? 'shadow-xl ring-2 ring-blue-500' : ''
          }`}>
            <div className="flex items-center px-3 py-2.5 sm:px-4 sm:py-3">
              {/* Search Icon */}
              <div className="flex-shrink-0 mr-3">
                <svg 
                  className="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" 
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
                className="flex-1 text-gray-900 placeholder-gray-500 bg-transparent border-none outline-none text-sm sm:text-base"
              />
              
              {/* Direction Icon */}
              <div className="flex-shrink-0 ml-3 sm:ml-3">
                <button
                  type="button"
                  className="p-1 hover:bg-gray-100 rounded-full transition-colors"
                  title="Petunjuk arah"
                >
                  <svg 
                    className="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" 
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