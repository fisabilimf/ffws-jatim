import React, { useEffect, useRef, useState, lazy, Suspense } from "react";
import mapboxgl from "mapbox-gl";
import "mapbox-gl/dist/mapbox-gl.css";

// Set Mapbox access token from environment variable
const mapboxToken = import.meta.env.VITE_MAPBOX_ACCESS_TOKEN;
if (!mapboxToken) {
    console.error('❌ VITE_MAPBOX_ACCESS_TOKEN not found in environment variables!');
    console.error('Please add VITE_MAPBOX_ACCESS_TOKEN to your .env file');
} else {
    mapboxgl.accessToken = mapboxToken;
    console.log('✅ Mapbox token loaded from environment');
}


// Lazy load MapTooltip untuk optimasi bundle
const MapTooltip = lazy(() => import("./devices/maptooltip"));

const MapboxMap = ({ devicesData, onStationSelect, onMapFocus, onStationChange }) => {
    const mapContainer = useRef(null);
    const map = useRef(null);
    const markersRef = useRef([]);
    const [selectedStationCoords, setSelectedStationCoords] = useState(null);
    
    // Use devicesData from props instead of fetching separately
    const devices = devicesData || [];

    // State untuk tooltip
    const [tooltip, setTooltip] = useState({
        visible: false,
        station: null,
        coordinates: null,
    });
    // Refs untuk kontrol zoom ketika tooltip tampil
    const tooltipRef = useRef(tooltip);
    const previousZoomRef = useRef(null);
    useEffect(() => { tooltipRef.current = tooltip; }, [tooltip]);

    // Tambahkan animasi CSS untuk pulse effect
    useEffect(() => {
        const style = document.createElement("style");
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
        setSelectedStationCoords([lng, lat]);

        // Konfigurasi animasi yang konsisten dengan autoswitch
        const FOCUS_ZOOM = zoom || Number(import.meta?.env?.VITE_FLYTO_ZOOM ?? 14);
        const FOCUS_SPEED = Number(import.meta?.env?.VITE_FLYTO_SPEED ?? 1.2);
        const FOCUS_CURVE = Number(import.meta?.env?.VITE_FLYTO_CURVE ?? 1.4);
        const FOCUS_PITCH = Number(import.meta?.env?.VITE_FLYTO_PITCH ?? 45);
        const FOCUS_BEARING = Number(import.meta?.env?.VITE_FLYTO_BEARING ?? -17.6);
        
        map.current.flyTo({
            center: [lng, lat],
            zoom: FOCUS_ZOOM,
            pitch: FOCUS_PITCH,
            bearing: FOCUS_BEARING,
            speed: FOCUS_SPEED,
            curve: FOCUS_CURVE,
            easing: (t) => t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2,
            essential: true,
        });
        // Tampilkan tooltip setelah jeda singkat
        setTimeout(() => {
            if (devicesData) {
                const station = devicesData.find((s) => s.id === stationId);
                if (station) {
                    const coordinates = getStationCoordinates(station.name);
                    if (coordinates) {
                        setTooltip({
                            visible: true,
                            station: station,
                            coordinates: coordinates,
                        });
                    }
                }
            }
        }, 800);
    };
    // Handler untuk auto switch dari toggle
    const handleAutoSwitch = (device, index) => {
        console.log(`=== HANDLE AUTO SWITCH DEBUG ===`);
        console.log(`Device:`, device);
        console.log(`Index:`, index);
        console.log(`Device name:`, device?.name || device?.device_name || device?.station_name || 'undefined');
        console.log(`Device coordinates:`, device?.coordinates);
        console.log(`Device latitude:`, device?.latitude);
        console.log(`Device longitude:`, device?.longitude);
        
        if (!map.current || !device) {
            console.warn("Map or device not available for auto switch");
            return;
        }
        
        let coordinates = null;
        
        try {
            // Check if device has direct coordinates
            if (device.coordinates && Array.isArray(device.coordinates) && device.coordinates.length >= 2) {
                coordinates = device.coordinates;
                console.log("Using direct coordinates from device object:", coordinates);
            } else if (device.latitude && device.longitude) {
                const lat = parseFloat(device.latitude);
                const lng = parseFloat(device.longitude);
                if (!isNaN(lat) && !isNaN(lng)) {
                    coordinates = [lng, lat];
                    console.log("Using lat/lng from device object:", coordinates);
                } else {
                    console.warn("Invalid latitude/longitude values:", device.latitude, device.longitude);
                }
            } else {
                console.log("No direct coordinates found, trying name lookup...");
                // Fallback to searching by name
                const deviceName = device.name || device.device_name || device.station_name;
                coordinates = getStationCoordinates(deviceName);
                console.log("Looked up coordinates by name:", coordinates);
            }
            
            if (!coordinates) {
                const deviceName = device.name || device.device_name || device.station_name;
                console.warn("No coordinates found for device:", deviceName);
                console.log("Available devices:", devices.map(d => ({
                    name: d.name || d.device_name || d.station_name,
                    hasLatLng: !!(d.latitude && d.longitude),
                    hasCoordinates: !!(d.coordinates && Array.isArray(d.coordinates))
                })));
                
                // Coba lakukan pencarian lebih agresif berdasarkan nama
                const deviceLowerName = deviceName.toLowerCase();
                
                const matchedDevice = devices.find(d => {
                    const dName = d.name || d.device_name || d.station_name;
                    return dName.toLowerCase() === deviceLowerName || 
                           dName.toLowerCase().includes(deviceLowerName) || 
                           deviceLowerName.includes(dName.toLowerCase());
                });
                
                if (matchedDevice && matchedDevice.latitude && matchedDevice.longitude) {
                    const lat = parseFloat(matchedDevice.latitude);
                    const lng = parseFloat(matchedDevice.longitude);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        coordinates = [lng, lat];
                        console.log("Found coordinates through fuzzy matching:", coordinates);
                    }
                }
                
                if (!coordinates) {
                    console.error("Still could not find coordinates after fuzzy matching");
                    
                    // Jika tidak ada koordinat ditemukan, gunakan koordinat default
                    if (devices.length > 0 && devices[0].latitude && devices[0].longitude) {
                        const lat = parseFloat(devices[0].latitude);
                        const lng = parseFloat(devices[0].longitude);
                        if (!isNaN(lat) && !isNaN(lng)) {
                            coordinates = [lng, lat];
                            console.log("Using first device coordinates as fallback:", coordinates);
                        }
                    } else {
                        console.error("No valid coordinates found in any device");
                        return;
                    }
                }
            }
            
            // Validasi koordinat akhir
            if (!coordinates || coordinates.length < 2 || isNaN(coordinates[0]) || isNaN(coordinates[1])) {
                console.error("Invalid coordinates:", coordinates);
                return;
            }
            
            console.log("Final coordinates:", coordinates);
        } catch (error) {
            console.error("Error processing device coordinates:", error);
            return;
        }
        
        const deviceName = device.name || device.device_name || device.station_name;
        console.log("Auto switching to device:", deviceName, "at coordinates:", coordinates, "index:", index);
        setSelectedStationCoords(coordinates);
        
        // Simpan zoom sebelum fokus
        try { previousZoomRef.current = map.current.getZoom(); } catch (e) { previousZoomRef.current = 12; }
        
        // Intelligent zoom calculation based on device type and location
        const calculateOptimalZoom = (device, coordinates) => {
            // Base zoom level
            let baseZoom = Number(import.meta?.env?.VITE_FLYTO_ZOOM ?? 15);
            
            // Adjust zoom based on device type
            if (device.device_type === 'flood_gate' || device.device_type === 'dam') {
                baseZoom = 16; // Closer zoom for critical infrastructure
            } else if (device.device_type === 'weather_station') {
                baseZoom = 14; // Wider view for weather stations
            }
            
            // Adjust zoom based on location density
            const nearbyDevices = devices.filter(d => {
                if (!d.latitude || !d.longitude) return false;
                const distance = Math.sqrt(
                    Math.pow(parseFloat(d.longitude) - coordinates[0], 2) + 
                    Math.pow(parseFloat(d.latitude) - coordinates[1], 2)
                );
                return distance < 0.01; // Within ~1km
            });
            
            if (nearbyDevices.length > 3) {
                baseZoom = Math.max(baseZoom - 1, 12); // Zoom out for dense areas
            }
            
            return baseZoom;
        };
        
        const TARGET_ZOOM = calculateOptimalZoom(device, coordinates);
        const PADDING = { left: 360, right: 40, top: 80, bottom: 120 };
        
        // Enhanced animation configuration
        const FLYTO_SPEED = Number(import.meta?.env?.VITE_FLYTO_SPEED ?? 1.5);
        const FLYTO_CURVE = Number(import.meta?.env?.VITE_FLYTO_CURVE ?? 1.8);
        const FLYTO_PITCH = Number(import.meta?.env?.VITE_FLYTO_PITCH ?? 45);
        const FLYTO_BEARING = Number(import.meta?.env?.VITE_FLYTO_BEARING ?? -17.6);
        
        // Advanced easing function for smooth transitions
        const advancedEase = (t) => {
            // Ease-in-out with slight bounce for natural feel
            if (t < 0.5) {
                return 2 * t * t * (3 - 2 * t);
            } else {
                return 1 - 2 * (1 - t) * (1 - t) * (3 - 2 * (1 - t));
            }
        };
        
        // Get current map state for smooth transition
        const currentZoom = map.current.getZoom();
        const currentCenter = map.current.getCenter();
        const currentPitch = map.current.getPitch();
        const currentBearing = map.current.getBearing();
        
        // Calculate distance for speed adjustment
        const distance = Math.sqrt(
            Math.pow(coordinates[0] - currentCenter.lng, 2) + 
            Math.pow(coordinates[1] - currentCenter.lat, 2)
        );
        
        // Adjust speed based on distance
        const adjustedSpeed = distance > 0.1 ? FLYTO_SPEED * 0.8 : FLYTO_SPEED * 1.2;
        
        // Enhanced flyTo with intelligent parameters
        try {
            // Stop any existing animations
            try { map.current.stop(); } catch (_) {}
            
            // Multi-stage flyTo for better visual experience
            const flyToOptions = {
                center: coordinates,
                zoom: TARGET_ZOOM,
                pitch: FLYTO_PITCH,
                bearing: FLYTO_BEARING,
                speed: adjustedSpeed,
                curve: FLYTO_CURVE,
                easing: advancedEase,
                essential: true,
                padding: PADDING,
                // Add duration for consistency
                duration: Math.max(1500, distance * 2000)
            };
            
            console.log('=== ENHANCED FLY TO ===');
            console.log('Target coordinates:', coordinates);
            console.log('Target zoom:', TARGET_ZOOM);
            console.log('Distance:', distance);
            console.log('Adjusted speed:', adjustedSpeed);
            console.log('Fly to options:', flyToOptions);
            
            map.current.flyTo(flyToOptions);
            
            // Add event listeners for better control
            map.current.once('moveend', () => {
                console.log('Fly to completed successfully');
                // Ensure we're at the exact coordinates
                map.current.setCenter(coordinates);
            });
            
        } catch (e) {
            console.warn('Enhanced flyTo failed, using fallback:', e);
            // Fallback with basic flyTo
            map.current.flyTo({ 
                center: coordinates, 
                zoom: TARGET_ZOOM, 
                essential: true,
                speed: 1.0,
                duration: 1000
            });
        }
        
        // Munculkan tooltip setelah animasi flyTo selesai - timing yang dioptimasi
        const tooltipDelay = Math.max(1000, distance * 1500);
        setTimeout(() => {
            setTooltip({
                visible: true,
                station: device, // Keep as station for tooltip compatibility
                coordinates: coordinates,
            });
        }, tooltipDelay);
        
        // Jika fungsi onStationChange tersedia, panggil dengan device saat ini
        if (onStationChange) {
            onStationChange(device, index);
        }
    };
    // Handler untuk menampilkan detail sidebar dari tooltip
    const handleShowDetail = (station) => {
        console.log('=== HANDLE SHOW DETAIL DEBUG ===');
        console.log('Station to show detail:', station);
        console.log('onStationSelect function:', typeof onStationSelect);
        
        // Tutup tooltip
        setTooltip((prev) => ({ ...prev, visible: false }));
        console.log('Tooltip closed');
        
        // Panggil onStationSelect untuk membuka sidebar
        if (onStationSelect) {
            console.log('Calling onStationSelect...');
            onStationSelect(station);
        } else {
            console.error('onStationSelect function not available!');
        }
        console.log('=== END HANDLE SHOW DETAIL DEBUG ===');
    };
    // Handler untuk menutup tooltip
    const handleCloseTooltip = () => {
        setTooltip((prev) => ({ ...prev, visible: false }));
    };
    // Helper functions
    const getStatusColor = (status) => {
        switch (status) {
            case "safe":
                return "#10B981";
            case "warning":
                return "#F59E0B";
            case "alert":
                return "#EF4444";
            default:
                return "#6B7280";
        }
    };
    // Function untuk icon SVG sesuai status 
    const getStatusIcon = (status) => {
        const iconSize = 27; // Diperbesar dari 20 menjadi 30
        const iconColor = "white";
        switch (status) {
            case "safe":
                return `<svg width="${iconSize}" height="${iconSize}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="${iconColor}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>`;
            case "warning":
                return `<svg width="${iconSize}" height="${iconSize}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 9V13M12 17.0195V17M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="${iconColor}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>`;
            case "alert":
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
        // console.log(`Looking for coordinates for station: "${stationName}"`);
        if (!devices || devices.length === 0) {
            // console.log("Devices array is empty or not yet loaded.");
            return null;
        }
        
        // Pertama coba pencarian yang tepat - cek berbagai field name
        let device = devices.find((d) => 
            d.name === stationName || 
            d.device_name === stationName || 
            d.station_name === stationName
        );
        
        // Jika tidak ditemukan, coba pencarian case-insensitive
        if (!device) {
            device = devices.find((d) => 
                (d.name && d.name.toLowerCase() === stationName.toLowerCase()) ||
                (d.device_name && d.device_name.toLowerCase() === stationName.toLowerCase()) ||
                (d.station_name && d.station_name.toLowerCase() === stationName.toLowerCase())
            );
        }
        
        // Jika masih tidak ditemukan, coba pencarian parsial
        if (!device) {
            device = devices.find((d) => {
                const name = d.name || d.device_name || d.station_name;
                if (!name) return false;
                return name.toLowerCase().includes(stationName.toLowerCase()) || 
                       stationName.toLowerCase().includes(name.toLowerCase());
            });
        }
        
        if (device && device.latitude && device.longitude) {
            const coords = [parseFloat(device.longitude), parseFloat(device.latitude)];
            const deviceName = device.name || device.device_name || device.station_name;
            console.log(`Found coordinates for "${stationName}":`, coords);
            console.log(`Matched to device: "${deviceName}"`);
            return coords;
        }
        
        console.warn(`Coordinates not found for station: "${stationName}"`);
        console.log("Available devices:", devices.map(d => d.name || d.device_name || d.station_name || 'unnamed').join(", "));
        return null;
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
    // Expose handleAutoSwitch ke window object dengan error handling yang robust
    useEffect(() => {
        console.log("=== REGISTERING MAPBOX AUTO SWITCH FUNCTION ===");
        console.log("Map instance available:", !!map.current);
        console.log("Devices available:", devices.length);
        
        // Register the function to window object
        window.mapboxAutoSwitch = (device, index) => {
            const deviceName = device?.name || device?.device_name || device?.station_name;
            console.log("MapboxMap: mapboxAutoSwitch called with device:", deviceName, "index:", index);
            
            try {
                // Validasi input yang lebih ketat
                if (!device) {
                    console.error("Error: Device is undefined or null");
                    document.dispatchEvent(new CustomEvent('autoSwitchError', {
                        detail: { 
                            error: 'Device is undefined or null',
                            type: 'validation_error',
                            device: device,
                            index: index
                        }
                    }));
                    return;
                }
                
                if (!map.current) {
                    console.error("Error: Map is not initialized yet");
                    document.dispatchEvent(new CustomEvent('autoSwitchError', {
                        detail: { 
                            error: 'Map is not initialized yet',
                            type: 'map_not_ready',
                            device: device,
                            index: index
                        }
                    }));
                    return;
                }
                
                // Validasi koordinat sebelum memanggil handleAutoSwitch
                const hasDirectCoordinates = device.coordinates && Array.isArray(device.coordinates) && device.coordinates.length >= 2;
                const hasLatLng = device.latitude && device.longitude && 
                    !isNaN(parseFloat(device.latitude)) && !isNaN(parseFloat(device.longitude));
                const hasLookupCoordinates = getStationCoordinates(deviceName);
                
                const hasValidCoordinates = hasDirectCoordinates || hasLatLng || hasLookupCoordinates;
                
                if (!hasValidCoordinates) {
                    console.warn("Warning: No valid coordinates found for device:", deviceName);
                    console.log("Device details:", {
                        hasDirectCoordinates,
                        hasLatLng,
                        hasLookupCoordinates,
                        device: device
                    });
                    // Jangan return, biarkan handleAutoSwitch yang handle fallback
                }
                
                // Call the actual switch function
                handleAutoSwitch(device, index);
                
                // Dispatch success event
                document.dispatchEvent(new CustomEvent('autoSwitchSuccess', {
                    detail: { 
                        device: device,
                        index: index,
                        timestamp: Date.now()
                    }
                }));
                
            } catch (error) {
                console.error("Unexpected error in mapboxAutoSwitch:", error);
                document.dispatchEvent(new CustomEvent('autoSwitchError', {
                    detail: { 
                        error: error.message,
                        type: 'unexpected_error',
                        device: device,
                        index: index,
                        stack: error.stack
                    }
                }));
            }
        };
        
        console.log("Window mapboxAutoSwitch status:", typeof window.mapboxAutoSwitch === 'function' ? "Available" : "Not available");
        console.log("Available devices:", devices.length);
        console.log("=== MAPBOX AUTO SWITCH FUNCTION REGISTERED ===");
        
        return () => {
            // Cleanup function - will be handled by unmount effect
        };
    }, [devices]);
    
    // Pastikan membersihkan window.mapboxAutoSwitch saat komponen unmount
    useEffect(() => {
        return () => {
            if (window.mapboxAutoSwitch) {
                delete window.mapboxAutoSwitch;
                console.log("mapboxAutoSwitch function removed from window object during unmount");
            }
        };
    }, []);
    // Initialize map
    useEffect(() => {
        // Token sudah diset di bagian atas file dari environment variable
        if (!mapboxgl.accessToken) {
            console.error('Mapbox access token not found! Please check VITE_MAPBOX_ACCESS_TOKEN in .env file');
            return;
        }
        if (map.current) return;

        map.current = new mapboxgl.Map({
            container: mapContainer.current,
            style: "mapbox://styles/mapbox/streets-v12",
            center: [112.5, -7.5],
            zoom: 8,
            pitch: 45,
            bearing: -17.6,
            antialias: true,
        });
        map.current.addControl(new mapboxgl.ScaleControl(), "bottom-left");
        
        // Tambahkan event listener untuk interaksi peta
        const emitUserInteraction = (source) => {
            console.log(`Map interaction detected: ${source}`);
            // Trigger custom event to stop auto switch
            const event = new CustomEvent("userInteraction", {
                detail: { source: source },
            });
            document.dispatchEvent(event);
        };
        
        // Tambahkan event listener untuk drag
        map.current.on("dragstart", () => {
            emitUserInteraction("mapDrag");
            try {
                if (tooltipRef.current?.visible) {
                    const fallbackZoom = 12;
                    const targetZoom = Math.max(previousZoomRef.current ?? fallbackZoom, fallbackZoom);
                    // Zoom out cepat saat user mulai geser agar area lebih luas
                    map.current.easeTo({ zoom: targetZoom, duration: 0, essential: true });
                }
            } catch (e) {
                // ignore
            }
        });
        // Tambahkan event listener untuk zoom
        map.current.on("zoomstart", () => emitUserInteraction("mapZoom"));
        // Tambahkan event listener untuk click pada peta (bukan marker)
        map.current.on("click", () => emitUserInteraction("mapClick"));
        
        return () => {
            if (map.current) {
                map.current.remove();
                map.current = null;
            }
        };
    }, []);
    // Listen for auto switch events
    useEffect(() => {
        const handleAutoSwitchActivated = (event) => {
            console.log("MapboxMap: Auto switch activated event received", event.detail);
            
            // Verifikasi apakah window.mapboxAutoSwitch berfungsi
            if (typeof window.mapboxAutoSwitch !== 'function') {
                console.error("WARNING: mapboxAutoSwitch function not available when auto switch activated");
                
                // Re-register fungsi jika hilang
                if (map.current) {
                    console.log("Re-registering mapboxAutoSwitch function");
                    window.mapboxAutoSwitch = (device, index) => {
                        console.log("Re-registered mapboxAutoSwitch called with:", device?.name);
                        handleAutoSwitch(device, index);
                    };
                }
            }
            
            // Kirim event konfirmasi kembali ke AutoSwitchToggle
            document.dispatchEvent(
                new CustomEvent("mapboxReadyForAutoSwitch", {
                    detail: { 
                        ready: true, 
                        devicesCount: devices.length,
                        hasMapInstance: !!map.current
                    }
                })
            );
        };
        
        const handleAutoSwitchDeactivated = (event) => {
            console.log("MapboxMap: Auto switch deactivated event received", event.detail);
        };
        
        document.addEventListener("autoSwitchActivated", handleAutoSwitchActivated);
        document.addEventListener("autoSwitchDeactivated", handleAutoSwitchDeactivated);
        
        return () => {
            document.removeEventListener("autoSwitchActivated", handleAutoSwitchActivated);
            document.removeEventListener("autoSwitchDeactivated", handleAutoSwitchDeactivated);
        };
    }, [devices]);
    
    // Update markers when devicesData changes
    useEffect(() => {
        if (!map.current || !devicesData || !devices.length) return;
        // Hapus marker yang ada
        markersRef.current.forEach((marker) => {
            if (marker && marker.remove) {
                marker.remove();
            }
        });
        markersRef.current = [];
        devicesData.forEach((station) => {
            const coordinates = getStationCoordinates(station.name);
            if (coordinates) {
                try {
                    const markerEl = document.createElement("div");
                    markerEl.className = "custom-marker";
                    // UKURAN MARKER DIPERBESAR
                    markerEl.style.width = "27px"; // Diperbesar dari 20px menjadi 30px
                    markerEl.style.height = "27px"; // Diperbesar dari 20px menjadi 30px
                    markerEl.style.borderRadius = "50%";
                    markerEl.style.backgroundColor = getStatusColor(station.status);
                    markerEl.style.border = "3px solid white"; // Diperbesar dari 2px menjadi 3px
                    markerEl.style.boxShadow = "0 4px 8px rgba(0,0,0,0.4)";
                    markerEl.style.cursor = "pointer";
                    markerEl.style.zIndex = "10";
                    markerEl.style.display = "flex";
                    markerEl.style.alignItems = "center";
                    markerEl.style.justifyContent = "center";
                    // Tambahkan icon sesuai status
                    const iconSvg = getStatusIcon(station.status);
                    markerEl.innerHTML = iconSvg;
                    // Tambahkan pulse animation untuk status alert
                    if (station.status === "alert") {
                        const pulseEl = document.createElement("div");
                        pulseEl.style.position = "absolute";
                        pulseEl.style.width = "100%";
                        pulseEl.style.height = "100%";
                        pulseEl.style.borderRadius = "50%";
                        pulseEl.style.backgroundColor = getStatusColor(station.status);
                        pulseEl.style.opacity = "0.7";
                        pulseEl.style.animation = "alert-pulse 2s infinite";
                        pulseEl.style.zIndex = "-1";
                        markerEl.appendChild(pulseEl);
                    }
                    const marker = new mapboxgl.Marker(markerEl).setLngLat(coordinates).addTo(map.current);
                    markersRef.current.push(marker);
                    // Event untuk klik marker
                    markerEl.addEventListener("click", (e) => {
                        e.stopPropagation(); // Mencegah event bubbling
                        console.log('=== MARKER CLICK DEBUG ===');
                        console.log('Clicked station:', station);
                        console.log('Station ID:', station.id);
                        console.log('Station name:', station.name);
                        
                        // Simpan zoom sebelum fokus
                        try { previousZoomRef.current = map.current.getZoom(); } catch (e) { previousZoomRef.current = 12; }
                        
                        // Fokus akurat dan smooth ke marker yang diklik
                        const TARGET_ZOOM = 15;
                        const PADDING = { left: 360, right: 40, top: 80, bottom: 120 };
                        const smoothEase = (t) => 1 - Math.pow(1 - t, 3);
                        try { map.current.stop(); } catch (_) {}
                        map.current.easeTo({
                            center: coordinates,
                            zoom: TARGET_ZOOM,
                            pitch: 45,
                            bearing: -17.6,
                            duration: 1100,
                            curve: 1.25,
                            easing: smoothEase,
                            essential: true,
                            padding: PADDING,
                        });
                        
                        // Tampilkan tooltip
                        setTooltip({
                            visible: true,
                            station: station,
                            coordinates: coordinates,
                        });
                        console.log('Tooltip set to visible');
                        
                        // Trigger custom event to stop auto switch
                        const event = new CustomEvent("userInteraction", {
                            detail: { source: "mapMarker", stationId: station.id },
                        });
                        document.dispatchEvent(event);
                        console.log('=== END MARKER CLICK DEBUG ===');
                    });
                } catch (error) {
                    console.error("Error creating marker for station:", station.name, error);
                }
            }
        });
    }, [devicesData, devices]);
    // Event listener untuk klik di luar tooltip
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (
                tooltip.visible &&
                !event.target.closest(".custom-marker") &&
                !event.target.closest(".mapboxgl-popup-content") &&
                !event.target.closest(".map-tooltip")
            ) {
                setTooltip((prev) => ({ ...prev, visible: false }));
            }
        };
        document.addEventListener("click", handleClickOutside);
        return () => {
            document.removeEventListener("click", handleClickOutside);
        };
    }, [tooltip.visible]);
    return (
        <div className="w-full h-screen overflow-hidden relative z-0">
            <div ref={mapContainer} className="w-full h-full relative z-0" />
            <Suspense fallback={null}>
                <MapTooltip
                    map={map.current}
                    station={tooltip.station}
                    isVisible={tooltip.visible}
                    coordinates={tooltip.coordinates}
                    onShowDetail={handleShowDetail}
                    onClose={handleCloseTooltip}
                />
            </Suspense>
        </div>
    );
};
export default MapboxMap;
