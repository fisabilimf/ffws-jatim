import React, { useEffect, useRef } from 'react';
import mapboxgl from 'mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';

const MapboxMap = ({ tickerData }) => {
  const mapContainer = useRef(null);
  const map = useRef(null);

  useEffect(() => {
    // Set your Mapbox access token here
    // You need to get this from https://account.mapbox.com/
    mapboxgl.accessToken = 'pk.eyJ1IjoiZXhhbXBsZSIsImEiOiJjbGV4YW1wbGUifQ.example';

    if (map.current) return; // initialize map only once

    map.current = new mapboxgl.Map({
      container: mapContainer.current,
      style: 'mapbox://styles/mapbox/streets-v12',
      center: [112.5, -7.5], // Jawa Timur coordinates
      zoom: 8
    });

    // Add navigation controls
    map.current.addControl(new mapboxgl.NavigationControl(), 'top-right');

    // Add fullscreen control
    map.current.addControl(new mapboxgl.FullscreenControl(), 'top-right');

    // Add scale control
    map.current.addControl(new mapboxgl.ScaleControl(), 'bottom-left');

    // Wait for map to load before adding markers
    map.current.on('load', () => {
      // Add markers for each station
      tickerData.forEach((station) => {
        // Get coordinates based on station name (you can replace with actual coordinates)
        const coordinates = getStationCoordinates(station.name);
        
        if (coordinates) {
          // Create custom marker element
          const markerEl = document.createElement('div');
          markerEl.className = 'custom-marker';
          markerEl.style.width = '20px';
          markerEl.style.height = '20px';
          markerEl.style.borderRadius = '50%';
          markerEl.style.backgroundColor = getStatusColor(station.status);
          markerEl.style.border = '2px solid white';
          markerEl.style.boxShadow = '0 2px 4px rgba(0,0,0,0.3)';
          markerEl.style.cursor = 'pointer';

          // Create popup
          const popup = new mapboxgl.Popup({ offset: 25 }).setHTML(`
            <div class="p-3">
              <h3 class="font-bold text-lg mb-2">${station.name}</h3>
              <div class="space-y-2">
                <div class="flex justify-between">
                  <span class="text-gray-600">Status:</span>
                  <span class="font-medium ${getStatusTextColor(station.status)}">${getStatusText(station.status)}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Water Level:</span>
                  <span class="font-bold text-lg">${station.value} ${station.unit}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Location:</span>
                  <span class="text-sm">${station.location}</span>
                </div>
              </div>
            </div>
          `);

          // Add marker to map
          new mapboxgl.Marker(markerEl)
            .setLngLat(coordinates)
            .setPopup(popup)
            .addTo(map.current);
        }
      });
    });

    // Cleanup function
    return () => {
      if (map.current) {
        map.current.remove();
        map.current = null;
      }
    };
  }, [tickerData]);

  // Function to get station coordinates (you can replace with actual coordinates)
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

  const getStatusColor = (status) => {
    switch (status) {
      case 'safe': return '#10B981'; // green
      case 'warning': return '#F59E0B'; // yellow
      case 'alert': return '#EF4444'; // red
      default: return '#6B7280'; // gray
    }
  };

  const getStatusTextColor = (status) => {
    switch (status) {
      case 'safe': return 'text-green-600';
      case 'warning': return 'text-yellow-600';
      case 'alert': return 'text-red-600';
      default: return 'text-gray-600';
    }
  };

  const getStatusText = (status) => {
    switch (status) {
      case 'safe': return 'Aman';
      case 'warning': return 'Waspada';
      case 'alert': return 'Bahaya';
      default: return 'Tidak Diketahui';
    }
  };

  return (
    <div className="w-full h-96 rounded-lg overflow-hidden shadow-lg">
      <div className="bg-white p-4 border-b">
        <h3 className="text-lg font-semibold text-gray-800">Peta Monitoring Stasiun</h3>
        <p className="text-sm text-gray-600">Lokasi stasiun monitoring banjir di Jawa Timur</p>
      </div>
      <div ref={mapContainer} className="w-full h-full" />
      
      {/* Legend */}
      <div className="bg-white p-3 border-t">
        <div className="flex items-center justify-center space-x-6 text-sm">
          <div className="flex items-center space-x-2">
            <div className="w-3 h-3 rounded-full bg-green-500"></div>
            <span className="text-gray-700">Aman</span>
          </div>
          <div className="flex items-center space-x-2">
            <div className="w-3 h-3 rounded-full bg-yellow-500"></div>
            <span className="text-gray-700">Waspada</span>
          </div>
          <div className="flex items-center space-x-2">
            <div className="w-3 h-3 rounded-full bg-red-500"></div>
            <span className="text-gray-700">Bahaya</span>
          </div>
        </div>
      </div>
    </div>
  );
};

export default MapboxMap;