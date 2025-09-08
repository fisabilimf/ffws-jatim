import React, { useEffect, useRef, useState } from 'react';
import mapboxgl from 'mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';

const MapboxMap = ({ tickerData }) => {
  const mapContainer = useRef(null);
  const map = useRef(null);
  const [selectedStation, setSelectedStation] = useState(null);
  const [isSidecardOpen, setIsSidecardOpen] = useState(false);

  // Initialize map only once
  useEffect(() => {
    // Set your Mapbox access token here
    // You need to get this from https://account.mapbox.com/
    mapboxgl.accessToken = 'pk.eyJ1IjoiZGl0b2ZhdGFoaWxsYWgxIiwiYSI6ImNtZjNveGloczAwNncya3E1YzdjcTRtM3MifQ.kIf5rscGYOzvvBcZJ41u8g';

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

    // Cleanup function
    return () => {
      if (map.current) {
        map.current.remove();
        map.current = null;
      }
    };
  }, []); // Empty dependency array - only run once

  // Update markers when tickerData changes
  useEffect(() => {
    if (!map.current || !tickerData) return;

    // Clear existing markers
    const existingMarkers = document.querySelectorAll('.custom-marker');
    existingMarkers.forEach(marker => marker.remove());

    // Add new markers
    tickerData.forEach((station) => {
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

        // Add marker to map
        const marker = new mapboxgl.Marker(markerEl)
          .setLngLat(coordinates)
          .addTo(map.current);

        // Add click event to marker
        markerEl.addEventListener('click', () => {
          setSelectedStation(station);
          setIsSidecardOpen(true);
        });
      }
    });
  }, [tickerData]); // Only update markers when tickerData changes

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

  const getStatusBgColor = (status) => {
    switch (status) {
      case 'safe': return 'bg-green-100';
      case 'warning': return 'bg-yellow-100';
      case 'alert': return 'bg-red-100';
      default: return 'bg-gray-100';
    }
  };

  return (
    <div className="w-full h-screen overflow-hidden relative">
      <div ref={mapContainer} className="w-full h-full" />
      
      {/* Sidecard - Google Maps Style */}
      {isSidecardOpen && selectedStation && (
        <div className="absolute top-0 left-0 w-80 h-full bg-white shadow-2xl transform transition-transform duration-300 ease-in-out z-10">
          {/* Header */}
          <div className="bg-white p-4 border-b border-gray-200">
            <div className="flex items-center justify-between">
              <button
                onClick={() => setIsSidecardOpen(false)}
                className="p-2 hover:bg-gray-100 rounded-full transition-colors"
              >
                <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
              <h3 className="text-lg font-semibold text-gray-800">Detail Stasiun</h3>
              <div className="w-5"></div>
            </div>
          </div>

          {/* Content */}
          <div className="p-4 space-y-4">
            {/* Station Image Placeholder */}
            <div className="w-full h-48 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
              <div className="text-center">
                <div className="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-2">
                  <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
                <p className="text-blue-600 font-medium">Stasiun Monitoring</p>
              </div>
            </div>

            {/* Station Info */}
            <div>
              <h2 className="text-xl font-bold text-gray-900 mb-2">{selectedStation.name}</h2>
              <div className="flex items-center space-x-2 mb-3">
                <div className={`w-3 h-3 rounded-full ${getStatusBgColor(selectedStation.status)}`}></div>
                <span className={`text-sm font-medium ${getStatusTextColor(selectedStation.status)}`}>
                  {getStatusText(selectedStation.status)}
                </span>
              </div>
              <p className="text-gray-600 text-sm">{selectedStation.location}</p>
            </div>

            {/* Water Level Info */}
            <div className="bg-gray-50 p-4 rounded-lg">
              <h3 className="font-semibold text-gray-800 mb-3">Informasi Level Air</h3>
              <div className="space-y-3">
                <div className="flex justify-between items-center">
                  <span className="text-gray-600">Level Saat Ini:</span>
                  <span className="text-2xl font-bold text-blue-600">{selectedStation.value} {selectedStation.unit}</span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-gray-600">Status:</span>
                  <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusBgColor(selectedStation.status)} ${getStatusTextColor(selectedStation.status)}`}>
                    {getStatusText(selectedStation.status)}
                  </span>
                </div>
              </div>
            </div>

            {/* Action Buttons */}
            <div className="space-y-2">
              <button className="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                Lihat Detail Lengkap
              </button>
              <button className="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                Simpan ke Favorit
              </button>
            </div>

            {/* Additional Info */}
            <div className="border-t pt-4">
              <h4 className="font-medium text-gray-800 mb-2">Informasi Tambahan</h4>
              <div className="space-y-2 text-sm text-gray-600">
                <p>• Stasiun aktif 24/7</p>
                <p>• Update data real-time</p>
                <p>• Monitoring otomatis</p>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default MapboxMap;