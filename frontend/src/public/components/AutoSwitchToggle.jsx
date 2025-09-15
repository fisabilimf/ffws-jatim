import React, { useState, useEffect, useRef } from 'react';

const AutoSwitchToggle = ({ 
  tickerData, 
  onStationChange,
  currentStationIndex,
  interval = 5000 
}) => {
  const [isPlaying, setIsPlaying] = useState(false);
  const [currentIndex, setCurrentIndex] = useState(0);
  const intervalRef = useRef(null);

  // Start auto switch
  const startAutoSwitch = () => {
    if (!tickerData || tickerData.length === 0) return;
    
    setIsPlaying(true);
    intervalRef.current = setInterval(() => {
      setCurrentIndex(prevIndex => {
        const nextIndex = (prevIndex + 1) % tickerData.length;
        const nextStation = tickerData[nextIndex];
        
        // Trigger map movement and station selection
        if (onStationChange) {
          onStationChange(nextStation, nextIndex);
        }
        
        return nextIndex;
      });
    }, interval);
  };

  // Stop auto switch
  const stopAutoSwitch = () => {
    setIsPlaying(false);
    if (intervalRef.current) {
      clearInterval(intervalRef.current);
      intervalRef.current = null;
    }
  };

  // Toggle play/pause
  const togglePlayPause = () => {
    if (isPlaying) {
      stopAutoSwitch();
    } else {
      startAutoSwitch();
    }
  };

  // Cleanup on unmount
  useEffect(() => {
    return () => {
      if (intervalRef.current) {
        clearInterval(intervalRef.current);
      }
    };
  }, []);

  // Sync with external currentStationIndex
  useEffect(() => {
    if (currentStationIndex !== undefined && currentStationIndex !== currentIndex) {
      setCurrentIndex(currentStationIndex);
    }
  }, [currentStationIndex]);

  // Don't render if no data
  if (!tickerData || tickerData.length === 0) return null;

  return (
    <div className="flex items-center space-x-3">
      {/* Current Station Info */}
      {tickerData[currentIndex] && (
        <div className="flex items-center space-x-2 text-sm bg-white/90 backdrop-blur-sm rounded-lg px-3 py-2 shadow-sm">
          <div className={`w-2 h-2 rounded-full ${
            tickerData[currentIndex].status === 'safe' ? 'bg-green-500' :
            tickerData[currentIndex].status === 'warning' ? 'bg-yellow-500' : 'bg-red-500'
          }`}></div>
          <span className="text-gray-700 font-medium">
            {tickerData[currentIndex].name.replace('Stasiun ', '')}
          </span>
          <span className="text-gray-500 text-xs">
            {currentIndex + 1}/{tickerData.length}
          </span>
        </div>
      )}

      {/* Toggle Button */}
      <button
        onClick={togglePlayPause}
        className={`flex items-center justify-center w-12 h-12 rounded-full transition-all duration-200 shadow-lg ${
          isPlaying 
            ? 'bg-red-500 hover:bg-red-600 text-white' 
            : 'bg-blue-500 hover:bg-blue-600 text-white'
        }`}
        title={isPlaying ? 'Pause Auto Switch' : 'Start Auto Switch'}
      >
        {isPlaying ? (
          <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
          </svg>
        ) : (
          <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M8 5v14l11-7z"/>
          </svg>
        )}
      </button>
    </div>
  );
};

export default AutoSwitchToggle;
