import React, { useEffect, useRef, useState } from 'react';
import mapboxgl from 'mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';
import MapTooltip from './maptooltip'; // Import komponen tooltip

const MapboxMap = ({ tickerData, onStationSelect, onMapFocus }) => {
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
    if (!mapboxgl.accessToken) {
      mapboxgl.accessToken = 'pk.eyJ1IjoiZGl0b2ZhdGFoaWxsYWgxIiwiYSI6ImNtZjNveGloczAwNncya3E1YzdjcTRtM3MifQ.kIf5rscGYOzvvBcZJ41u8g';
    }
    
    if (map.current) return;
    
    try {
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
      
      // Handle map errors
      map.current.on('error', (e) => {
        console.error('Mapbox error:', e.error);
      });
    } catch (error) {
      console.error('Error initializing map:', error);
    }
    
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
          markerEl.style.width = '30px';
          markerEl.style.height = '30px';
          markerEl.style.borderRadius = '50%';
          markerEl.style.backgroundColor = getStatusColor(station.status);
          markerEl.style.border = '3px solid white';
          markerEl.style.boxShadow = '0 4px 8px rgba(0,0,0,0.4)';
          markerEl.style.cursor = 'pointer';
          markerEl.style.zIndex = '10';
          
          if (station.status === 'alert') {
            markerEl.innerHTML = `<div style="position: absolute; width: 100%; height: 100%; border-radius: 50%; background-color: ${getStatusColor(station.status)}; opacity: 0.7; animation: alert-pulse 2s infinite;"></div>`;
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

MapboxMap.displayName = 'MapboxMap';
export default MapboxMap;