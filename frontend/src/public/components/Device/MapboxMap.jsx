import React, { useEffect, useRef, useState } from 'react';
import mapboxgl from 'mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';
import MapTooltip from './maptooltip'; // Import komponen tooltip
const MapboxMap = ({ tickerData, onStationSelect, onMapFocus, onStationChange }) => {
  const mapContainer = useRef(null);
  const map = useRef(null);
  const markersRef = useRef([]);
  const [waterAnimationActive, setWaterAnimationActive] = useState(false);
  const [selectedStationCoords, setSelectedStationCoords] = useState(null);
  
  // State untuk tooltip
  const [tooltip, setTooltip] = useState({
    visible: false,
    station: null,
    coordinates: null
  });
  // Tambahkan animasi CSS untuk pulse effect
  useEffect(() => {
    const style = document.createElement('style');
    style.textContent = `
      @keyframes alert-pulse {
        0% { transform: scale(1); opacity: 0.7; }
        50% { transform: scale(1.5); opacity: 0.3; }
        100% { transform: scale(1); opacity: 0.7; }
      }
    `;
    document.head.appendChild(style);
    return () => {
      document.head.removeChild(style);
    };
  }, []);
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
    // Tampilkan tooltip setelah jeda singkat
    setTimeout(() => {
      if (tickerData) {
        const station = tickerData.find(s => s.id === stationId);
        if (station) {
          const coordinates = getStationCoordinates(station.name);
          if (coordinates) {
            setTooltip({
              visible: true,
              station: station,
              coordinates: coordinates
            });
          }
        }
      }
    }, 800);
  };
  // Handler untuk auto switch dari toggle
  const handleAutoSwitch = (station, index) => {
    if (!map.current || !station) return;
    const coordinates = getStationCoordinates(station.name);
    if (!coordinates) return;
    
    setWaterAnimationActive(true);
    setSelectedStationCoords(coordinates);
    // Fly to station
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
    // Munculkan tooltip dengan jeda kecil
    setTimeout(() => {
      setTooltip({
        visible: true,
        station: station,
        coordinates: coordinates
      });
    }, 800);
  };
  // Handler untuk menampilkan detail sidebar dari tooltip
  const handleShowDetail = (station) => {
    // Tutup tooltip
    setTooltip(prev => ({ ...prev, visible: false }));
    // Panggil onStationSelect untuk membuka sidebar
    if (onStationSelect) {
      onStationSelect(station);
    }
  };
  // Handler untuk menutup tooltip
  const handleCloseTooltip = () => {
    setTooltip(prev => ({ ...prev, visible: false }));
  };
  // Helper functions
  const getStatusColor = (status) => {
    switch (status) {
      case 'safe': return '#10B981';
      case 'warning': return '#F59E0B';
      case 'alert': return '#EF4444';
      default: return '#6B7280';
    }
  };
  // Function untuk mendapatkan icon SVG sesuai status - UKURAN DIPERBESAR
  const getStatusIcon = (status) => {
    const iconSize = 27; // Diperbesar dari 20 menjadi 30
    const iconColor = 'white';
    switch (status) {
      case 'safe':
        return `<svg width="${iconSize}" height="${iconSize}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="${iconColor}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>`;
      case 'warning':
        return `<svg width="${iconSize}" height="${iconSize}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 9V13M12 17.0195V17M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="${iconColor}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>`;
      case 'alert':
        return `<svg width="${iconSize}" height="${iconSize}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 7.25V13M12 16.75V16.76M10.29 3.86L1.82 18A2 2 0 0 0 3.55 21H20.45A2 2 0 0 0 22.18 18L13.71 3.86A2 2 0 0 0 10.29 3.86Z" stroke="${iconColor}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>`;
      default:
        return `<svg width="${iconSize}" height="${iconSize}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12" cy="12" r="9" stroke="${iconColor}" stroke-width="2"/>
        </svg>`;
    }
  };
  const getStationCoordinates = (stationName) => {
    // Hanya 20 stasiun pertama
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
      'Stasiun Tulungagung': [111.9000, -8.0667],
      'Stasiun Bangil': [112.7333, -7.6000],
      'Stasiun Lawang': [112.6833, -7.8333],
      'Stasiun Singosari': [112.6500, -7.9000],
      'Stasiun Wates': [110.3569, -7.9133],
      'Stasiun Lempuyangan': [110.3739, -7.7884],
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
  // Expose handleAutoSwitch ke window object
  useEffect(() => {
    window.mapboxAutoSwitch = handleAutoSwitch;
    return () => {
      if (window.mapboxAutoSwitch) {
        delete window.mapboxAutoSwitch;
      }
    };
  }, [tickerData]);
  // Initialize map
  useEffect(() => {
    if (!mapboxgl.accessToken) {
      mapboxgl.accessToken = 'pk.eyJ1IjoiZGl0b2ZhdGFoaWxsYWgxIiwiYSI6ImNtZjNveGloczAwNncya3E1YzdjcTRtM3MifQ.kIf5rscGYOzvvBcZJ41u8g';
    }
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
    map.current.addControl(new mapboxgl.ScaleControl(), 'bottom-left');
    // Tambahkan event listener untuk drag
    map.current.on('dragstart', () => {
      // Trigger custom event to stop auto switch
      const event = new CustomEvent('userInteraction', {
        detail: { source: 'mapDrag' }
      });
      document.dispatchEvent(event);
    });
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
    // Hapus marker yang ada
    markersRef.current.forEach(marker => {
      if (marker && marker.remove) {
        marker.remove();
      }
    });
    markersRef.current = [];
    tickerData.forEach((station) => {
      const coordinates = getStationCoordinates(station.name);
      if (coordinates) {
        try {
          const markerEl = document.createElement('div');
          markerEl.className = 'custom-marker';
          // UKURAN MARKER DIPERBESAR
          markerEl.style.width = '27px'; // Diperbesar dari 20px menjadi 30px
          markerEl.style.height = '27px'; // Diperbesar dari 20px menjadi 30px
          markerEl.style.borderRadius = '50%';
          markerEl.style.backgroundColor = getStatusColor(station.status);
          markerEl.style.border = '3px solid white'; // Diperbesar dari 2px menjadi 3px
          markerEl.style.boxShadow = '0 4px 8px rgba(0,0,0,0.4)';
          markerEl.style.cursor = 'pointer';
          markerEl.style.zIndex = '10';
          markerEl.style.display = 'flex';
          markerEl.style.alignItems = 'center';
          markerEl.style.justifyContent = 'center';
          // Tambahkan icon sesuai status
          const iconSvg = getStatusIcon(station.status);
          markerEl.innerHTML = iconSvg;
          // Tambahkan pulse animation untuk status alert
          if (station.status === 'alert') {
            const pulseEl = document.createElement('div');
            pulseEl.style.position = 'absolute';
            pulseEl.style.width = '100%';
            pulseEl.style.height = '100%';
            pulseEl.style.borderRadius = '50%';
            pulseEl.style.backgroundColor = getStatusColor(station.status);
            pulseEl.style.opacity = '0.7';
            pulseEl.style.animation = 'alert-pulse 2s infinite';
            pulseEl.style.zIndex = '-1';
            markerEl.appendChild(pulseEl);
          }
          const marker = new mapboxgl.Marker(markerEl).setLngLat(coordinates).addTo(map.current);
          markersRef.current.push(marker);
          // Event untuk klik marker
          markerEl.addEventListener('click', (e) => {
            e.stopPropagation(); // Mencegah event bubbling
            setWaterAnimationActive(true);
            setSelectedStationCoords(coordinates);
            // Animasi flyTo
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
            // Tampilkan tooltip
            setTooltip({
              visible: true,
              station: station,
              coordinates: coordinates
            });
            // Trigger custom event to stop auto switch
            const event = new CustomEvent('userInteraction', {
              detail: { source: 'mapMarker', stationId: station.id }
            });
            document.dispatchEvent(event);
          });
        } catch (error) {
          console.error('Error creating marker for station:', station.name, error);
        }
      }
    });
  }, [tickerData]);
  // Event listener untuk klik di luar tooltip
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (tooltip.visible &&
          !event.target.closest('.custom-marker') &&
          !event.target.closest('.mapboxgl-popup-content') &&
          !event.target.closest('.map-tooltip')) {
        setTooltip(prev => ({ ...prev, visible: false }));
      }
    };
    document.addEventListener('click', handleClickOutside);
    return () => {
      document.removeEventListener('click', handleClickOutside);
    };
  }, [tooltip.visible]);
  return (
    <div className="w-full h-screen overflow-hidden relative z-0">
      <div ref={mapContainer} className="w-full h-full relative z-0" />
      <MapTooltip
        map={map.current}
        station={tooltip.station}
        isVisible={tooltip.visible}
        coordinates={tooltip.coordinates}
        onShowDetail={handleShowDetail}
        onClose={handleCloseTooltip}
      />
    </div>
  );
};
export default MapboxMap;