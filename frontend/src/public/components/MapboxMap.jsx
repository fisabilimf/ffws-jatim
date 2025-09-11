import React, { useEffect, useRef, useState } from 'react';
import mapboxgl from 'mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';

const MapboxMap = ({ tickerData, onStationSelect, onMapFocus }) => {
  const mapContainer = useRef(null);
  const map = useRef(null);
  const [waterAnimationActive, setWaterAnimationActive] = useState(false);
  const [selectedStationCoords, setSelectedStationCoords] = useState(null);
  const popupRef = useRef(null);
  const [expandedPopupId, setExpandedPopupId] = useState(null);

  // Handler untuk auto focus dari running bar
  const handleMapFocus = (focusData) => {
    if (!map.current) return;
    
    const { lat, lng, zoom, stationId, stationName } = focusData;
    setWaterAnimationActive(true);
    setSelectedStationCoords([lng, lat]);
    
    map.current.flyTo({
      center: [lng, lat],
      zoom: zoom || 14,
      pitch: 45,
      bearing: -17.6,
      speed: 1.2,
      curve: 1.4,
      easing: (t) => t,
      essential: true
    });
    
    setTimeout(() => {
      if (tickerData) {
        const station = tickerData.find(s => s.id === stationId);
        if (station) {
          if (onStationSelect) onStationSelect(station);
          showStationPopup(station);
        }
      }
    }, 800);
  };

  // Fungsi untuk menampilkan popup stasiun dengan fitur detail
  const showStationPopup = (station) => {
    if (!map.current || !station || !station.coordinates) return;
    
    if (popupRef.current) {
      popupRef.current.remove();
    }
    
    const popupContent = document.createElement('div');
    popupContent.className = 'station-popup';
    
    const getThresholdInfo = (status) => {
      switch (status) {
        case 'safe': return { min: '0.0', max: '2.5', label: 'Normal' };
        case 'warning': return { min: '2.5', max: '3.5', label: 'Waspada' };
        case 'alert': return { min: '3.5', max: '5.0+', label: 'Bahaya' };
        default: return { min: '0.0', max: '2.5', label: 'Normal' };
      }
    };
    
    const threshold = getThresholdInfo(station.status);
    const lastUpdated = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    const isExpanded = expandedPopupId === station.id;
    
    // Build popup HTML - Bagian Dasar (Selalu Tampil)
    popupContent.innerHTML = `
      <div class="popup-wrapper">
        <div class="popup-header">
          <h3>${station.name.replace('Stasiun ', '')}</h3>
          <span class="timestamp">${lastUpdated}</span>
        </div>
        
        <div class="status-row">
          <div class="status-indicator ${station.status}"></div>
          <span class="status-text ${station.status}">${getStatusText(station.status)}</span>
          <span class="station-id">ID: ${station.id}</span>
        </div>
        
        <div class="level-section">
          <div class="level-header">
            <span>Level Air:</span>
            <span class="level-value ${station.status}">${station.value} ${station.unit}</span>
          </div>
          
          <div class="progress-bar">
            <div class="progress-fill ${station.status}" style="width: ${Math.min((station.value / 5) * 100, 100)}%"></div>
          </div>
          
          <div class="threshold-labels">
            <span>0m</span>
            <span>Threshold: ${threshold.min}m - ${threshold.max}m</span>
            <span>5m</span>
          </div>
        </div>
        
        <div class="location-section">
          <span class="location-label">Lokasi:</span> ${station.location}
        </div>
        
        ${isExpanded ? `
        <div class="detail-section">
          <div class="chart-title">Riwayat Level Air (20 data terakhir)</div>
          <div class="chart-container"></div>
          
          <div class="info-grid">
            <div class="info-item">
              <div class="info-label">Koordinat</div>
              <div class="info-value">${station.coordinates[1].toFixed(3)}, ${station.coordinates[0].toFixed(3)}</div>
            </div>
            <div class="info-item">
              <div class="info-label">Status</div>
              <div class="info-value">${threshold.label}</div>
            </div>
          </div>
          
          <div class="footer-info">
            <span>Sumber: BMKG</span>
            <span>Update: ${lastUpdated}</span>
          </div>
        </div>
        ` : ''}
        
        <div class="toggle-section">
          <button class="detail-toggle" data-id="${station.id}">
            ${isExpanded ? 'Sembunyikan Detail' : 'Lihat Detail'}
          </button>
        </div>
      </div>
    `;
    
    // Tambahkan grafik mini jika diperluas
    if (isExpanded) {
      const chartContainer = popupContent.querySelector('.chart-container');
      if (chartContainer) {
        const canvas = createMiniChart(station);
        chartContainer.appendChild(canvas);
      }
    }
    
    // Buat popup baru dengan positioning yang lebih baik
    popupRef.current = new mapboxgl.Popup({
      offset: [0, -10],
      closeButton: true,
      closeOnClick: false,
      className: 'station-detail-popup',
      maxWidth: '320px'
    })
      .setLngLat(station.coordinates)
      .setDOMContent(popupContent)
      .addTo(map.current);
    
    // Event listener untuk tombol detail
    const detailToggle = popupContent.querySelector('.detail-toggle');
    if (detailToggle) {
      detailToggle.addEventListener('click', () => {
        const stationId = parseInt(detailToggle.getAttribute('data-id'));
        setExpandedPopupId(isExpanded ? null : stationId);
        
        // Tutup popup lama dan buka yang baru
        setTimeout(() => {
          popupRef.current.remove();
          showStationPopup(station);
        }, 100);
      });
    }
  };

  // Fungsi untuk membuat grafik mini
  const createMiniChart = (station) => {
    const canvas = document.createElement('canvas');
    canvas.width = 250;
    canvas.height = 80;
    const ctx = canvas.getContext('2d');
    
    // Background
    ctx.fillStyle = '#f8fafc';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Grid lines
    ctx.strokeStyle = '#e2e8f0';
    ctx.lineWidth = 1;
    for (let i = 0; i <= 4; i++) {
      const y = (canvas.height / 4) * i;
      ctx.beginPath();
      ctx.moveTo(0, y);
      ctx.lineTo(canvas.width, y);
      ctx.stroke();
    }
    
    // Draw chart data
    if (station.history && station.history.length > 0) {
      const maxValue = Math.max(...station.history, 5);
      const minValue = Math.min(...station.history, 0);
      const range = maxValue - minValue || 1;
      
      ctx.beginPath();
      ctx.strokeStyle = getStatusColor(station.status);
      ctx.lineWidth = 2;
      
      station.history.forEach((value, i) => {
        const x = (i / (station.history.length - 1)) * canvas.width;
        const y = canvas.height - ((value - minValue) / range) * canvas.height;
        
        if (i === 0) {
          ctx.moveTo(x, y);
        } else {
          ctx.lineTo(x, y);
        }
      });
      
      ctx.stroke();
    }
    
    return canvas;
  };

  // Helper functions
  const getStatusText = (status) => {
    switch (status) {
      case 'safe': return 'Aman';
      case 'warning': return 'Waspada';
      case 'alert': return 'Bahaya';
      default: return 'Tidak Diketahui';
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'safe': return '#10B981';
      case 'warning': return '#F59E0B';
      case 'alert': return '#EF4444';
      default: return '#6B7280';
    }
  };

  const getStationCoordinates = (stationName) => {
    const coordinates = {
      'Stasiun Surabaya': [112.7508, -7.2575],
      'Stasiun Malang': [112.6308, -7.9831],
      'Stasiun Sidoarjo': [112.7183, -7.4478],
      'Stasiun Probolinggo': [113.7156, -7.7764],
      'Stasiun Pasuruan': [112.6909, -7.6461],
      'Stasiun Mojokerto': [112.4694, -7.4706],
      'Stasiun Lamongan': [112.3333, -7.1167],
      'Stasiun Gresik': [112.5729, -7.1554],
      'Stasiun Tuban': [112.0483, -6.8976],
      'Stasiun Bojonegoro': [111.8816, -7.1500],
      'Stasiun Jombang': [112.2333, -7.5500],
      'Stasiun Nganjuk': [111.8833, -7.6000],
      'Stasiun Kediri': [112.0167, -7.8167],
      'Stasiun Blitar': [112.1667, -8.1000],
      'Stasiun Tulungagung': [111.9000, -8.0667]
    };
    return coordinates[stationName] || null;
  };

  // Expose handleMapFocus ke window object
  useEffect(() => {
    window.mapboxAutoFocus = handleMapFocus;
    return () => {
      if (window.mapboxAutoFocus) {
        delete window.mapboxAutoFocus;
      }
    };
  }, [tickerData]);

  // Initialize map
  useEffect(() => {
    mapboxgl.accessToken = 'pk.eyJ1IjoiZGl0b2ZhdGFoaWxsYWgxIiwiYSI6ImNtZjNveGloczAwNncya3E1YzdjcTRtM3MifQ.kIf5rscGYOzvvBcZJ41u8g';
    if (map.current) return;
    
    map.current = new mapboxgl.Map({
      container: mapContainer.current,
      style: 'mapbox://styles/mapbox/streets-v12',
      center: [112.5, -7.5],
      zoom: 8,
      pitch: 45,
      bearing: -17.6,
      antialias: true
    });
    
    map.current.addControl(new mapboxgl.NavigationControl(), 'top-right');
    map.current.addControl(new mapboxgl.FullscreenControl(), 'top-right');
    map.current.addControl(new mapboxgl.ScaleControl(), 'bottom-left');
    
    // Add popup styles
    const popupStyle = document.createElement('style');
    popupStyle.innerHTML = `
      .station-detail-popup .mapboxgl-popup-content {
        border-radius: 12px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        padding: 0;
        max-height: 80vh;
        overflow-y: auto;
      }
      
      .popup-wrapper {
        padding: 16px;
        min-width: 280px;
        max-width: 300px;
      }
      
      .popup-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 12px;
      }
      
      .popup-header h3 {
        font-weight: bold;
        font-size: 18px;
        margin: 0;
        color: #1f2937;
      }
      
      .timestamp {
        font-size: 12px;
        color: #6b7280;
      }
      
      .status-row {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        gap: 8px;
      }
      
      .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
      }
      
      .status-indicator.safe { background-color: #10B981; }
      .status-indicator.warning { background-color: #F59E0B; }
      .status-indicator.alert { background-color: #EF4444; }
      
      .status-text {
        font-weight: 500;
        flex-grow: 1;
      }
      
      .status-text.safe { color: #059669; }
      .status-text.warning { color: #D97706; }
      .status-text.alert { color: #DC2626; }
      
      .station-id {
        font-size: 12px;
        color: #6b7280;
      }
      
      .level-section {
        background-color: #f9fafb;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 12px;
      }
      
      .level-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
      }
      
      .level-header span:first-child {
        color: #6b7280;
      }
      
      .level-value {
        font-weight: bold;
        font-size: 18px;
      }
      
      .level-value.safe { color: #059669; }
      .level-value.warning { color: #D97706; }
      .level-value.alert { color: #DC2626; }
      
      .progress-bar {
        width: 100%;
        background-color: #e5e7eb;
        border-radius: 9999px;
        height: 8px;
        margin-bottom: 4px;
      }
      
      .progress-fill {
        height: 8px;
        border-radius: 9999px;
        transition: width 0.3s ease;
      }
      
      .progress-fill.safe { background-color: #10B981; }
      .progress-fill.warning { background-color: #F59E0B; }
      .progress-fill.alert { background-color: #EF4444; }
      
      .threshold-labels {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #6b7280;
      }
      
      .location-section {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 12px;
      }
      
      .location-label {
        font-weight: 500;
      }
      
      .detail-section {
        border-top: 1px solid #e5e7eb;
        padding-top: 12px;
        margin-bottom: 12px;
      }
      
      .chart-title {
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 8px;
      }
      
      .chart-container {
        background-color: #f8fafc;
        border-radius: 6px;
        padding: 8px;
        margin-bottom: 12px;
        display: flex;
        justify-content: center;
      }
      
      .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-bottom: 12px;
      }
      
      .info-item {
        background-color: #f9fafb;
        border-radius: 6px;
        padding: 8px;
      }
      
      .info-label {
        color: #6b7280;
        font-size: 12px;
      }
      
      .info-value {
        font-weight: 500;
        font-size: 14px;
        color: #1f2937;
      }
      
      .footer-info {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #6b7280;
        border-top: 1px solid #e5e7eb;
        padding-top: 8px;
      }
      
      .toggle-section {
        display: flex;
        justify-content: center;
      }
      
      .detail-toggle {
        background-color: #3b82f6;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s ease;
      }
      
      .detail-toggle:hover {
        background-color: #2563eb;
      }
      
      .station-detail-popup .mapboxgl-popup-close-button {
        font-size: 20px;
        padding: 5px;
        color: #6b7280;
      }
      
      @keyframes alert-pulse {
        0% { transform: scale(1); opacity: 0.7; }
        70% { transform: scale(3.0); opacity: 0; }
        100% { transform: scale(3.0); opacity: 0; }
      }
    `;
    document.head.appendChild(popupStyle);

    return () => {
      if (map.current) {
        map.current.remove();
        map.current = null;
      }
    };
  }, []);

  // Update markers when tickerData changes
  useEffect(() => {
    if (!map.current || !tickerData) return;
    
    const existingMarkers = document.querySelectorAll('.custom-marker');
    existingMarkers.forEach(marker => marker.remove());
    
    tickerData.forEach((station) => {
      const coordinates = getStationCoordinates(station.name);
      if (coordinates) {
        const markerEl = document.createElement('div');
        markerEl.className = 'custom-marker';
        markerEl.style.width = '30px';
        markerEl.style.height = '30px';
        markerEl.style.borderRadius = '50%';
        markerEl.style.backgroundColor = getStatusColor(station.status);
        markerEl.style.border = '3px solid white';
        markerEl.style.boxShadow = '0 4px 8px rgba(0,0,0,0.4)';
        markerEl.style.cursor = 'pointer';
        
        if (station.status === 'alert') {
          markerEl.innerHTML = `<div style="position: absolute; width: 100%; height: 100%; border-radius: 50%; background-color: ${getStatusColor(station.status)}; opacity: 0.7; animation: alert-pulse 2s infinite;"></div>`;
        }
        
        const marker = new mapboxgl.Marker(markerEl).setLngLat(coordinates).addTo(map.current);
        
        markerEl.addEventListener('click', () => {
          setWaterAnimationActive(true);
          setSelectedStationCoords(coordinates);
          map.current.flyTo({
            center: coordinates,
            zoom: 12,
            pitch: 45,
            bearing: -17.6,
            speed: 1.2,
            curve: 1.4,
            easing: (t) => t,
            essential: true
          });
          
          showStationPopup(station);
          
          setTimeout(() => {
            if (onStationSelect) {
              onStationSelect(station);
            }
          }, 800);
        });
      }
    });
  }, [tickerData]);

  return (
    <div className="w-full h-screen overflow-hidden relative z-0">
      <div ref={mapContainer} className="w-full h-full relative z-0" />
    </div>
  );
};

export default MapboxMap;