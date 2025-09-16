import React, { useState } from 'react';

const FloatingArrow = ({ 
  isVisible = true, 
  onToggle, 
  isOpen = false,
  position = "right",
  className = ""
}) => {
  const [isHovered, setIsHovered] = useState(false);

  if (!isVisible) return null;

  const getPositionClasses = () => {
    switch (position) {
      case 'right':
        return 'right-4 top-1/2 -translate-y-1/2';
      case 'left':
        return 'left-4 top-1/2 -translate-y-1/2';
      case 'top':
        return 'top-4 left-1/2 -translate-x-1/2';
      case 'bottom':
        return 'bottom-4 left-1/2 -translate-x-1/2';
      default:
        return 'right-4 top-1/2 -translate-y-1/2';
    }
  };

  const getArrowDirection = () => {
    // Arrow berputar 180 derajat ketika panel terbuka untuk menunjukkan arah menutup
    return isOpen ? 'rotate-180' : 'rotate-0';
  };


  return (
    <button
      onClick={onToggle}
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => setIsHovered(false)}
      className={`
        fixed z-[70] 
        ${getPositionClasses()}
        w-12 h-12 
        bg-white 
        border-2 border-gray-300 
        rounded-full 
        shadow-lg 
        hover:shadow-xl 
        transition-all duration-300 ease-in-out
        flex items-center justify-center
        group
        ${isHovered ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-white'}
        ${className}
      `}
    >
      <svg 
        className={`
          w-6 h-6 
          text-gray-600 
          group-hover:text-blue-600 
          transition-all duration-300 ease-in-out
          ${getArrowDirection()}
        `}
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path 
          strokeLinecap="round" 
          strokeLinejoin="round" 
          strokeWidth={2} 
          d="M9 5l7 7-7 7" 
        />
      </svg>
    </button>
  );
};

export default FloatingArrow;
