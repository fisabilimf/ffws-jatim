import React, { useMemo, useState, useEffect } from 'react';

/**
 * LeveeChart
 * Visualisasi real-time penampang sungai dan tanggul dengan SVG.
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
  
  status = 'safe' // Status untuk menentukan warna
}) => {
  const [animationTime, setAnimationTime] = useState(0);

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

  // Animation loop untuk gelombang air
  useEffect(() => {
    const interval = setInterval(() => {
      setAnimationTime(prev => prev + 1);
    }, 50); // 20 FPS

    return () => clearInterval(interval);
  }, []);

  // Generate path data untuk area chart
  const generatePaths = useMemo(() => {
    const dataPoints = 50;
    const margin = { top: 40, right: 80, left: 60, bottom: 30 };
    const chartWidth = width - margin.left - margin.right;
    const chartHeight = height - margin.top - margin.bottom;
    
    // Generate levee path (V-shape seperti di gambar)
    let leveePath = '';
    for (let i = 0; i <= dataPoints; i++) {
      const x = margin.left + (i / dataPoints) * chartWidth;
      const normalizedX = i / dataPoints;
      
      let leveeHeight;
      if (normalizedX < 0.2) {
        // Slope kiri tanggul - tinggi di kiri
        leveeHeight = maxHeight * 0.9 + (normalizedX / 0.2) * (maxHeight * 0.1);
      } else if (normalizedX > 0.8) {
        // Slope kanan tanggul - tinggi di kanan
        leveeHeight = maxHeight * 0.1 + ((normalizedX - 0.8) / 0.2) * (maxHeight * 0.9);
      } else {
        // Bagian tengah - rendah (V-shape)
        const centerProgress = (normalizedX - 0.2) / 0.6;
        leveeHeight = maxHeight * 0.9 - centerProgress * (maxHeight * 0.8);
      }
      
      const y = margin.top + chartHeight * (1 - leveeHeight / maxHeight);
      
      if (i === 0) {
        leveePath += `M ${x} ${y}`;
      } else {
        leveePath += ` L ${x} ${y}`;
      }
    }
    
    // Complete levee path to ground
    leveePath += ` L ${width - margin.right} ${height - margin.bottom}`;
    leveePath += ` L ${margin.left} ${height - margin.bottom}`;
    leveePath += ' Z';
    
    // Generate water level line (horizontal dengan marker)
    const waterLevelY = margin.top + chartHeight * (1 - currentLevel / maxHeight);
    let waterLinePath = `M ${margin.left} ${waterLevelY}`;
    for (let i = 1; i <= dataPoints; i++) {
      const x = margin.left + (i / dataPoints) * chartWidth;
      waterLinePath += ` L ${x} ${waterLevelY}`;
    }
    
    return { leveePath, waterLinePath, waterLevelY };
  }, [currentLevel, maxHeight, width, height]);

  // Generate Y-axis labels
  const yLabels = useMemo(() => {
    const labels = [];
    const margin = { top: 40, right: 80, left: 60, bottom: 30 };
    const chartHeight = height - margin.top - margin.bottom;
    
    for (let i = 0; i <= 7; i++) {
      const value = (i / 7) * maxHeight;
      const y = margin.top + chartHeight * (1 - value / maxHeight);
      labels.push({ value: value.toFixed(1), y });
    }
    
    return labels;
  }, [maxHeight, height]);

  return (
    <div className={className}>
      <svg width={width} height={height} className="w-full rounded bg-white">
        <defs>
          {/* Gradient untuk area tanggul */}
          <linearGradient id="leveeGradient" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stopColor="#F3F4F6" />
            <stop offset="100%" stopColor="#D1D5DB" />
          </linearGradient>
        </defs>
        
        {/* Grid lines */}
        {yLabels.map((label, index) => (
          <line
            key={index}
            x1={60}
            y1={label.y}
            x2={width - 80}
            y2={label.y}
            stroke="#f3f4f6"
            strokeDasharray="3 3"
            strokeWidth={1}
          />
        ))}
        
        {/* Area tanggul (abu-abu) */}
        <path
          d={generatePaths.leveePath}
          fill="#D1D5DB"
          fillOpacity={0.6}
          stroke="#9CA3AF"
          strokeWidth={1}
        />
        
        {/* Garis level air horizontal (biru muda) */}
        <path
          d={generatePaths.waterLinePath}
          fill="none"
          stroke="#87CEEB"
          strokeWidth={3}
        />
        
        {/* Marker bulat pada garis air */}
        {Array.from({ length: 7 }, (_, i) => {
          const x = 60 + (i / 6) * (width - 140);
          return (
            <circle
              key={i}
              cx={x}
              cy={generatePaths.waterLevelY}
              r={4}
              fill="#87CEEB"
              stroke="#ffffff"
              strokeWidth={2}
            />
          );
        })}
        
        {/* Y-axis labels */}
        {yLabels.map((label, index) => (
          <text
            key={index}
            x={55}
            y={label.y + 4}
            textAnchor="end"
            fontSize="12"
            fill="#6b7280"
          >
            {label.value}
          </text>
        ))}
        
        {/* Y-axis title */}
        <text
          x={20}
          y={height / 2}
          textAnchor="middle"
          fontSize="12"
          fill="#374151"
          fontWeight="bold"
          transform={`rotate(-90, 20, ${height / 2})`}
        >
          Tinggi Air (m)
        </text>
        
        {/* Judul di pojok kiri atas */}
        <text
          x={60}
          y={25}
          textAnchor="start"
          fontSize="16"
          fill="#374151"
          fontWeight="bold"
        >
          Visualisasi air dengan tanggul
        </text>
        
        {/* Legend di pojok kanan atas */}
        <g transform={`translate(${width - 70}, 20)`}>
          {/* Legend background */}
          <rect
            x={0}
            y={0}
            width={60}
            height={50}
            fill="white"
            stroke="#E5E7EB"
            strokeWidth={1}
            rx={4}
          />
          
          {/* Legend Level Air */}
          <line
            x1={5}
            y1={15}
            x2={15}
            y2={15}
            stroke="#87CEEB"
            strokeWidth={3}
          />
          <circle
            cx={10}
            cy={15}
            r={3}
            fill="#87CEEB"
          />
          <text
            x={20}
            y={18}
            fontSize="10"
            fill="#374151"
          >
            Level Air
          </text>
          
          {/* Legend Bingkai Tanggul */}
          <rect
            x={5}
            y={25}
            width={10}
            height={8}
            fill="#D1D5DB"
            fillOpacity={0.6}
            stroke="#9CA3AF"
            strokeWidth={1}
          />
          <text
            x={20}
            y={32}
            fontSize="10"
            fill="#374151"
          >
            Bingkai Tanggul
          </text>
        </g>
      </svg>
    </div>
  );
};

export default LeveeChart;



  