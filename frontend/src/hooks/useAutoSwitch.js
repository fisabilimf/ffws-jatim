import { useState, useEffect, useRef, useCallback, useReducer } from 'react';
import { fetchDevices } from '@/services/devices';

// Initial state untuk reducer
const initialState = {
  isPlaying: false,
  currentIndex: 0,
  devicesData: [],
  isLoadingDevices: false,
  error: null,
  tickCounter: 0,
  lastInteractionTime: null,
  isUserInteracting: false,
  isPaused: false,
  pauseTimer: null,
  autoResumeDelay: 5000 // 5 detik untuk auto resume
};

// Reducer untuk mengelola state
const autoSwitchReducer = (state, action) => {
  switch (action.type) {
    case 'SET_PLAYING':
      return { ...state, isPlaying: action.payload };
    case 'SET_CURRENT_INDEX':
      return { ...state, currentIndex: action.payload };
    case 'SET_DEVICES_DATA':
      return { ...state, devicesData: action.payload };
    case 'SET_LOADING':
      return { ...state, isLoadingDevices: action.payload };
    case 'SET_ERROR':
      return { ...state, error: action.payload };
    case 'INCREMENT_TICK':
      return { ...state, tickCounter: state.tickCounter + 1 };
    case 'RESET_TICK':
      return { ...state, tickCounter: 0 };
    case 'SET_LAST_INTERACTION':
      return { ...state, lastInteractionTime: action.payload };
    case 'SET_USER_INTERACTING':
      return { ...state, isUserInteracting: action.payload };
    case 'SET_PAUSED':
      return { ...state, isPaused: action.payload };
    case 'SET_PAUSE_TIMER':
      return { ...state, pauseTimer: action.payload };
    case 'RESET':
      return initialState;
    default:
      return state;
  }
};

