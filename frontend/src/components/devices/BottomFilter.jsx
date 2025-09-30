import React from 'react';

const BottomFilter = ({ onToggleFilter }) => {
  return (
    <button
      onClick={onToggleFilter}
      className="absolute bottom-186 right-10 transform -translate-x-2 z-10 px-4 py-3.5 rounded-lg shadow-lg transition-colors flex items-center bg-white hover:bg-gray-100 text-gray-800"
    >
      <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
        <path fillRule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clipRule="evenodd" />
      </svg>
      Filter
    </button>
  );
};

export default BottomFilter;