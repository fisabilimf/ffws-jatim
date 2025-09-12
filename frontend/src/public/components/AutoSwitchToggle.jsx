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
    <div className="flex items-center space-x-4">
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

      {/* Toggle Switch Container */}
      <div className="flex items-center space-x-3">
        {/* Toggle Switch */}
        <div className="relative">
          <button
            onClick={togglePlayPause}
            className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 ${
              isPlaying ? 'bg-green-500' : 'bg-gray-300'
            }`}
            role="switch"
            aria-checked={isPlaying}
            title={isPlaying ? 'Pause Auto Switch' : 'Start Auto Switch'}
          >
            <span
              className={`inline-block h-4 w-4 transform rounded-full bg-white shadow-lg transition-transform duration-200 ease-in-out ${
                isPlaying ? 'translate-x-6' : 'translate-x-1'
              }`}
            />
          </button>
        </div>
        
        {/* Text Label */}
        <span className="text-sm font-medium text-gray-700">
          Auto Switchd
        </span>
      </div>
    </div>
  );
};

export default AutoSwitchToggle;
