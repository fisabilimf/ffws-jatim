import React, { useEffect, useRef, useState } from 'react';
import mapboxgl from 'mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';
import StationDetail from './StationDetail';

const MapboxMap = ({ tickerData, onStationSelect }) => {
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
          if (onStationSelect) {
            onStationSelect(station);
          } else {
            setSelectedStation(station);
            setIsSidecardOpen(true);
          }
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


  return (
    <div className="w-full h-screen overflow-hidden relative z-0">
      <div ref={mapContainer} className="w-full h-full relative z-0" />
      
      {/* StationDetail Component */}
      <StationDetail 
        selectedStation={selectedStation}
        onClose={() => setIsSidecardOpen(false)}
        tickerData={tickerData}
      />
    </div>
  );
};

export default MapboxMap;