import React, { useState, useEffect, useRef } from 'react';

const AutoSwitchToggle = ({ 
  tickerData, 
  onStationChange,
  currentStationIndex,
  onAutoSwitchToggle,
  interval = 5000 
}) => {
  const [isPlaying, setIsPlaying] = useState(false);
  const [currentIndex, setCurrentIndex] = useState(0);
  const intervalRef = useRef(null);
  
  // Start auto switch
  const startAutoSwitch = () => {
    if (!tickerData || tickerData.length === 0) return;
    
    setIsPlaying(true);
    
    // Dispatch custom event untuk menutup sidebar dan detail panel
    document.dispatchEvent(new CustomEvent('autoSwitchActivated', { 
      detail: { active: true } 
    }));
    
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
    
    // Dispatch custom event saat auto switch dimatikan
    document.dispatchEvent(new CustomEvent('autoSwitchDeactivated', { 
      detail: { active: false } 
    }));
  };
  
  // Toggle play/pause
  const togglePlayPause = () => {
    if (isPlaying) {
      stopAutoSwitch();
      if (onAutoSwitchToggle) {
        onAutoSwitchToggle(false);
      }
    } else {
      startAutoSwitch();
      if (onAutoSwitchToggle) {
        onAutoSwitchToggle(true);
      }
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
  
  // Add event listener for user interactions
  useEffect(() => {
    const handleUserInteraction = (event) => {
      if (isPlaying) {
        console.log('User interaction detected, stopping auto switch:', event.detail);
        stopAutoSwitch();
      }
    };
    
    // Listen for custom event from other components
    document.addEventListener('userInteraction', handleUserInteraction);
    
    // Cleanup
    return () => {
      document.removeEventListener('userInteraction', handleUserInteraction);
    };
  }, [isPlaying]);
  
  // Selalu render komponen; nonaktifkan toggle jika tidak ada data
  const hasData = tickerData && tickerData.length > 0;
  
  return (
    <div className="flex items-center justify-between w-full">
      <span className="text-xs sm:text-sm font-semibold text-gray-800">Auto Switch</span>
      <button
        onClick={togglePlayPause}
        disabled={!hasData}
        className={`relative inline-flex items-center transition-colors focus:outline-none select-none w-14 h-8 rounded-full shadow ${
          isPlaying ? 'bg-green-500' : 'bg-gray-300'
        } ${!hasData ? 'opacity-50 cursor-not-allowed' : ''}`}
        title={isPlaying ? 'Pause Auto Switch' : 'Start Auto Switch'}
      >
        <span
          className={`inline-block w-6 h-6 transform bg-white rounded-full shadow transition-transform ${
            isPlaying ? 'translate-x-6' : 'translate-x-1'
          }`}
        />
      </button>
    </div>
  );
};

export default AutoSwitchToggle;