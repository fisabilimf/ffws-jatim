import React, { useState, useEffect } from 'react';

const Chart = ({ 
  data = [], 
  width = 320, 
  height = 160, 
  showTooltip = true,
  className = "",
  onDataPointHover = null 
}) => {
  const [tooltip, setTooltip] = useState({ visible: false, x: 0, y: 0, data: null });

  // Function to generate timestamps for chart data points
  const generateTimestamps = (dataLength) => {
    const timestamps = [];
    const now = new Date();
    
    // Generate timestamps going back in time (each point represents 30 seconds)
    for (let i = dataLength - 1; i >= 0; i--) {
      const time = new Date(now.getTime() - (i * 30 * 1000)); // 30 seconds per point
      timestamps.push(time);
    }
    
    return timestamps;
  };

  // Draw chart
  useEffect(() => {
    if (data.length === 0) return;

    const canvas = document.getElementById('chart-canvas');
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

    // Set line color based on data range (you can customize this)
    let lineColor = '#10B981'; // Default green
    if (maxValue > 4) lineColor = '#EF4444'; // Red for high values
    else if (maxValue > 2.5) lineColor = '#F59E0B'; // Yellow for medium values

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

    // Draw hover indicator if tooltip is visible
    if (tooltip.visible && tooltip.data) {
      const pointIndex = tooltip.data.index;
      const x = (pointIndex / (data.length - 1)) * canvasWidth;
      const y = canvasHeight - ((tooltip.data.value - minValue) / range) * canvasHeight;
      
      // Draw circle at hovered point
      ctx.beginPath();
      ctx.fillStyle = lineColor;
      ctx.arc(x, y, 6, 0, 2 * Math.PI);
      ctx.fill();
      
      // Draw white border
      ctx.beginPath();
      ctx.strokeStyle = '#FFFFFF';
      ctx.lineWidth = 2;
      ctx.arc(x, y, 6, 0, 2 * Math.PI);
      ctx.stroke();
      
      // Draw vertical line
      ctx.beginPath();
      ctx.strokeStyle = lineColor;
      ctx.lineWidth = 1;
      ctx.setLineDash([5, 5]);
      ctx.moveTo(x, 0);
      ctx.lineTo(x, canvasHeight);
      ctx.stroke();
      ctx.setLineDash([]); // Reset dash
    }

  }, [data, tooltip]);

  // Handle mouse events for tooltip
  const handleChartMouseMove = (event) => {
    if (!showTooltip || data.length === 0) return;

    const canvas = document.getElementById('chart-canvas');
    if (!canvas) return;

    const rect = canvas.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;

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
      
      // Convert canvas coordinates to screen coordinates
      const screenX = rect.left + pointX;
      const screenY = rect.top + pointY;
      
      const tooltipData = {
        value: value,
        timestamp: timestamp,
        index: pointIndex
      };

      setTooltip({
        visible: true,
        x: screenX,
        y: screenY,
        data: tooltipData
      });

      // Call onDataPointHover callback if provided
      if (onDataPointHover) {
        onDataPointHover(tooltipData);
      }
    }
  };

  const handleChartMouseLeave = () => {
    setTooltip({ visible: false, x: 0, y: 0, data: null });
  };

  return (
    <div className={`relative ${className}`}>
      <canvas
        id="chart-canvas"
        width={width}
        height={height}
        className={`w-full rounded bg-white ${showTooltip ? 'cursor-crosshair' : ''}`}
        onMouseMove={handleChartMouseMove}
        onMouseLeave={handleChartMouseLeave}
      />
      
      {/* Tooltip */}
      {showTooltip && tooltip.visible && tooltip.data && (
        <div
          className="absolute bg-gray-900 text-white text-xs rounded-lg px-3 py-2 shadow-lg pointer-events-none z-10"
          style={{
            left: `${tooltip.x}px`,
            top: `${tooltip.y - 15}px`,
            transform: 'translate(-50%, -100%)'
          }}
        >
          <div className="text-center">
            <div className="font-semibold text-white">
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
          {/* Arrow pointing down */}
          <div className="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
        </div>
      )}
    </div>
  );
};

export default Chart;
