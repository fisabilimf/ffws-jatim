import React, { useEffect, useRef, useState } from 'react';
import mapboxgl from 'mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';
import MapTooltip from './maptooltip'; // Import komponen tooltip

const MapboxMap = ({ onStationSelect, onMapFocus, onStationChange }) => {
  const mapContainer = useRef(null);
  const map = useRef(null);
  const [waterAnimationActive, setWaterAnimationActive] = useState(false);
  const [selectedStationCoords, setSelectedStationCoords] = useState(null);
  const [devicesData, setDevicesData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  
  // State untuk tooltip
  const [tooltip, setTooltip] = useState({
    visible: false,
    station: null,
    coordinates: null
  });

  // Fetch devices data from API
  const fetchDevicesData = async () => {
    try {
      setLoading(true);
      const response = await fetch('http://localhost:8000/api/devices/map', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          // Add authorization header if needed
          // 'Authorization': `Bearer ${localStorage.getItem('token')}`,
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();
      
      if (result.success) {
        setDevicesData(result.data);
        setError(null);
      } else {
        throw new Error(result.message || 'Failed to fetch devices data');
      }
    } catch (err) {
      console.error('Error fetching devices data:', err);
      setError(err.message);
      // Fallback to mock data for development
      setDevicesData([]);
    } finally {
      setLoading(false);
    }
  };

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
      if (devicesData) {
        const device = devicesData.find(d => d.id === stationId);
        if (device) {
          setTooltip({
            visible: true,
            station: device,
            coordinates: [device.longitude, device.latitude]
          });
        }
      }
    }, 800);
  };

  // Handler untuk auto switch dari toggle
  const handleAutoSwitch = (device, index) => {
    if (!map.current || !device) return;
    
    const coordinates = [device.longitude, device.latitude];
    
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
        station: device,
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
      case 'danger': return '#EF4444';
      default: return '#6B7280';
    }
  };

  // Expose handleMapFocus ke window object
  useEffect(() => {
    window.mapboxAutoFocus = handleMapFocus;
    return () => {
      if (window.mapboxAutoFocus) {
        delete window.mapboxAutoFocus;
      }
    };
  }, [devicesData]);

  // Expose handleAutoSwitch ke window object
  useEffect(() => {
    window.mapboxAutoSwitch = handleAutoSwitch;
    return () => {
      if (window.mapboxAutoSwitch) {
        delete window.mapboxAutoSwitch;
      }
    };
  }, [devicesData]);

  // Fetch devices data on component mount
  useEffect(() => {
    fetchDevicesData();
    
    // Set up periodic refresh (optional)
    const interval = setInterval(fetchDevicesData, 30000); // Refresh every 30 seconds
    
    return () => clearInterval(interval);
  }, []);
  
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
  
  // Update markers when devicesData changes
  useEffect(() => {
    if (!map.current || !devicesData || devicesData.length === 0) return;
    
    const existingMarkers = document.querySelectorAll('.custom-marker');
    existingMarkers.forEach(marker => marker.remove());
    
    devicesData.forEach((device) => {
      if (!device.latitude || !device.longitude) return;
      
      const coordinates = [device.longitude, device.latitude];
      
      const markerEl = document.createElement('div');
      markerEl.className = 'custom-marker';
      markerEl.style.width = '30px';
      markerEl.style.height = '30px';
      markerEl.style.borderRadius = '50%';
      markerEl.style.backgroundColor = getStatusColor(device.status);
      markerEl.style.border = '3px solid white';
      markerEl.style.boxShadow = '0 4px 8px rgba(0,0,0,0.4)';
      markerEl.style.cursor = 'pointer';
      markerEl.style.zIndex = '10';
      
      if (device.status === 'danger') {
        markerEl.innerHTML = `<div style="position: absolute; width: 100%; height: 100%; border-radius: 50%; background-color: ${getStatusColor(device.status)}; opacity: 0.7; animation: alert-pulse 2s infinite;"></div>`;
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
          station: device,
          coordinates: coordinates
        });
      });
    });
  }, [devicesData]);
  
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
  
  return (
    <div className="w-full h-screen overflow-hidden relative z-0">
      {loading && (
        <div className="absolute top-4 right-4 z-20 bg-blue-100 text-blue-800 px-4 py-2 rounded-lg shadow-md">
          <div className="flex items-center space-x-2">
            <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-800"></div>
            <span>Loading devices...</span>
          </div>
        </div>
      )}
      
      {error && (
        <div className="absolute top-4 right-4 z-20 bg-red-100 text-red-800 px-4 py-2 rounded-lg shadow-md">
          <div className="flex items-center space-x-2">
            <span>⚠️ {error}</span>
          </div>
        </div>
      )}
      
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