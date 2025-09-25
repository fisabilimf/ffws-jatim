import React, { useEffect, useRef } from 'react';
import mapboxgl from 'mapbox-gl';

const MapTooltip = ({ map, station, isVisible, coordinates, onShowDetail, onClose }) => {
  const popupRef = useRef(null);
  
  useEffect(() => {
    if (!map || !isVisible || !station || !coordinates) {
      if (popupRef.current) {
        popupRef.current.remove();
        popupRef.current = null;
      }
      return;
    }
    
    // Hapus popup yang ada sebelumnya
    if (popupRef.current) {
      popupRef.current.remove();
    }
    
    const getStatusColor = (status) => {
      switch (status) {
        case 'safe': return 'bg-green-500';
        case 'warning': return 'bg-yellow-500';
        case 'alert': return 'bg-red-500';
        default: return 'bg-gray-500';
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
    
    // Buat konten popup
    const popupContent = document.createElement('div');
    popupContent.className = 'map-tooltip-content';
    popupContent.innerHTML = `
      <div class="tooltip-header">
        <div class="flex items-center">
          <div class="w-3 h-3 rounded-full ${getStatusColor(station.status)} mr-2"></div>
          <h3 class="font-bold text-gray-900">${station.name.replace('Stasiun ', '')}</h3>
        </div>
        <button class="tooltip-close-btn">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <div class="tooltip-level">
        <div class="flex justify-between items-center">
          <span class="text-sm text-gray-600">Level Air:</span>
          <div class="text-right">
            <span class="font-semibold text-lg block">${station.value}</span>
            <span class="text-xs text-gray-500">${station.unit}</span>
          </div>
        </div>
        <div class="text-xs text-gray-500 mt-1">
          Status: ${getStatusText(station.status)}
        </div>
      </div>
      
      <div class="tooltip-info">
        <div class="text-xs text-gray-500 mb-1">ID: ${station.id}</div>
        <div class="text-xs text-gray-500 truncate">${station.location}</div>
      </div>
      
      <button class="tooltip-detail-btn">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Lihat Detail
      </button>
    `;
    
    // Tambahkan style untuk popup
    const style = document.createElement('style');
    style.textContent = `
      .mapboxgl-popup-content {
        padding: 0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      }
      
      /* Reset semua button styling */
      button {
        background: none;
        border: none;
        outline: none;
        box-shadow: none;
      }
      
      button:focus {
        outline: none;
        border: none;
        box-shadow: none;
      }
      
      .map-tooltip-content {
        padding: 12px;
        width: 240px;
      }
      
      .tooltip-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
      }
      
      .tooltip-header h3 {
        font-size: 14px;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 160px;
      }
      
      .tooltip-close-btn {
        background: none !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        color: #9CA3AF;
        cursor: pointer;
        padding: 0;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: auto;
        height: auto;
        font-weight: bold;
      }
      
      .tooltip-close-btn svg {
        stroke-width: 3;
        font-weight: bold;
      }
      
      .tooltip-close-btn:hover {
        color: #4B5563;
        background: none !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
      }
      
      .tooltip-close-btn:focus {
        outline: none !important;
        border: none !important;
        box-shadow: none !important;
      }
      
      .tooltip-level {
        margin-bottom: 10px;
      }
      
      .tooltip-info {
        margin-bottom: 12px;
        padding-bottom: 10px;
      }
      
      .tooltip-detail-btn {
        width: 100%;
        background-color: #3B82F6;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 8px;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.2s;
      }
      
      .tooltip-detail-btn:hover {
        background-color: #2563EB;
      }
    `;
    document.head.appendChild(style);
    
    // Buat popup
    popupRef.current = new mapboxgl.Popup({
      closeButton: false,
      closeOnClick: false,
      offset: [0, -30] // Offset untuk menempatkan popup di atas marker
    })
      .setLngLat(coordinates)
      .setDOMContent(popupContent)
      .addTo(map);
    
    // Event listener untuk tombol detail
    const detailBtn = popupContent.querySelector('.tooltip-detail-btn');
    if (detailBtn) {
      detailBtn.addEventListener('click', () => {
        onShowDetail(station);
      });
    }
    
    // Event listener untuk tombol close
    const closeBtn = popupContent.querySelector('.tooltip-close-btn');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => {
        onClose();
      });
    }
    
    return () => {
      if (popupRef.current) {
        popupRef.current.remove();
        popupRef.current = null;
      }
    };
  }, [map, isVisible, station, coordinates, onShowDetail, onClose]);
  
  return null; // Komponen ini tidak merender apa-apa secara langsung
};

export default MapTooltip;