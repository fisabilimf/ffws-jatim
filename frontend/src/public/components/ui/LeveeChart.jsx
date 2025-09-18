import React, { useMemo } from 'react';

/**
 * LeveeChart
 * Visualisasi sederhana penampang sungai dan tanggul dengan SVG.
 * - Menampilkan bingkai tanggul (abu-abu) dan level air saat ini (biru).
 * - Skalasi sumbu Y berdasarkan maxHeight agar konsisten antar stasiun.
 */
const LeveeChart = ({
  currentLevel = 0,
  unit = 'm',
  maxHeight = 4, // ketinggian maksimum chart (mis. ambang bahaya + margin)
  width = 600,
  height = 200,
  className = ''
}) => {
  const dims = useMemo(() => ({
    w: width,
    h: height,
    paddingX: 24,
    paddingY: 16
  }), [width, height]);

  const scaleY = (value) => {
    const clamped = Math.max(0, Math.min(maxHeight, value));
    const usableHeight = dims.h - dims.paddingY * 2;
    return dims.paddingY + usableHeight * (1 - clamped / maxHeight);
  };

  // Bentuk tanggul (sisi kiri datar -> tebing -> dasar sungai -> tebing -> sisi kanan datar)
  const leveePath = useMemo(() => {
    const w = dims.w;
    const h = dims.h;
    const px = dims.paddingX;
    const topY = scaleY(maxHeight * 0.88); // puncak tanggul mendekati max
    const shoulderY = scaleY(maxHeight * 0.75);
    const bottomY = scaleY(maxHeight * 0.05); // dasar sungai
    const leftTop = `${px},${topY}`;
    const leftShoulder = `${px + w * 0.15},${shoulderY}`;
    const leftSlope = `${px + w * 0.35},${bottomY}`;
    const rightSlope = `${px + w * 0.65},${bottomY}`;
    const rightShoulder = `${px + w * 0.85},${shoulderY}`;
    const rightTop = `${w - px},${topY}`;
    // Tutup poligon ke bawah untuk shading
    const bottomRight = `${w - px},${h - dims.paddingY}`;
    const bottomLeft = `${px},${h - dims.paddingY}`;
    return `M ${leftTop} L ${leftShoulder} L ${leftSlope} L ${rightSlope} L ${rightShoulder} L ${rightTop} L ${bottomRight} L ${bottomLeft} Z`;
  }, [dims, maxHeight]);

  const waterY = scaleY(currentLevel);

  return (
    <div className={className}>
      <svg width={dims.w} height={dims.h} viewBox={`0 0 ${dims.w} ${dims.h}`} className="w-full">
        {/* Grid horizontal sederhana */}
        {[0, 0.25, 0.5, 0.75, 1].map((t) => {
          const y = dims.paddingY + (dims.h - dims.paddingY * 2) * (1 - t);
          return (
            <line key={t} x1={dims.paddingX} x2={dims.w - dims.paddingX} y1={y} y2={y} stroke="#e5e7eb" strokeWidth="1" />
          );
        })}

        {/* Bingkai tanggul */}
        <path d={leveePath} fill="#eef0f3" stroke="#e5e7eb" strokeWidth="2" />

        {/* Air: kotak dari waterY ke bawah */}
        <rect
          x={dims.paddingX}
          y={waterY}
          width={dims.w - dims.paddingX * 2}
          height={dims.h - dims.paddingY - waterY}
          fill="#38bdf8"
          opacity="0.6"
        />

        {/* Garis level air */}
        <line x1={dims.paddingX} x2={dims.w - dims.paddingX} y1={waterY} y2={waterY} stroke="#06b6d4" strokeWidth="3" />

        {/* Titik indikator di kanan */}
        <circle cx={dims.w - dims.paddingX - 8} cy={waterY} r={5} fill="#06b6d4" stroke="#fff" strokeWidth="2" />

        {/* Sumbu Y label sederhana (0 dan max) */}
        <text x={dims.paddingX - 8} y={dims.h - dims.paddingY} textAnchor="end" alignmentBaseline="middle" fontSize="10" fill="#6b7280">0 {unit}</text>
        <text x={dims.paddingX - 8} y={dims.paddingY} textAnchor="end" alignmentBaseline="middle" fontSize="10" fill="#6b7280">{maxHeight} {unit}</text>
      </svg>

      {/* Legend */}
      <div className="flex items-center gap-4 mt-2 text-xs text-gray-600">
        <div className="flex items-center gap-1">
          <span className="inline-block w-4 h-2 bg-cyan-400" />
          <span className="font-medium">Level Air</span>
        </div>
        <div className="flex items-center gap-1">
          <span className="inline-block w-4 h-2 bg-gray-200 border border-gray-200" />
          <span className="font-medium">Bingkai Tanggul</span>
        </div>
        <div className="ml-auto text-gray-700">{currentLevel.toFixed(2)}{unit}</div>
      </div>
    </div>
  );
};

export default LeveeChart;



