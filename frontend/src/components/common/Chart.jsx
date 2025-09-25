import React, { useState, useEffect, memo, useCallback, useRef } from 'react';

const Chart = ({ 
  data = [], 
  width = 320, 
  height = 160, 
  showTooltip = true,
  className = "",
  onDataPointHover = null,
  miniMode = false,
  status = 'safe',
  canvasId = 'chart-canvas'
}) => {
  const [tooltip, setTooltip] = useState({ 
    visible: false, 
    x: 0, 
    y: 0, 
    data: null, 
    position: 'top' // 'top' or 'bottom'
  });
  
  // Debounce tooltip untuk performa yang lebih baik
  const tooltipTimeoutRef = useRef(null);

  // Function to generate timestamps for chart data points - memoized
  const generateTimestamps = useCallback((dataLength) => {
    const timestamps = [];
    const now = new Date();
    
    // Generate timestamps going back in time (each point represents 30 seconds)
    for (let i = dataLength - 1; i >= 0; i--) {
      const time = new Date(now.getTime() - (i * 30 * 1000)); // 30 seconds per point
      timestamps.push(time);
    }
    
    return timestamps;
  }, []);

  // Function to calculate optimal tooltip position - memoized
  const calculateTooltipPosition = useCallback((pointX, pointY, canvasRect) => {
    const tooltipHeight = 60; // Smaller tooltip height
    const tooltipWidth = 100; // Smaller tooltip width
    const margin = 8; // Smaller margin from edges
    
    // Calculate if tooltip should be above or below the point
    const spaceAbove = pointY;
    const spaceBelow = canvasRect.height - pointY;
    
    // If there's not enough space above, position below
    const shouldPositionBelow = spaceAbove < tooltipHeight + margin;
    
    // Calculate X position (center on point, but keep within bounds)
    let tooltipX = pointX;
    const halfWidth = tooltipWidth / 2;
    
    if (pointX - halfWidth < margin) {
      tooltipX = margin + halfWidth;
    } else if (pointX + halfWidth > canvasRect.width - margin) {
      tooltipX = canvasRect.width - margin - halfWidth;
    }
    
    // Calculate Y position with smaller offset
    let tooltipY = shouldPositionBelow 
      ? pointY + 10 // Below the point
      : pointY - 10; // Above the point
    
    return {
      x: tooltipX,
      y: tooltipY,
      position: shouldPositionBelow ? 'bottom' : 'top'
    };
  }, []);

  // Draw chart
  useEffect(() => {
    if (data.length === 0) return;

    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const canvasWidth = canvas.width;
    const canvasHeight = canvas.height;

    // Clear canvas
    ctx.clearRect(0, 0, canvasWidth, canvasHeight);

    if (data.length < 2) return;

    // Find min and max values for scaling
    const minValue = Math.min(...data);
    const maxValue = Math.max(...data);
    const range = maxValue - minValue || 1;

    // Set line color based on status - Chart.jsx is the single source of truth for colors
    let lineColor = '#10B981'; // Default green
    if (status) {
      // Use status-based coloring for all charts (centralized color management)
      switch (status) {
        case 'safe': lineColor = '#10B981'; break; // green
        case 'warning': lineColor = '#F59E0B'; break; // yellow
        case 'alert': lineColor = '#EF4444'; break; // red
        default: lineColor = '#6B7280'; break;
      }
    } else {
      // Fallback to data-based coloring only if no status provided
      if (maxValue > 4) lineColor = '#EF4444'; // Red for high values
      else if (maxValue > 2.5) lineColor = '#F59E0B'; // Yellow for medium values
    }

    // Create gradient for area fill
    const gradient = ctx.createLinearGradient(0, 0, 0, canvasHeight);
    gradient.addColorStop(0, lineColor + '50'); // 50% opacity at top
    gradient.addColorStop(0.3, lineColor + '40'); // 40% opacity near line
    gradient.addColorStop(0.6, lineColor + '25'); // 25% opacity at middle
    gradient.addColorStop(1, lineColor + '05'); // 5% opacity at bottom

    // Draw area fill below the line
    ctx.beginPath();
    ctx.fillStyle = gradient;
    
    // Start from bottom-left
    ctx.moveTo(0, canvasHeight);
    
    // Draw line to each data point
    data.forEach((value, index) => {
      const x = (index / (data.length - 1)) * canvasWidth;
      const y = canvasHeight - ((value - minValue) / range) * canvasHeight;
      ctx.lineTo(x, y);
    });
    
    // Complete the path to bottom-right and fill
    ctx.lineTo(canvasWidth, canvasHeight);
    ctx.closePath();
    ctx.fill();

    // Draw the line chart on top
    ctx.beginPath();
    ctx.strokeStyle = lineColor;
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    data.forEach((value, index) => {
      const x = (index / (data.length - 1)) * canvasWidth;
      const y = canvasHeight - ((value - minValue) / range) * canvasHeight;
      
      if (index === 0) {
        ctx.moveTo(x, y);
      } else {
        ctx.lineTo(x, y);
      }
    });

    ctx.stroke();

    // Draw hover indicator if tooltip is visible (only for non-mini mode)
    if (!miniMode && tooltip.visible && tooltip.data) {
      const pointIndex = tooltip.data.index;
      
      // Ensure pointIndex is within bounds
      if (pointIndex >= 0 && pointIndex < data.length) {
        const x = (pointIndex / (data.length - 1)) * canvasWidth;
        // Use actual data value from the array to ensure marker is on the line
        const actualValue = data[pointIndex];
        const y = canvasHeight - ((actualValue - minValue) / range) * canvasHeight;
        
        // Draw circle at hovered point with better visibility
        ctx.beginPath();
        ctx.fillStyle = lineColor;
        ctx.arc(x, y, 6, 0, 2 * Math.PI);
        ctx.fill();
        
        // Draw white border for better contrast
        ctx.beginPath();
        ctx.strokeStyle = '#FFFFFF';
        ctx.lineWidth = 2;
        ctx.arc(x, y, 6, 0, 2 * Math.PI);
        ctx.stroke();
        
        // Draw vertical line with better styling
        ctx.beginPath();
        ctx.strokeStyle = lineColor;
        ctx.lineWidth = 1;
        ctx.setLineDash([5, 5]);
        ctx.moveTo(x, 0);
        ctx.lineTo(x, canvasHeight);
        ctx.stroke();
        ctx.setLineDash([]); // Reset dash
      }
    }

  }, [data, tooltip, showTooltip, miniMode, status, canvasId]);

  // Handle mouse events for tooltip (disabled in mini mode) - dengan debouncing
  const handleChartMouseMove = useCallback((event) => {
    if (!showTooltip || data.length === 0 || miniMode) return;

    // Clear existing timeout
    if (tooltipTimeoutRef.current) {
      clearTimeout(tooltipTimeoutRef.current);
    }

    // Debounce tooltip update
    tooltipTimeoutRef.current = setTimeout(() => {
      const canvas = document.getElementById(canvasId);
      if (!canvas) return;

      const rect = canvas.getBoundingClientRect();
      const x = event.clientX - rect.left;
      const y = event.clientY - rect.top;

      // Only show tooltip if mouse is within chart bounds
      if (x < 0 || x > rect.width || y < 0 || y > rect.height) {
        setTooltip({ visible: false, x: 0, y: 0, data: null });
        return;
      }

      // Calculate which data point is closest to mouse position
      const canvasWidth = canvas.width;
      const canvasHeight = canvas.height;
      const pointIndex = Math.round((x / canvasWidth) * (data.length - 1));
      
      if (pointIndex >= 0 && pointIndex < data.length) {
        const value = data[pointIndex];
        const timestamps = generateTimestamps(data.length);
        const timestamp = timestamps[pointIndex];
        
        // Calculate the exact position of the data point on the canvas
        const minValue = Math.min(...data);
        const maxValue = Math.max(...data);
        const range = maxValue - minValue || 1;
        
        const pointX = (pointIndex / (data.length - 1)) * canvasWidth;
        const pointY = canvasHeight - ((value - minValue) / range) * canvasHeight;
        
        // Calculate optimal tooltip position
        const canvasRect = { width: canvasWidth, height: canvasHeight };
        const tooltipPos = calculateTooltipPosition(pointX, pointY, canvasRect);
        
        // Use relative coordinates for smoother tooltip
        const tooltipData = {
          value: value, // Use actual data value from array
          timestamp: timestamp,
          index: pointIndex
        };

        setTooltip({
          visible: true,
          x: tooltipPos.x,
          y: tooltipPos.y,
          data: tooltipData,
          position: tooltipPos.position
        });

        // Call onDataPointHover callback if provided
        if (onDataPointHover) {
          onDataPointHover(tooltipData);
        }
      }
    }, 16); // ~60fps debounce
  }, [showTooltip, data, miniMode, canvasId, generateTimestamps, calculateTooltipPosition, onDataPointHover]);

  const handleChartMouseLeave = useCallback(() => {
    if (miniMode) return;
    
    // Clear timeout when mouse leaves
    if (tooltipTimeoutRef.current) {
      clearTimeout(tooltipTimeoutRef.current);
    }
    
    setTooltip({ visible: false, x: 0, y: 0, data: null });
  }, [miniMode]);

  return (
    <div className={`relative ${className}`}>
      <canvas
        id={canvasId}
        width={width}
        height={height}
        className={`w-full rounded bg-white ${showTooltip && !miniMode ? 'cursor-crosshair' : ''} ${miniMode ? 'transition-all duration-300 hover:scale-105' : ''}`}
        onMouseMove={handleChartMouseMove}
        onMouseLeave={handleChartMouseLeave}
      />
      
      {/* Tooltip (only for non-mini mode) */}
      {!miniMode && showTooltip && tooltip.visible && tooltip.data && (
        <div
          className="absolute bg-gray-900 text-white text-xs rounded-md px-2 py-1.5 shadow-lg pointer-events-none z-50 border border-gray-700 transition-all duration-150 ease-out"
          style={{
            left: `${tooltip.x}px`,
            top: `${tooltip.y}px`,
            transform: tooltip.position === 'top' 
              ? 'translate(-50%, -100%)' 
              : 'translate(-50%, 0%)',
            minWidth: '80px',
            maxWidth: '120px'
          }}
        >
          <div className="text-center space-y-0.5">
            <div className="font-semibold text-white text-xs">
              {tooltip.data.value.toFixed(2)}m
            </div>
            <div className="text-gray-300 text-xs">
              {tooltip.data.timestamp.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
              })}
            </div>
            <div className="text-gray-400 text-xs">
              {tooltip.data.timestamp.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
              })}
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

// Memoize component untuk mencegah re-render yang tidak perlu
export default memo(Chart);
