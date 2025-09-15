import React, { useEffect, useRef, useState } from 'react';
import mapboxgl from 'mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';
import MapTooltip from './maptooltip'; // Import komponen tooltip

const MapboxMap = ({ tickerData, onStationSelect, onMapFocus, onStationChange, isAutoSwitchOn, onCloseSidebar }) => {
  const mapContainer = useRef(null);
  const map = useRef(null);
  const [waterAnimationActive, setWaterAnimationActive] = useState(false);
  const [selectedStationCoords, setSelectedStationCoords] = useState(null);
  
  // State untuk tooltip
  const [tooltip, setTooltip] = useState({
    visible: false,
    station: null,
    coordinates: null
  });

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
    // map.current.addControl(new mapboxgl.FullscreenControl(), 'top-right');
    map.current.addControl(new mapboxgl.ScaleControl(), 'bottom-left');
    
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
        markerEl.style.zIndex = '10';
        
        if (station.status === 'alert') {
          markerEl.innerHTML = `<div style="position: absolute; width: 100%; height: 100%; border-radius: 50%; background-color: ${getStatusColor(station.status)}; opacity: 0.7; animation: alert-pulse 2s infinite;"></div>`;
        }
        
        const marker = new mapboxgl.Marker(markerEl).setLngLat(coordinates).addTo(map.current);
        
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
          
          // Hide sidebar if auto switch is off
          if (!isAutoSwitchOn && onCloseSidebar) {
            onCloseSidebar();
          }
        });
      }
    });
  }, [tickerData]);
  
  // Event listener untuk klik di luar tooltip
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (tooltip.visible && !event.target.closest('.custom-marker') && !event.target.closest('.mapboxgl-popup-content')) {
        setTooltip(prev => ({ ...prev, visible: false }));
      }
    };
    
    document.addEventListener('click', handleClickOutside);
    return () => {
      document.removeEventListener('click', handleClickOutside);
    };
  }, [tooltip.visible]);

  // Event listener untuk map movement (pan/zoom) - hide sidebar when auto switch is off
  useEffect(() => {
    if (!map.current) return;

    let moveTimeout;
    const handleMapMove = () => {
      // Clear previous timeout
      if (moveTimeout) {
        clearTimeout(moveTimeout);
      }
      
      // Add small delay to prevent sidebar from closing too quickly
      moveTimeout = setTimeout(() => {
        // Hide sidebar if auto switch is off and user moves the map
        if (!isAutoSwitchOn && onCloseSidebar) {
          onCloseSidebar();
        }
      }, 500); // 500ms delay
    };

    // Add event listeners for map movement
    map.current.on('moveend', handleMapMove);
    map.current.on('zoomend', handleMapMove);

    return () => {
      if (moveTimeout) {
        clearTimeout(moveTimeout);
      }
      if (map.current) {
        map.current.off('moveend', handleMapMove);
        map.current.off('zoomend', handleMapMove);
      }
    };
  }, [isAutoSwitchOn, onCloseSidebar]);
  
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