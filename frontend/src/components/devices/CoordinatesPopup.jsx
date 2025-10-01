// src/components/map/CoordinatesPopup.js
import React, { useEffect, useRef } from 'react';
import mapboxgl from 'mapbox-gl'; // Tambahkan import mapboxgl

const CoordinatesPopup = ({ map, coordinates, onClose }) => {
  const popupRef = useRef(null);

  useEffect(() => {
    // Pastikan mapboxgl dan map tersedia
    if (!mapboxgl || !map || !coordinates) return;

    // Hapus popup yang ada sebelumnya
    if (popupRef.current) {
      popupRef.current.remove();
      popupRef.current = null;
    }

    try {
      const { lng, lat } = coordinates;
      
      // Format koordinat untuk ditampilkan
      const formattedLng = lng.toFixed(6);
      const formattedLat = lat.toFixed(6);
      
      // Buat popup baru
      const popup = new mapboxgl.Popup({ 
        closeButton: true,
        closeOnClick: false,
        className: 'coordinates-popup',
        onClose: () => {
          if (onClose) onClose();
        }
      })
      .setLngLat([lng, lat])
      .setHTML(`
        <div class="p-1">
          <h3 class="font-bold text-sm mb-2"></h3>
          <div class="text-xs">
            <p><strong>Longitude:</strong> ${formattedLng}</p>
            <p><strong>Latitude:</strong> ${formattedLat}</p>
          </div>
          <button 
            id="copy-coordinates" 
            class="mt-1 px-1 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors"
            data-lng="${formattedLng}" 
            data-lat="${formattedLat}"
          >
           
          </button>
        </div>
      `)
      .addTo(map);
      
      // Simpan referensi popup
      popupRef.current = popup;
      
      // Tambahkan event listener untuk tombol salin
      setTimeout(() => {
        const copyButton = document.getElementById('copy-coordinates');
        if (copyButton) {
          copyButton.addEventListener('click', function() {
            const lng = this.getAttribute('data-lng');
            const lat = this.getAttribute('data-lat');
            const coordinatesText = `${lat}, ${lng}`;
            
            // Salin ke clipboard
            navigator.clipboard.writeText(coordinatesText)
              .then(() => {
                // Ubah teks tombol sementara
                this.textContent = 'Tersalin!';
                this.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                this.classList.add('bg-green-500');
                
                // Kembalikan teks tombol setelah 2 detik
                setTimeout(() => {
                  this.textContent = 'Salin Koordinat';
                  this.classList.remove('bg-green-500');
                  this.classList.add('bg-blue-500', 'hover:bg-blue-600');
                }, 2000);
              })
              .catch(err => {
                console.error('Gagal menyalin koordinat: ', err);
              });
          });
        }
      }, 100);
    } catch (error) {
      console.error('Error creating coordinates popup:', error);
    }

    return () => {
      if (popupRef.current) {
        popupRef.current.remove();
        popupRef.current = null;
      }
    };
  }, [map, coordinates, onClose]);

  return null;
};

export default CoordinatesPopup;