// src/components/WaterMeasurementPopup.js
import React, { useEffect } from 'react';
import mapboxgl from 'mapbox-gl';

const WaterMeasurementPopup = ({ map, isVisible, coordinates, elevation, status, statusColor, onClose }) => {
  useEffect(() => {
    if (!map || !isVisible || !coordinates) return;

    // Hapus popup yang ada sebelumnya
    const existingPopup = document.getElementById('water-measurement-popup');
    if (existingPopup) {
      existingPopup.remove();
    }

    // Tentukan status berdasarkan ketinggian
    let statusText, statusColorValue;
    if (elevation > 5) {
      statusText = 'Bahaya';
      statusColorValue = '#EF4444';
    } else if (elevation > 2) {
      statusText = 'Waspada';
      statusColorValue = '#F59E0B';
    } else {
      statusText = 'Aman';
      statusColorValue = '#10B981';
    }

    // Buat elemen popup
    const popupEl = document.createElement('div');
    popupEl.id = 'water-measurement-popup';
    popupEl.className = 'water-measurement-popup';
    popupEl.innerHTML = `
      <div class="p-3 bg-white rounded-lg shadow-lg border-l-4" style="border-color: ${statusColorValue}; min-width: 220px;">
        <div class="flex items-center mb-2">
          <div class="w-3 h-3 rounded-full mr-2" style="background-color: ${statusColorValue};"></div>
          <h3 class="font-bold text-gray-800">Pengukuran Permukaan Air</h3>
        </div>
        <div class="text-sm text-gray-600 space-y-1">
          <div class="flex justify-between">
            <span class="font-medium">Latitude:</span>
            <span class="font-mono">${coordinates[1].toFixed(6)}</span>
          </div>
          <div class="flex justify-between">
            <span class="font-medium">Longitude:</span>
            <span class="font-mono">${coordinates[0].toFixed(6)}</span>
          </div>
          <div class="flex justify-between">
            <span class="font-medium">Tinggi Air:</span>
            <span class="font-bold text-lg">${elevation.toFixed(2)} m</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="font-medium">Status:</span>
            <span class="font-bold px-2 py-1 rounded text-white text-xs" style="background-color: ${statusColorValue};">${statusText}</span>
          </div>
        </div>
        <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 close-btn">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    `;

    // Tambahkan event listener untuk tombol close
    const closeBtn = popupEl.querySelector('.close-btn');
    if (closeBtn) {
      closeBtn.addEventListener('click', onClose);
    }

    // Buat popup Mapbox
    const popup = new mapboxgl.Popup({
      closeButton: false,
      closeOnClick: false,
      className: 'water-measurement-popup-container'
    })
      .setLngLat(coordinates)
      .setDOMContent(popupEl)
      .addTo(map);

    return () => {
      popup.remove();
    };
  }, [map, isVisible, coordinates, elevation, status, statusColor, onClose]);

  return null;
};

export default WaterMeasurementPopup;