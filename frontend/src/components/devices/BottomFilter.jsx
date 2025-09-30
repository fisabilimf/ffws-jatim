import React from 'react';

const BottomFilter = ({ onToggleFilter }) => {
  return (
    <button
      onClick={onToggleFilter}
      className="absolute bottom-185 right-6 z-[1000] w-14 h-14 rounded-full shadow-lg transition-all duration-300 flex items-center justify-center bg-white hover:bg-gray-50 text-blue-500 hover:text-blue-600 hover:shadow-xl transform hover:scale-105 active:scale-95"
      aria-label="Filter"
    >
      <svg xmlns="http://www.w3.org/2000/svg" className="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
        <line x1="7" y1="7" x2="17" y2="7"/>
        <line x1="7" y1="11" x2="17" y2="11"/>
      </svg>
    </button>
  );
};

export default BottomFilter;