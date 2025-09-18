import React, { useMemo, useEffect, useRef } from 'react';

/**
 * LeveeChart
 * Visualisasi real-time penampang sungai dan tanggul dengan Canvas.
 * - Menampilkan bingkai tanggul dan level air dengan data real-time
 * - Menggunakan warna yang konsisten dengan Chart component
 * - Menampilkan trend data historis sebagai background
 */
const LeveeChart = ({
  currentLevel = 0,
  unit = 'm',
  maxHeight = 4, // ketinggian maksimum chart (mis. ambang bahaya + margin)
  width = 600,
  height = 200,
  className = '',
  historyData = [], // Data historis untuk trend background
  status = 'safe' // Status untuk menentukan warna
}) => {
  const canvasRef = useRef(null);

  // Warna berdasarkan status (konsisten dengan Chart component)
  const getStatusColor = (status) => {
    switch (status) {
      case 'safe': return '#10B981'; // green
      case 'warning': return '#F59E0B'; // yellow
      case 'alert': return '#EF4444'; // red
      default: return '#6B7280'; // gray
    }
  };

  const waterColor = getStatusColor(status);

  // Fungsi untuk mengkonversi nilai ke koordinat Y
  const scaleY = (value) => {
    const clamped = Math.max(0, Math.min(maxHeight, value));
    const usableHeight = height - 60; // padding top dan bottom lebih besar
    return 30 + usableHeight * (1 - clamped / maxHeight);
  };

  // Fungsi untuk mengkonversi index data ke koordinat X
  const scaleX = (index, dataLength) => {
    if (dataLength <= 1) return width / 2;
    return 24 + (index / (dataLength - 1)) * (width - 48);
  };

  // Draw chart dengan Canvas
  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const canvasWidth = canvas.width;
    const canvasHeight = canvas.height;

    // Animation loop for waves
    let animationId;
    
    const drawChart = () => {
      // Clear canvas
      ctx.clearRect(0, 0, canvasWidth, canvasHeight);

      // Draw grid lines with better spacing
      ctx.strokeStyle = '#f3f4f6';
      ctx.lineWidth = 1;
      for (let i = 0; i <= 4; i++) {
        const y = 30 + (canvasHeight - 60) * (i / 4);
        ctx.beginPath();
        ctx.moveTo(40, y);
        ctx.lineTo(canvasWidth - 40, y);
        ctx.stroke();
      }

      // Draw simplified levee structure with better positioning
      const leveeTop = scaleY(maxHeight * 0.9);
      const leveeBottom = scaleY(maxHeight * 0.1);
      const leftSlope = 40 + (canvasWidth - 80) * 0.3;
      const rightSlope = 40 + (canvasWidth - 80) * 0.7;
      
      // Simple levee shape - single color
      ctx.fillStyle = '#F8FAFC'; // Very light gray
      ctx.beginPath();
      ctx.moveTo(40, leveeTop);
      ctx.lineTo(leftSlope, leveeBottom);
      ctx.lineTo(rightSlope, leveeBottom);
      ctx.lineTo(canvasWidth - 40, leveeTop);
      ctx.lineTo(canvasWidth - 40, canvasHeight - 30);
      ctx.lineTo(40, canvasHeight - 30);
      ctx.closePath();
      ctx.fill();
      
      // Simple levee outline
      ctx.strokeStyle = '#E2E8F0';
      ctx.lineWidth = 1;
      ctx.beginPath();
      ctx.moveTo(40, leveeTop);
      ctx.lineTo(leftSlope, leveeBottom);
      ctx.lineTo(rightSlope, leveeBottom);
      ctx.lineTo(canvasWidth - 40, leveeTop);
      ctx.stroke();

    // Draw historical data trend (if available) - DISABLED
    // if (historyData && historyData.length > 1) {
    //   ctx.strokeStyle = waterColor + '40'; // 25% opacity
    //   ctx.lineWidth = 2;
    //   ctx.beginPath();
    //   
    //   historyData.forEach((value, index) => {
    //     const x = scaleX(index, historyData.length);
    //     const y = scaleY(value);
    //     
    //     if (index === 0) {
    //       ctx.moveTo(x, y);
    //     } else {
    //       ctx.lineTo(x, y);
    //     }
    //   });
    //   ctx.stroke();
    // }

      // Draw current water level
      const waterY = scaleY(currentLevel);
      
      // Water area fill with softer blue gradient
      const gradient = ctx.createLinearGradient(0, waterY, 0, canvasHeight - 30);
      // Soft light blue at surface (shallow)
      gradient.addColorStop(0, '#B3E5FC'); // Very light blue
      gradient.addColorStop(0.2, '#81D4FA'); // Light blue
      gradient.addColorStop(0.4, '#4FC3F7'); // Soft blue
      gradient.addColorStop(0.6, '#29B6F6'); // Medium light blue
      gradient.addColorStop(0.8, '#03A9F4'); // Medium blue
      // Soft dark blue at bottom (deep)
      gradient.addColorStop(1, '#0277BD'); // Soft dark blue
      
      ctx.fillStyle = gradient;
      ctx.fillRect(40, waterY, canvasWidth - 80, canvasHeight - 30 - waterY);

      // Draw animated water waves
      const time = Date.now() * 0.001; // Get time in seconds
      const waveAmplitude = 3; // Height of waves
      const waveFrequency = 0.02; // How many waves per pixel
      const waveSpeed = 2; // Speed of wave animation
      
      // Wave colors based on depth - softer tones
      const waveColors = ['#B3E5FC', '#81D4FA', '#4FC3F7']; // Soft light to medium blue
      
      // Draw multiple wave layers for depth
      for (let layer = 0; layer < 3; layer++) {
        const layerOffset = layer * 0.3;
        const layerOpacity = 1 - (layer * 0.3);
        const layerAmplitude = waveAmplitude * (1 - layer * 0.2);
        
        ctx.globalAlpha = layerOpacity * 0.8;
        ctx.strokeStyle = waveColors[layer];
        ctx.lineWidth = 1 + layer;
        ctx.beginPath();
        
        for (let x = 40; x <= canvasWidth - 40; x += 2) {
          const wave1 = Math.sin((x * waveFrequency) + (time * waveSpeed) + layerOffset) * layerAmplitude;
          const wave2 = Math.sin((x * waveFrequency * 1.5) + (time * waveSpeed * 0.8) + layerOffset) * (layerAmplitude * 0.5);
          const wave3 = Math.sin((x * waveFrequency * 0.7) + (time * waveSpeed * 1.2) + layerOffset) * (layerAmplitude * 0.3);
          
          const y = waterY + wave1 + wave2 + wave3;
          
          if (x === 40) {
            ctx.moveTo(x, y);
          } else {
            ctx.lineTo(x, y);
          }
        }
        ctx.stroke();
      }
      
      // Reset alpha
      ctx.globalAlpha = 1;

      // Water level line - softer blue color
      ctx.strokeStyle = '#29B6F6'; // Soft medium blue
      ctx.lineWidth = 4;
      ctx.beginPath();
      ctx.moveTo(40, waterY);
      ctx.lineTo(canvasWidth - 40, waterY);
      ctx.stroke();
      
      // Add soft white outline to water level line
      ctx.strokeStyle = '#ffffff';
      ctx.lineWidth = 6;
      ctx.globalAlpha = 0.6;
      ctx.beginPath();
      ctx.moveTo(40, waterY);
      ctx.lineTo(canvasWidth - 40, waterY);
      ctx.stroke();
      ctx.globalAlpha = 1;

      // Current level indicator with softer design
      ctx.fillStyle = '#29B6F6'; // Soft medium blue
      ctx.strokeStyle = '#ffffff';
      ctx.lineWidth = 2;
      ctx.beginPath();
      ctx.arc(canvasWidth - 40 - 8, waterY, 8, 0, 2 * Math.PI);
      ctx.fill();
      ctx.stroke();
      
      // All text labels removed for clean visualization

      // Continue animation loop
      animationId = requestAnimationFrame(drawChart);
    };

    // Start animation
    drawChart();

    // Cleanup function
    return () => {
      if (animationId) {
        cancelAnimationFrame(animationId);
      }
    };
  }, [currentLevel, maxHeight, width, height, waterColor, historyData, unit]);

  return (
    <div className={className}>
      <canvas
        ref={canvasRef}
        width={width}
        height={height}
        className="w-full rounded bg-white"
      />
      
      {/* All text removed - clean visualization only */}
    </div>
  );
};

export default LeveeChart;



  