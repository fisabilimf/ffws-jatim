/**
 * Performance Monitor untuk debugging dan optimasi
 * Tools untuk memantau performa aplikasi di development
 */

class PerformanceMonitor {
  constructor() {
    this.metrics = {
      renderTimes: [],
      memoryUsage: [],
      componentRenders: new Map()
    };
    this.isEnabled = process.env.NODE_ENV === 'development';
  }

  // Monitor render time untuk komponen
  measureRenderTime(componentName, startTime) {
    if (!this.isEnabled) return;
    
    const endTime = performance.now();
    const renderTime = endTime - startTime;
    
    this.metrics.renderTimes.push({
      component: componentName,
      time: renderTime,
      timestamp: Date.now()
    });

    // Log jika render time terlalu lama (> 16ms untuk 60fps)
    if (renderTime > 16) {
      console.warn(`🐌 Slow render detected: ${componentName} took ${renderTime.toFixed(2)}ms`);
    }
  }

  // Monitor memory usage
  measureMemoryUsage() {
    if (!this.isEnabled || !performance.memory) return;
    
    const memory = {
      used: performance.memory.usedJSHeapSize,
      total: performance.memory.totalJSHeapSize,
      limit: performance.memory.jsHeapSizeLimit,
      timestamp: Date.now()
    };
    
    this.metrics.memoryUsage.push(memory);
    
    // Log jika memory usage tinggi
    const usagePercent = (memory.used / memory.limit) * 100;
    if (usagePercent > 80) {
      console.warn(`High memory usage: ${usagePercent.toFixed(1)}%`);
    }
  }

  // Track component re-renders
  trackComponentRender(componentName) {
    if (!this.isEnabled) return;
    
    const count = this.metrics.componentRenders.get(componentName) || 0;
    this.metrics.componentRenders.set(componentName, count + 1);
  }

  // Get performance summary
  getPerformanceSummary() {
    if (!this.isEnabled) return null;
    
    const avgRenderTime = this.metrics.renderTimes.length > 0 
      ? this.metrics.renderTimes.reduce((sum, metric) => sum + metric.time, 0) / this.metrics.renderTimes.length
      : 0;
    
    const slowRenders = this.metrics.renderTimes.filter(metric => metric.time > 16).length;
    
    const mostRenderedComponents = Array.from(this.metrics.componentRenders.entries())
      .sort((a, b) => b[1] - a[1])
      .slice(0, 5);
    
    return {
      averageRenderTime: avgRenderTime.toFixed(2),
      slowRenders,
      totalRenders: this.metrics.renderTimes.length,
      mostRenderedComponents,
      memoryUsage: this.metrics.memoryUsage.slice(-10) // Last 10 measurements
    };
  }

  // Log performance summary
  logPerformanceSummary() {
    if (!this.isEnabled) return;
    
    const summary = this.getPerformanceSummary();
    if (!summary) return;
    
    console.group('Performance Summary');
    console.log(`Average render time: ${summary.averageRenderTime}ms`);
    console.log(`Slow renders (>16ms): ${summary.slowRenders}`);
    console.log(`Total renders: ${summary.totalRenders}`);
    console.log('Most rendered components:', summary.mostRenderedComponents);
    console.groupEnd();
  }

  // Clear metrics
  clearMetrics() {
    this.metrics.renderTimes = [];
    this.metrics.memoryUsage = [];
    this.metrics.componentRenders.clear();
  }
}

// Singleton instance
const performanceMonitor = new PerformanceMonitor();

// Auto log performance summary setiap 30 detik
if (process.env.NODE_ENV === 'development') {
  setInterval(() => {
    performanceMonitor.logPerformanceSummary();
  }, 30000);
}

export default performanceMonitor;

// React Hook untuk performance monitoring
import React from 'react';

export const usePerformanceMonitor = (componentName) => {
  const startTime = React.useRef(performance.now());
  
  React.useEffect(() => {
    performanceMonitor.measureRenderTime(componentName, startTime.current);
    performanceMonitor.trackComponentRender(componentName);
  });
  
  React.useEffect(() => {
    performanceMonitor.measureMemoryUsage();
  }, []);
};
