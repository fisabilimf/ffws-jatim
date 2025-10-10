import { useState, useEffect, useRef, useCallback } from 'react';
import { useDevices } from '@/hooks/useDevicesContext';

// Custom hook untuk auto switch logic yang menggunakan DevicesContext sebagai single source of truth
export const useAutoSwitch = ({
  onStationChange,
  interval = Number(import.meta?.env?.VITE_AUTOSWITCH_INTERVAL ?? 10000), 
  stopDelay = Number(import.meta?.env?.VITE_AUTOSWITCH_STOP_DELAY ?? 5000)  
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
    console.log('Devices loading:', devicesLoading);
    console.log('Devices error:', devicesError);
    
    // Hanya menggunakan devices dari context
    if (devicesData && devicesData.length > 0) {
      console.log('Using devices from context');
      return devicesData;
    }
    
    console.log('No devices available from context');
    return [];
  }, [devicesLoading, devicesError]);
  
  // Tick logic using refs to avoid stale closures
  const performTick = useCallback(() => {
    console.log('=== PERFORM TICK CALLED ===');
    console.log('isPlayingRef.current:', isPlayingRef.current);
    console.log('intervalRef.current:', intervalRef.current);
    console.log('pauseUntilRef.current:', pauseUntilRef.current);
    console.log('Date.now():', Date.now());
    
    if (!isPlayingRef.current) {
      console.log('Tick skipped - auto switch not playing');
      return;
    }
    // Skip tick when paused window is active
    if (pauseUntilRef.current && Date.now() < pauseUntilRef.current) {
      console.log('Tick skipped - paused window active');
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
    // Set marker ready setelah jeda animasi - sinkron dengan fly to animation
    // Fly to dengan speed 1.2 biasanya memakan waktu ~1.5 detik
    setTimeout(() => {
      setIsAtMarker(true);
    }, 1500);
    console.log('=== END AUTO SWITCH TICK ===');
  }, [getCurrentDataSource]);
  
  // Start auto switch
  const startAutoSwitch = useCallback(() => {
    console.log('=== START AUTO SWITCH DEBUG ===');
    console.log('devicesLoading:', devicesLoading);
    console.log('devicesError:', devicesError);
    console.log('devices from context:', devices);
    console.log('devicesRef.current:', devicesRef.current);
    
    const dataSource = getCurrentDataSource();
    console.log('dataSource from getCurrentDataSource:', dataSource);
    console.log('dataSource length:', dataSource?.length || 0);
    
    if (!dataSource || dataSource.length === 0) {
      console.warn('Cannot start auto switch: No data available. Will start when devices arrive.');
      console.log('Setting pendingStartRef.current = true');
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
        }, 1500);
      } catch (error) {
        console.error('Immediate switch error:', error);
      }
    }
    
    // Set playing state first
    setIsPlaying(true);
    
    // Start accurate timer loop (drift-resistant)
    const runTick = () => {
      console.log('=== RUN TICK CALLED ===');
      console.log('Current time:', Date.now());
      console.log('Next tick time:', nextTickTimeRef.current);
      console.log('Interval:', interval);
      console.log('isPlayingRef.current:', isPlayingRef.current);
      
      performTick();
      
      if (isPlayingRef.current) {
        nextTickTimeRef.current += interval;
        const delay = Math.max(0, nextTickTimeRef.current - Date.now());
        console.log('Setting next tick in:', delay, 'ms');
        intervalRef.current = setTimeout(runTick, delay);
      } else {
        console.log('Auto switch stopped, not scheduling next tick');
      }
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
  }, [getCurrentDataSource, currentIndex, interval, performTick]);

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
    
    if (pauseTimeoutRef.current) {
      clearTimeout(pauseTimeoutRef.current);
      pauseTimeoutRef.current = null;
    }
    
    setIsPlaying(false);
    isPlayingRef.current = false;
    pendingStartRef.current = false;
    setIsPendingStop(false);
    setIsAtMarker(true);
    pauseUntilRef.current = 0;
    nextTickTimeRef.current = 0;
    
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
      console.log('=== USER INTERACTION DETECTED ===');
      console.log('isPlayingRef.current:', isPlayingRef.current);
      console.log('event.detail:', event.detail);
      
      if (!isPlayingRef.current) {
        console.log('Auto switch not playing, ignoring user interaction');
        return;
      }
      
      console.log('User interaction detected - handled by AutoSwitchToggle for pause/resume:', event.detail);
      // Biarkan AutoSwitchToggle yang handle user interaction
      // Jangan langsung stop auto switch untuk tampilan publik
    };

    const handlePauseAutoSwitch = (event) => {
      console.log('=== PAUSE AUTO SWITCH EVENT ===');
      console.log('isPlayingRef.current:', isPlayingRef.current);
      console.log('event.detail:', event.detail);
      
      if (!isPlayingRef.current) {
        console.log('Auto switch not playing, ignoring pause event');
        return;
      }
      
      console.log('Pause auto switch event received - handled by AutoSwitchToggle:', event.detail);
      // Biarkan AutoSwitchToggle yang handle pause/resume logic
      // Jangan langsung stop auto switch
    };

    const handleResumeAutoSwitch = (event) => {
      console.log('=== RESUME AUTO SWITCH EVENT ===');
      console.log('event.detail:', event.detail);
      console.log('Resume auto switch event received from AutoSwitchToggle:', event.detail);
      
      // Jika auto switch sedang playing, tidak perlu resume
      if (isPlayingRef.current) {
        console.log('Auto switch already playing, no need to resume');
        return;
      }
      
      // Jika ada pending start, jalankan start
      if (pendingStartRef.current) {
        console.log('Resuming from pending start');
        pendingStartRef.current = false;
        startAutoSwitch();
      }
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
    isAutoSwitchOn: isPlaying,
    
    // Debug info
    debugInfo: {
      devicesCount: currentDataSource.length,
      isLoading: devicesLoading,
      hasError: !!devicesError,
      errorMessage: devicesError,
      isPlaying: isPlaying,
      currentIndex: currentIndex,
      isPendingStop: isPendingStop,
      isAtMarker: isAtMarker
    }
  };
};