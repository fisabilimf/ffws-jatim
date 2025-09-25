/**
 * Konfigurasi optimasi untuk aplikasi
 * Centralized configuration untuk performance tuning
 */

export const OPTIMIZATION_CONFIG = {
  // Chart optimizations
  CHART: {
    // Reduce data points untuk performa lebih baik
    MAX_DATA_POINTS: 50,
    // Debounce tooltip updates
    TOOLTIP_DEBOUNCE_MS: 16, // ~60fps
    // Animation frame rate
    ANIMATION_FPS: 10
  },
  
  // Map optimizations
  MAP: {
    // Lazy load map tiles
    LAZY_LOAD_TILES: true,
    // Reduce map updates frequency
    UPDATE_INTERVAL_MS: 1000
  },
  
  // General optimizations
  GENERAL: {
    // Enable performance monitoring in development
    ENABLE_PERFORMANCE_MONITORING: process.env.NODE_ENV === 'development',
    // Enable React DevTools profiler
    ENABLE_PROFILER: process.env.NODE_ENV === 'development',
    // Memory cleanup interval
    MEMORY_CLEANUP_INTERVAL_MS: 30000
  },
  
  // Lazy loading configuration
  LAZY_LOADING: {
    // Components to lazy load
    LAZY_COMPONENTS: [
      'MapboxMap',
      'GoogleMapsSearchbar',
      'FloatingLegend',
      'FloodRunningBar',
      'StationDetail',
      'DetailPanel',
      'AutoSwitchToggle'
    ],
    // Fallback loading component
    FALLBACK_COMPONENT: 'div'
  }
};

// Performance thresholds
export const PERFORMANCE_THRESHOLDS = {
  // Render time thresholds (ms)
  RENDER_TIME: {
    GOOD: 16,      // 60fps
    WARNING: 33,   // 30fps
    CRITICAL: 100  // 10fps
  },
  
  // Memory usage thresholds (MB)
  MEMORY_USAGE: {
    GOOD: 50,
    WARNING: 100,
    CRITICAL: 200
  },
  
  // Bundle size thresholds (KB)
  BUNDLE_SIZE: {
    GOOD: 500,
    WARNING: 1000,
    CRITICAL: 2000
  }
};

// Utility functions untuk optimasi
export const optimizationUtils = {
  // Debounce function
  debounce: (func, wait) => {
    let timeout;  
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  },
  
  // Throttle function
  throttle: (func, limit) => {
    let inThrottle;
    return function() {
      const args = arguments;
      const context = this;
      if (!inThrottle) {
        func.apply(context, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  },
  
  // Check if component should re-render
  shouldComponentUpdate: (prevProps, nextProps, keysToCheck = []) => {
    if (keysToCheck.length === 0) {
      return JSON.stringify(prevProps) !== JSON.stringify(nextProps);
    }
    
    return keysToCheck.some(key => prevProps[key] !== nextProps[key]);
  },
  
  // Memory cleanup
  cleanupMemory: () => {
    if (window.gc) {
      window.gc();
    }
  }
};

export default OPTIMIZATION_CONFIG;