// Custom hook untuk auto switch logic
export const useAutoSwitch = ({
  tickerData,
  onStationChange,
  interval = 5000,
  fetchInterval = 10000,
  isAutoSwitchOn = false
}) => {
  const [state, dispatch] = useReducer(autoSwitchReducer, initialState);
  const intervalRef = useRef(null);
  const fetchIntervalRef = useRef(null);
  const tickerDataRef = useRef(tickerData);
  const devicesDataRef = useRef(state.devicesData);
  
  // Update refs when data changes
  useEffect(() => {
    tickerDataRef.current = tickerData;
  }, [tickerData]);
  
  useEffect(() => {
    devicesDataRef.current = state.devicesData;
  }, [state.devicesData]);
  
  // Fetch device data dengan retry mechanism
  const fetchDeviceData = useCallback(async (retryCount = 0) => {
    const maxRetries = 3;
    
    try {
      dispatch({ type: 'SET_LOADING', payload: true });
      dispatch({ type: 'SET_ERROR', payload: null });
      
      console.log(`=== FETCHING DEVICE DATA ===`);
      console.log(`Attempt ${retryCount + 1}/${maxRetries + 1}`);
      
      const devices = await fetchDevices();
      
      console.log('Raw API response:', devices);
      
      if (!devices || !Array.isArray(devices)) {
        throw new Error('Invalid device data received from API');
      }
      
      console.log(`API returned ${devices.length} devices`);
      
      // Validasi struktur data devices
      const validDevices = devices.filter(device => {
        const hasName = device && (device.name || device.device_name || device.station_name);
        const hasCoordinates = device && ((device.latitude && device.longitude) || device.coordinates);
        const isValid = hasName && hasCoordinates;
        
        if (!isValid) {
          console.log('Invalid device filtered out:', {
            name: device?.name || device?.device_name || device?.station_name,
            hasName,
            hasCoordinates,
            coordinates: device?.coordinates,
            latitude: device?.latitude,
            longitude: device?.longitude
          });
        }
        
        return isValid;
      });
      
      console.log(`Successfully fetched ${devices.length} devices`);
      console.log(`Valid devices: ${validDevices.length}`);
      console.log('Sample valid devices:', validDevices.slice(0, 2));
      
      if (validDevices.length === 0) {
        throw new Error('No valid devices found - all devices missing name or coordinates');
      }
      
      dispatch({ type: 'SET_DEVICES_DATA', payload: validDevices });
      console.log('Device data stored in state successfully');
      console.log('=== DEVICE DATA FETCH COMPLETED ===');
      
    } catch (error) {
      console.error(`=== DEVICE DATA FETCH ERROR ===`);
      console.error(`Attempt ${retryCount + 1} failed:`, error);
      
      if (retryCount < maxRetries) {
        const retryDelay = (retryCount + 1) * 2000;
        console.log(`Retrying in ${retryDelay}ms...`);
        setTimeout(() => {
          fetchDeviceData(retryCount + 1);
        }, retryDelay);
      } else {
        console.error(`All ${maxRetries + 1} attempts failed`);
        dispatch({ type: 'SET_ERROR', payload: `Failed to fetch devices after ${maxRetries + 1} attempts: ${error.message}` });
      }
      console.error('=== END DEVICE DATA FETCH ERROR ===');
    } finally {
      if (retryCount === 0 || retryCount === maxRetries) {
        dispatch({ type: 'SET_LOADING', payload: false });
        console.log('Loading state set to false');
      }
    }
  }, []);
  
  // Tick function untuk cycling through devices dengan error handling yang robust
  const tick = useCallback(() => {
    if (!state.isPlaying) {
      console.log('Tick skipped - auto switch not playing');
      return;
    }
    
    dispatch({ type: 'INCREMENT_TICK' });
    
    console.log('=== AUTO SWITCH TICK ===');
    console.log('Tick counter:', state.tickCounter + 1);
    console.log('Current index:', state.currentIndex);
    console.log('Devices data length:', devicesDataRef.current.length);
    console.log('Ticker data length:', tickerDataRef.current?.length || 0);
    
    const dataSource = devicesDataRef.current.length > 0 
      ? devicesDataRef.current 
      : tickerDataRef.current;
    
    console.log('Using data source:', dataSource === devicesDataRef.current ? 'devices' : 'tickerData');
    console.log('Data source length:', dataSource?.length || 0);
    
    if (!dataSource || dataSource.length === 0) {
      console.warn('No data available for auto switch');
      dispatch({ type: 'SET_ERROR', payload: 'No data available for auto switch' });
      return;
    }
    
    const nextIndex = (state.currentIndex + 1) % dataSource.length;
    const nextDevice = dataSource[nextIndex];
    
    console.log('Next device name:', nextDevice?.name || nextDevice?.device_name || nextDevice?.station_name || 'undefined');
    console.log('Next device index:', nextIndex);
    console.log('Next device coordinates:', nextDevice?.coordinates || `${nextDevice?.latitude}, ${nextDevice?.longitude}`);
    console.log('Next device full data:', nextDevice);
    
    if (!nextDevice) {
      console.error('Next device is undefined at index:', nextIndex);
      dispatch({ type: 'SET_ERROR', payload: `Device not found at index ${nextIndex}` });
      return;
    }
    
    dispatch({ type: 'SET_CURRENT_INDEX', payload: nextIndex });
    
    // Call mapboxAutoSwitch dengan retry mechanism
    if (typeof window.mapboxAutoSwitch === 'function') {
      console.log('Calling mapboxAutoSwitch function...');
      try {
        window.mapboxAutoSwitch(nextDevice, nextIndex);
        console.log('mapboxAutoSwitch called successfully');
      } catch (error) {
        console.error('Error switching map:', error);
        dispatch({ type: 'SET_ERROR', payload: `Map switch error: ${error.message}` });
        
        // Retry mechanism - coba lagi setelah delay singkat
        console.log('Retrying mapboxAutoSwitch in 1 second...');
        setTimeout(() => {
          if (typeof window.mapboxAutoSwitch === 'function') {
            try {
              console.log('Retrying mapboxAutoSwitch...');
              window.mapboxAutoSwitch(nextDevice, nextIndex);
              console.log('Retry successful');
              dispatch({ type: 'SET_ERROR', payload: null }); // Clear error on retry success
            } catch (retryError) {
              console.error('Retry failed:', retryError);
              dispatch({ type: 'SET_ERROR', payload: `Retry failed: ${retryError.message}` });
            }
          } else {
            console.error('mapboxAutoSwitch function not available for retry');
          }
        }, 1000);
      }
    } else {
      console.warn('mapboxAutoSwitch function not available on window object');
      dispatch({ type: 'SET_ERROR', payload: 'Map function not available' });
    }
    
    // Notify parent
    if (onStationChange) {
      try {
        console.log('Notifying parent component...');
        onStationChange(nextDevice, nextIndex);
        console.log('Parent notification successful');
      } catch (error) {
        console.error('Error notifying parent:', error);
        dispatch({ type: 'SET_ERROR', payload: `Parent notification error: ${error.message}` });
      }
    }
    
    console.log('=== END AUTO SWITCH TICK ===');
  }, [state.isPlaying, state.currentIndex, state.tickCounter, onStationChange]);
  
  // Start auto switch
  const startAutoSwitch = useCallback(async () => {
    if (intervalRef.current) {
      console.log('Auto switch already running, skipping start');
      return;
    }
    
    console.log('=== STARTING AUTO SWITCH ===');
    console.log('Current devices data length:', state.devicesData?.length || 0);
    console.log('Current ticker data length:', tickerDataRef.current?.length || 0);
    console.log('Current index:', state.currentIndex);
    console.log('MapboxAutoSwitch available:', typeof window.mapboxAutoSwitch === 'function');
    
    // Pastikan devices data sudah di-fetch sebelum memulai
    if (state.devicesData.length === 0) {
      console.log('No devices data available, fetching...');
      try {
        await fetchDeviceData();
        // Tunggu sebentar untuk state update
        await new Promise(resolve => setTimeout(resolve, 100));
        console.log('Device data fetch completed');
      } catch (error) {
        console.error('Failed to fetch devices before starting:', error);
      }
    }
    
    const dataSource = state.devicesData.length > 0 
      ? state.devicesData 
      : tickerDataRef.current;
    
    console.log('Using data source:', dataSource === state.devicesData ? 'devices' : 'tickerData');
    console.log('Data source length:', dataSource?.length || 0);
    console.log('Data source sample:', dataSource?.slice(0, 2));
    
    if (!dataSource || dataSource.length === 0) {
      console.warn('Cannot start: No data available');
      dispatch({ type: 'SET_ERROR', payload: 'No data available to start auto switch' });
      return;
    }
    
    // Start fetch interval
    console.log('Starting fetch interval with interval:', fetchInterval);
    fetchIntervalRef.current = setInterval(() => {
      console.log('Fetch interval triggered - fetching device data');
      fetchDeviceData();
    }, fetchInterval);
    
    // Immediately switch to first device (no delay)
    const firstDevice = dataSource[state.currentIndex];
    console.log('First device for immediate switch:', firstDevice?.name || firstDevice?.device_name || firstDevice?.station_name);
    console.log('First device coordinates:', firstDevice?.coordinates || `${firstDevice?.latitude}, ${firstDevice?.longitude}`);
    
    if (firstDevice && typeof window.mapboxAutoSwitch === 'function') {
      try {
        console.log('Performing immediate switch...');
        window.mapboxAutoSwitch(firstDevice, state.currentIndex);
        console.log(`Immediate switch successful to: ${firstDevice.name || firstDevice.device_name || firstDevice.station_name}`);
      } catch (error) {
        console.error('Immediate switch error:', error);
        dispatch({ type: 'SET_ERROR', payload: `Immediate switch error: ${error.message}` });
      }
    } else {
      console.warn('Cannot perform immediate switch - missing device or mapboxAutoSwitch function');
      if (!firstDevice) console.warn('First device is missing');
      if (typeof window.mapboxAutoSwitch !== 'function') console.warn('mapboxAutoSwitch function not available');
    }
    
    // Start main interval with fresh timing
    console.log('Starting main interval with interval:', interval);
    intervalRef.current = setInterval(() => {
      console.log('Interval triggered - checking if should tick');
      if (!state.isPlaying) {
        console.log('Auto switch stopped, clearing interval');
        clearInterval(intervalRef.current);
        intervalRef.current = null;
        return;
      }
      console.log('Calling tick function...');
      tick();
    }, interval);
    
    dispatch({ type: 'SET_PLAYING', payload: true });
    dispatch({ type: 'RESET_TICK' });
    
    console.log('Auto switch started successfully');
    console.log('=== AUTO SWITCH STARTED ===');
    
    // Dispatch event
    document.dispatchEvent(
      new CustomEvent('autoSwitchActivated', {
        detail: { 
          active: true, 
          currentIndex: state.currentIndex, 
          deviceCount: dataSource.length 
        }
      })
    );
  }, [state.devicesData, state.isPlaying, interval, fetchInterval, fetchDeviceData, tick, state.currentIndex]);
  
  // Pause auto switch sementara (untuk user interaction)
  const pauseAutoSwitch = useCallback(() => {
    if (!state.isPlaying || state.isPaused) return;
    
    // Clear main interval tapi keep fetch interval
    if (intervalRef.current) {
      clearInterval(intervalRef.current);
      intervalRef.current = null;
    }
    
    dispatch({ type: 'SET_PAUSED', payload: true });
    dispatch({ type: 'SET_LAST_INTERACTION', payload: Date.now() });
    dispatch({ type: 'SET_USER_INTERACTING', payload: true });
    
    // Set timer untuk auto resume
    const timer = setTimeout(() => {
      console.log('Auto resuming auto switch after user interaction');
      resumeAutoSwitch();
    }, state.autoResumeDelay);
    
    dispatch({ type: 'SET_PAUSE_TIMER', payload: timer });
    
    // Dispatch event
    document.dispatchEvent(
      new CustomEvent('autoSwitchPaused', {
        detail: { 
          paused: true, 
          resumeIn: state.autoResumeDelay,
          reason: 'user_interaction'
        }
      })
    );
  }, [state.isPlaying, state.isPaused, state.autoResumeDelay]);

  // Resume auto switch setelah pause
  const resumeAutoSwitch = useCallback(() => {
    if (!state.isPlaying || !state.isPaused) return;
    
    const dataSource = state.devicesData.length > 0 
      ? state.devicesData 
      : tickerDataRef.current;
    
    if (!dataSource || dataSource.length === 0) {
      console.warn('Cannot resume: No data available');
      return;
    }
    
    // Clear pause timer
    if (state.pauseTimer) {
      clearTimeout(state.pauseTimer);
      dispatch({ type: 'SET_PAUSE_TIMER', payload: null });
    }
    
    // Immediately switch to current device
    const currentDevice = dataSource[state.currentIndex];
    if (currentDevice && typeof window.mapboxAutoSwitch === 'function') {
      try {
        window.mapboxAutoSwitch(currentDevice, state.currentIndex);
        console.log(`Resumed switch to: ${currentDevice.name || currentDevice.device_name || currentDevice.station_name}`);
      } catch (error) {
        console.error('Resume switch error:', error);
      }
    }
    
    // Restart main interval
    intervalRef.current = setInterval(() => {
      if (!state.isPlaying || state.isPaused) {
        clearInterval(intervalRef.current);
        intervalRef.current = null;
        return;
      }
      tick();
    }, interval);
    
    dispatch({ type: 'SET_PAUSED', payload: false });
    dispatch({ type: 'SET_USER_INTERACTING', payload: false });
    
    // Dispatch event
    document.dispatchEvent(
      new CustomEvent('autoSwitchResumed', {
        detail: { 
          resumed: true, 
          currentIndex: state.currentIndex,
          deviceCount: dataSource.length
        }
      })
    );
  }, [state.isPlaying, state.isPaused, state.devicesData, state.currentIndex, interval, tick]);

  // Stop auto switch completely
  const stopAutoSwitch = useCallback(() => {
    if (intervalRef.current) {
      clearInterval(intervalRef.current);
      intervalRef.current = null;
    }
    
    if (fetchIntervalRef.current) {
      clearInterval(fetchIntervalRef.current);
      fetchIntervalRef.current = null;
    }
    
    // Clear pause timer if exists
    if (state.pauseTimer) {
      clearTimeout(state.pauseTimer);
      dispatch({ type: 'SET_PAUSE_TIMER', payload: null });
    }
    
    dispatch({ type: 'SET_PLAYING', payload: false });
    dispatch({ type: 'SET_PAUSED', payload: false });
    dispatch({ type: 'SET_LAST_INTERACTION', payload: Date.now() });
    dispatch({ type: 'SET_USER_INTERACTING', payload: true });
    
    // Dispatch event
    document.dispatchEvent(
      new CustomEvent('autoSwitchDeactivated', {
        detail: { active: false }
      })
    );
  }, [state.pauseTimer]);
  
  // Restart auto switch dengan delay yang tepat
  const restartAutoSwitch = useCallback(() => {
    if (intervalRef.current) return;
    
    const dataSource = state.devicesData.length > 0 
      ? state.devicesData 
      : tickerDataRef.current;
    
    if (!dataSource || dataSource.length === 0) {
      console.warn('Cannot restart: No data available');
      return;
    }
    
    // Calculate delay based on last interaction
    const now = Date.now();
    const timeSinceLastInteraction = state.lastInteractionTime ? now - state.lastInteractionTime : 0;
    const minDelay = 2000; // Minimum 2 seconds delay
    const calculatedDelay = Math.max(minDelay - timeSinceLastInteraction, 0);
    
    console.log(`Restarting auto switch with delay: ${calculatedDelay}ms`);
    
    // Start fetch interval
    fetchIntervalRef.current = setInterval(() => {
      fetchDeviceData();
    }, fetchInterval);
    
    // Immediately switch to current device (no delay for visual feedback)
    const currentDevice = dataSource[state.currentIndex];
    if (currentDevice && typeof window.mapboxAutoSwitch === 'function') {
      try {
        window.mapboxAutoSwitch(currentDevice, state.currentIndex);
        console.log(`Immediate switch to: ${currentDevice.name || currentDevice.device_name || currentDevice.station_name}`);
      } catch (error) {
        console.error('Immediate switch error:', error);
      }
    }
    
    // Start main interval with calculated delay
    setTimeout(() => {
      if (intervalRef.current) return; // Prevent multiple starts
      
      intervalRef.current = setInterval(() => {
        if (!state.isPlaying) {
          clearInterval(intervalRef.current);
          intervalRef.current = null;
          return;
        }
        tick();
      }, interval);
      
      console.log(`Auto switch interval started with fresh timing`);
    }, calculatedDelay);
    
    dispatch({ type: 'SET_PLAYING', payload: true });
    dispatch({ type: 'SET_USER_INTERACTING', payload: false });
    dispatch({ type: 'RESET_TICK' });
    
    // Dispatch event
    document.dispatchEvent(
      new CustomEvent('autoSwitchActivated', {
        detail: { 
          active: true, 
          currentIndex: state.currentIndex, 
          deviceCount: dataSource.length,
          restartDelay: calculatedDelay
        }
      })
    );
  }, [state.devicesData, state.isPlaying, state.lastInteractionTime, interval, fetchInterval, fetchDeviceData, tick, state.currentIndex]);

  // Toggle play/pause
  const togglePlayPause = useCallback(() => {
    if (state.isPlaying) {
      if (state.isPaused) {
        // Resume if paused
        resumeAutoSwitch();
      } else {
        // Stop completely if playing
        stopAutoSwitch();
      }
    } else {
      // Start if not playing
      startAutoSwitch();
    }
  }, [state.isPlaying, state.isPaused, startAutoSwitch, stopAutoSwitch, resumeAutoSwitch]);
  
  // Sync with external state dengan debouncing untuk mencegah infinite loops
  useEffect(() => {
    const syncTimeout = setTimeout(() => {
      if (isAutoSwitchOn !== state.isPlaying) {
        console.log(`=== SYNCING EXTERNAL STATE ===`);
        console.log(`isAutoSwitchOn: ${isAutoSwitchOn}`);
        console.log(`state.isPlaying: ${state.isPlaying}`);
        console.log(`state.isPaused: ${state.isPaused}`);
        
        if (isAutoSwitchOn && !state.isPlaying) {
          console.log('Starting auto switch from external state');
          startAutoSwitch();
        } else if (!isAutoSwitchOn && state.isPlaying) {
          console.log('Stopping auto switch from external state');
          stopAutoSwitch();
        }
        console.log('=== EXTERNAL STATE SYNC COMPLETED ===');
      }
    }, 100); // Debounce 100ms untuk mencegah rapid state changes
    
    return () => clearTimeout(syncTimeout);
  }, [isAutoSwitchOn, state.isPlaying, state.isPaused, startAutoSwitch, stopAutoSwitch]);
  
  // Initial fetch
  useEffect(() => {
    fetchDeviceData();
  }, [fetchDeviceData]);
  
  // Listen for auto switch errors from MapboxMap
  useEffect(() => {
    const handleAutoSwitchError = (event) => {
      console.error('=== AUTO SWITCH ERROR RECEIVED ===');
      console.error('Error details:', event.detail);
      dispatch({ type: 'SET_ERROR', payload: event.detail.error });
      
      // Jika error adalah map_not_ready, coba restart setelah delay
      if (event.detail.type === 'map_not_ready') {
        console.log('Map not ready error - scheduling retry in 2 seconds');
        setTimeout(() => {
          if (state.isPlaying && !state.isPaused) {
            console.log('Retrying auto switch after map ready');
            restartAutoSwitch();
          }
        }, 2000);
      }
      console.error('=== END AUTO SWITCH ERROR ===');
    };
    
    const handleAutoSwitchSuccess = (event) => {
      console.log('=== AUTO SWITCH SUCCESS ===');
      console.log('Success details:', event.detail);
      // Clear any previous errors on success
      if (state.error) {
        console.log('Clearing previous error on success');
        dispatch({ type: 'SET_ERROR', payload: null });
      }
      console.log('=== END AUTO SWITCH SUCCESS ===');
    };
    
    console.log('Registering auto switch event listeners');
    document.addEventListener('autoSwitchError', handleAutoSwitchError);
    document.addEventListener('autoSwitchSuccess', handleAutoSwitchSuccess);
    
    return () => {
      console.log('Removing auto switch event listeners');
      document.removeEventListener('autoSwitchError', handleAutoSwitchError);
      document.removeEventListener('autoSwitchSuccess', handleAutoSwitchSuccess);
    };
  }, [state.isPlaying, state.isPaused, state.error, restartAutoSwitch]);

  // Cleanup on unmount
  useEffect(() => {
    return () => {
      if (intervalRef.current) {
        clearInterval(intervalRef.current);
      }
      if (fetchIntervalRef.current) {
        clearInterval(fetchIntervalRef.current);
      }
    };
  }, []);
  
  return {
    ...state,
    startAutoSwitch,
    stopAutoSwitch,
    pauseAutoSwitch,
    resumeAutoSwitch,
    togglePlayPause,
    fetchDeviceData,
    hasData: (state.devicesData && state.devicesData.length > 0) || (tickerData && tickerData.length > 0)
  };
};
