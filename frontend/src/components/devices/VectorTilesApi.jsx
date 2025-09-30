import React, { useEffect, useRef } from 'react';

const VectorTilesAPI = ({ map, isVisible, coordinates, mapLoaded }) => {
    // Fungsi untuk mengubah warna air di area hijau
    const changeWaterColorInSafeArea = () => {
        if (!map || !mapLoaded || !coordinates) return;

        // Pastikan safe-areas source ada (river layer harus aktif terlebih dahulu)
        if (!map.getSource('safe-areas')) {
            console.warn('Safe areas not found. River layer must be activated first.');
            return;
        }

        try {
            // Hapus layer efek sebelumnya jika ada
            if (map.getLayer('water-in-safe-area')) {
                map.removeLayer('water-in-safe-area');
            }
            
            if (map.getSource('water-in-safe-area-source')) {
                map.removeSource('water-in-safe-area-source');
            }

            // Dapatkan data safe-areas yang sudah ada
            const safeAreasSource = map.getSource('safe-areas');
            let safeAreaData = null;

            if (safeAreasSource._data && safeAreasSource._data.features && safeAreasSource._data.features.length > 0) {
                safeAreaData = safeAreasSource._data.features[0];
            } else {
                console.warn('Safe area data not available');
                return;
            }

            // Buat sumber data baru untuk air di dalam safe area
            map.addSource('water-in-safe-area-source', {
                type: 'geojson',
                data: {
                    type: 'FeatureCollection',
                    features: [] // Akan diisi nanti
                }
            });

            // Tambahkan layer untuk air di dalam safe area dengan warna berbeda
            map.addLayer({
                id: 'water-in-safe-area',
                type: 'fill',
                source: 'water-in-safe-area-source',
                paint: {
                    'fill-color': '#0066CC', // Biru lebih gelap untuk air di dalam area hijau
                    'fill-opacity': 0.8
                }
            }, 'safe-areas-border'); // Tempatkan sebelum border

            // Ambil semua fitur air yang ada di dalam safe area menggunakan query
            const bounds = safeAreaData.geometry.coordinates[0];
            const sw = [bounds[0][0], bounds[2][1]]; // Southwest corner
            const ne = [bounds[2][0], bounds[0][1]]; // Northeast corner
            
            // Query semua fitur air di dalam bbox safe area
            const waterFeatures = map.queryRenderedFeatures(
                map.project(sw),
                map.project(ne),
                { 
                    layers: ['water'], // Cari di layer 'water' yang berisi semua jenis air
                    sourceLayer: 'water' // Source layer untuk fitur air
                }
            );

            // Filter fitur yang benar-benar di dalam safe area
            const featuresInSafeArea = waterFeatures.filter(feature => {
                // Cek apakah fitur berada di dalam safe area
                const coords = feature.geometry.coordinates;
                
                if (feature.geometry.type === 'Polygon') {
                    // Untuk polygon, cek apakah titik tengah berada di dalam safe area
                    const polygonCoords = coords[0];
                    const centerPoint = getPolygonCenter(polygonCoords);
                    return isPointInPolygon(centerPoint, safeAreaData.geometry.coordinates[0]);
                } else if (feature.geometry.type === 'LineString') {
                    // Untuk garis, cek apakah titik tengah berada di dalam safe area
                    const midIndex = Math.floor(coords.length / 2);
                    const midPoint = coords[midIndex];
                    return isPointInPolygon(midPoint, safeAreaData.geometry.coordinates[0]);
                } else if (feature.geometry.type === 'MultiLineString') {
                    // Untuk multi garis, cek apakah salah satu titik tengah berada di dalam safe area
                    return coords.some(line => {
                        const midIndex = Math.floor(line.length / 2);
                        const midPoint = line[midIndex];
                        return isPointInPolygon(midPoint, safeAreaData.geometry.coordinates[0]);
                    });
                } else if (feature.geometry.type === 'MultiPolygon') {
                    // Untuk multi polygon, cek apakah salah satu titik tengah berada di dalam safe area
                    return coords.some(polygon => {
                        const polygonCoords = polygon[0];
                        const centerPoint = getPolygonCenter(polygonCoords);
                        return isPointInPolygon(centerPoint, safeAreaData.geometry.coordinates[0]);
                    });
                }
                
                return false;
            });

            // Update sumber data dengan fitur yang di dalam safe area
            map.getSource('water-in-safe-area-source').setData({
                type: 'FeatureCollection',
                features: featuresInSafeArea
            });

            console.log('Water color in safe area changed');

        } catch (error) {
            console.error('Error changing water color in safe area:', error);
        }
    };

    // Fungsi helper untuk mendapatkan titik tengah polygon
    const getPolygonCenter = (coords) => {
        let x = 0;
        let y = 0;
        let n = coords.length;
        
        for (let i = 0; i < n; i++) {
            x += coords[i][0];
            y += coords[i][1];
        }
        
        return [x / n, y / n];
    };

    // Fungsi helper untuk mengecek apakah titik berada di dalam polygon
    const isPointInPolygon = (point, polygon) => {
        // Ray casting algorithm
        let inside = false;
        for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
            const xi = polygon[i][0], yi = polygon[i][1];
            const xj = polygon[j][0], yj = polygon[j][1];
            
            const intersect = ((yi > point[1]) !== (yj > point[1]))
                && (point[0] < (xj - xi) * (point[1] - yi) / (yj - yi) + xi);
            if (intersect) inside = !inside;
        }
        return inside;
    };

    // Fungsi untuk mengembalikan warna air ke semula
    const revertWaterColor = () => {
        if (!map) return;

        try {
            if (map.getLayer('water-in-safe-area')) {
                map.removeLayer('water-in-safe-area');
            }
            
            if (map.getSource('water-in-safe-area-source')) {
                map.removeSource('water-in-safe-area-source');
            }
            
            console.log('Water color reverted to original');
        } catch (error) {
            console.error('Error reverting water color:', error);
        }
    };

    useEffect(() => {
        if (!map || !mapLoaded) return;

        if (isVisible && coordinates) {
            // Tambahkan delay kecil untuk memastikan river layer sudah siap
            const timer = setTimeout(() => {
                changeWaterColorInSafeArea();
            }, 500);
            
            return () => {
                clearTimeout(timer);
                revertWaterColor();
            };
        } else {
            revertWaterColor();
        }

        return () => {
            revertWaterColor();
        };
    }, [map, isVisible, coordinates, mapLoaded]);

    // Cleanup saat komponen unmount
    useEffect(() => {
        return () => {
            revertWaterColor();
        };
    }, []);

    // Komponen ini tidak merender apapun secara visual
    return null;
};

export default VectorTilesAPI;