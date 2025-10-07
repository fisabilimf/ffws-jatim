import { useState, useEffect, useRef, useCallback } from 'react';
import { useDevices } from '@/hooks/useAppContext';

// Custom hook untuk auto switch logic yang menggunakan DevicesContext sebagai single source of truth
export const useAutoSwitch = ({
  onStationChange,
  interval = 8000, // 8 detik untuk memberikan waktu melihat station detail
  stopDelay = 5000
}) => {
  // Get devices from context - single source of truth
  const { devices, loading: devicesLoading, error: devicesError } = useDevices();
  
  // State management
  const [isPlaying, setIsPlaying] = useState(false);
  const [currentIndex, setCurrentIndex] = useState(0);
  const [isPendingStop, setIsPendingStop] = useState(false);
  const [isAtMarker, setIsAtMarker] = useState(true);
  
  // Refs
  const intervalRef = useRef(null);
  const nextTickTimeRef = useRef(0);
  const devicesRef = useRef(devices);
  const isPlayingRef = useRef(false);
  const currentIndexRef = useRef(0);
  const onStationChangeRef = useRef(onStationChange);
  const stopTimeoutRef = useRef(null);
  const pendingStartRef = useRef(false);
  const pauseUntilRef = useRef(0);
  const pauseTimeoutRef = useRef(null);
  
  // Update refs when data changes
  useEffect(() => {
    devicesRef.current = devices || [];
  }, [devices]);

  useEffect(() => {
    isPlayingRef.current = isPlaying;
  }, [isPlaying]);

  useEffect(() => {
    currentIndexRef.current = currentIndex;
  }, [currentIndex]);

  useEffect(() => {
    onStationChangeRef.current = onStationChange;
  }, [onStationChange]);
  
  // Get current data source - hanya menggunakan devices dari context
  const getCurrentDataSource = useCallback(() => {
    const devicesData = devicesRef.current;
    
    console.log('=== GET CURRENT DATA SOURCE ===');
    console.log('Devices from context:', devicesData?.length || 0);
    
    // Hanya menggunakan devices dari context
    if (devicesData && devicesData.length > 0) {
      console.log('Using devices from context');
      return devicesData;
    }
    
    console.log('No devices available from context');
    return [];
  }, []);
  
  // Tick logic using refs to avoid stale closures
  const performTick = useCallback(() => {
    if (!isPlayingRef.current) {
      console.log('Tick skipped - auto switch not playing');
      return;
    }
    // Skip tick when paused window is active
    if (pauseUntilRef.current && Date.now() < pauseUntilRef.current) {
      return;
    }
    const dataSource = getCurrentDataSource();
    if (!dataSource || dataSource.length === 0) {
      console.warn('No data available for auto switch');
      return;
    }
    const nextIndex = (currentIndexRef.current + 1) % dataSource.length;
    const nextDevice = dataSource[nextIndex];
    console.log('=== AUTO SWITCH TICK ===');
    console.log('Current index:', currentIndexRef.current);
    console.log('Next index:', nextIndex);
    console.log('Data source length:', dataSource.length);
    console.log('Next device:', nextDevice?.name || nextDevice?.device_name || nextDevice?.station_name);
    if (!nextDevice) {
      console.error('Next device is undefined at index:', nextIndex);
      return;
    }
    setCurrentIndex(nextIndex);
    setIsAtMarker(false);
    // Delegasikan perpindahan peta ke handler eksternal (Layout)
    if (onStationChangeRef.current) {
      try {
        onStationChangeRef.current(nextDevice, nextIndex);
      } catch (error) {
        console.error('Error notifying parent:', error);
      }
    }
    // Set marker ready setelah jeda animasi
    setTimeout(() => {
      setIsAtMarker(true);
    }, 1500);
    console.log('=== END AUTO SWITCH TICK ===');
  }, [getCurrentDataSource]);
  
  // Start auto switch
  const startAutoSwitch = useCallback(() => {
    const dataSource = getCurrentDataSource();
    
    if (!dataSource || dataSource.length === 0) {
      console.warn('Cannot start auto switch: No data available. Will start when devices arrive.');
      pendingStartRef.current = true;
      return;
    }
    
    if (intervalRef.current) {
      console.log('Auto switch already running');
      return;
    }
    
    console.log('=== STARTING AUTO SWITCH ===');
    console.log('Data source length:', dataSource.length);
    console.log('Starting with index:', currentIndex);
    
    // Clear any pending stop
    if (stopTimeoutRef.current) {
      clearTimeout(stopTimeoutRef.current);
      stopTimeoutRef.current = null;
      setIsPendingStop(false);
    }
    
    // Immediately switch to current device
    const currentDevice = dataSource[currentIndex];
    if (currentDevice && typeof window.mapboxAutoSwitch === 'function') {
      try {
        console.log('Performing immediate switch...');
        window.mapboxAutoSwitch(currentDevice, currentIndex);
        
        setTimeout(() => {
          setIsAtMarker(true);
        }, 1000);
      } catch (error) {
        console.error('Immediate switch error:', error);
      }
    }
    
    // Set playing state first
    setIsPlaying(true);
    
    // Start accurate timer loop (drift-resistant)
    const runTick = () => {
      performTick();
      nextTickTimeRef.current += interval;
      const delay = Math.max(0, nextTickTimeRef.current - Date.now());
      intervalRef.current = setTimeout(runTick, delay);
    };

    nextTickTimeRef.current = Date.now() + interval;
    intervalRef.current = setTimeout(runTick, interval);
    setIsAtMarker(false);
    
    // Dispatch activation event
    document.dispatchEvent(new CustomEvent('autoSwitchActivated', {
      detail: { 
        active: true, 
        currentIndex, 
        deviceCount: dataSource.length 
      }
    }));
    
    console.log('Auto switch started successfully');
    console.log('=== AUTO SWITCH STARTED ===');
  }, [getCurrentDataSource, currentIndex, interval, performTick, isPlaying]);

  // Attempt to start when devices arrive if there was a pending start
  useEffect(() => {
    const hasDevicesNow = Array.isArray(devicesRef.current) && devicesRef.current.length > 0;
    if (pendingStartRef.current && hasDevicesNow && !intervalRef.current && !isPlayingRef.current) {
      console.log('Pending auto switch start detected and devices available. Starting now...');
      pendingStartRef.current = false;
      startAutoSwitch();
    }
  }, [startAutoSwitch]);
  
  // Stop auto switch immediately
  const stopAutoSwitchImmediately = useCallback(() => {
    console.log('Stopping auto switch immediately');
    
    if (intervalRef.current) {
      clearTimeout(intervalRef.current);
      intervalRef.current = null;
    }
    
    if (stopTimeoutRef.current) {
      clearTimeout(stopTimeoutRef.current);
      stopTimeoutRef.current = null;
    }
    
    setIsPlaying(false);
    isPlayingRef.current = false;
    pendingStartRef.current = false;
    setIsPendingStop(false);
    setIsAtMarker(true);
    
    // Dispatch deactivation event
    document.dispatchEvent(new CustomEvent('autoSwitchDeactivated', {
      detail: { active: false }
    }));
  }, []);
  
  // Stop auto switch with delay
  const stopAutoSwitch = useCallback(() => {
    // Immediate OFF
    if (pauseTimeoutRef.current) {
      clearTimeout(pauseTimeoutRef.current);
      pauseTimeoutRef.current = null;
    }
    pauseUntilRef.current = 0;
    setIsPendingStop(false);
    stopAutoSwitchImmediately();
  }, [stopAutoSwitchImmediately]);
  
  // Toggle play/pause
  const togglePlayPause = useCallback(() => {
    console.log('Toggle play/pause called. Current state - isPlaying:', isPlaying, 'isPendingStop:', isPendingStop);
    
    if (isPendingStop) {
      // Cancel pause immediately
      if (pauseTimeoutRef.current) {
        clearTimeout(pauseTimeoutRef.current);
        pauseTimeoutRef.current = null;
      }
      pauseUntilRef.current = 0;
      setIsPendingStop(false);
      console.log('Auto switch pause cancelled, continuing');
      return;
    }
    
    if (isPlaying) {
      // Convert stop action to pause
      stopAutoSwitch();
    } else {
      const dataSource = getCurrentDataSource();
      if (!dataSource || dataSource.length === 0) {
        console.warn('Cannot start auto switch: No data available. Will start when devices arrive.');
        pendingStartRef.current = true;
        return;
      }
      startAutoSwitch();
    }
  }, [isPlaying, isPendingStop, startAutoSwitch, stopAutoSwitch]);
  
  // Handle user interaction and pause/resume events
  useEffect(() => {
    const handleUserInteraction = (event) => {
      if (!isPlayingRef.current) return;
      console.log('User interaction detected, turning OFF auto switch:', event.detail);
      stopAutoSwitchImmediately();
    };

    const handlePauseAutoSwitch = (event) => {
      console.log('Pause auto switch event received (interpreted as OFF):', event.detail);
      if (!isPlayingRef.current) return;
      stopAutoSwitchImmediately();
    };

    const handleResumeAutoSwitch = (event) => {
      console.log('Resume auto switch event received - ignored (requires manual toggle ON):', event.detail);
    };

    document.addEventListener('userInteraction', handleUserInteraction);
    document.addEventListener('pauseAutoSwitch', handlePauseAutoSwitch);
    document.addEventListener('resumeAutoSwitch', handleResumeAutoSwitch);
    
    return () => {
      document.removeEventListener('userInteraction', handleUserInteraction);
      document.removeEventListener('pauseAutoSwitch', handlePauseAutoSwitch);
      document.removeEventListener('resumeAutoSwitch', handleResumeAutoSwitch);
    };
  }, [isPlaying, isPendingStop, stopAutoSwitch]);
  
  // Cleanup on unmount
  useEffect(() => {
    return () => {
      if (intervalRef.current) {
        clearTimeout(intervalRef.current);
      }
      if (stopTimeoutRef.current) {
        clearTimeout(stopTimeoutRef.current);
      }
    };
  }, []);
  
  // Get current data source for external use
  const currentDataSource = getCurrentDataSource();
  const hasData = currentDataSource.length > 0;
  
  return {
    // State
    isPlaying,
    currentIndex,
    isPendingStop,
    isAtMarker,
    hasData,
    error: devicesError,
    isLoadingDevices: devicesLoading,
    
    // Data
    devicesData: currentDataSource,
    
    // Actions
    startAutoSwitch,
    stopAutoSwitch: stopAutoSwitchImmediately,
    togglePlayPause,
    
    // For compatibility
    isAutoSwitchOn: isPlaying
  };
};