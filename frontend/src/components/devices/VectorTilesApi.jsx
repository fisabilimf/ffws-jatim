import React, { useEffect, useRef } from 'react';
import * as turf from '@turf/turf';

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

            // Cari layer air di peta - gunakan 'water-layer' yang sudah kita buat
            const waterLayer = map.getStyle().layers.find(layer => 
                layer.id === 'water-layer' || 
                (layer.type === 'fill' && 
                 layer['source-layer'] === 'water' && 
                 layer.filter && layer.filter.includes('river'))
            );
            
            if (!waterLayer) {
                console.warn('Water layer not found. Available layers:', 
                    map.getStyle().layers.map(l => l.id));
                return;
            }

            console.log('Found water layer:', waterLayer.id);

            // Dapatkan sumber data layer air
            const waterSource = waterLayer.source;
            const waterSourceLayer = waterLayer['source-layer'];

            // Hitung bounding box dari safe area
            const bounds = safeAreaData.geometry.coordinates[0];
            const bbox = [
                Math.min(...bounds.map(coord => coord[0])),
                Math.min(...bounds.map(coord => coord[1])),
                Math.max(...bounds.map(coord => coord[0])),
                Math.max(...bounds.map(coord => coord[1]))
            ];

            // Coba beberapa pendekatan untuk mendapatkan fitur air
            let waterFeatures = [];
            
            // Pendekatan 1: querySourceFeatures
            try {
                waterFeatures = map.querySourceFeatures(waterSource, {
                    sourceLayer: waterSourceLayer,
                    filter: ['any', 
                        ['==', '$type', 'Polygon'],
                        ['==', '$type', 'MultiPolygon']
                    ],
                    bounds: bbox
                });
                console.log(`Found ${waterFeatures.length} water features using querySourceFeatures`);
            } catch (e) {
                console.warn('Error with querySourceFeatures:', e);
            }
            
            // Pendekatan 2: queryRenderedFeatures jika pendekatan 1 gagal
            if (waterFeatures.length === 0) {
                try {
                    const sw = [bbox[0], bbox[1]];
                    const ne = [bbox[2], bbox[3]];
                    
                    waterFeatures = map.queryRenderedFeatures(
                        map.project(sw),
                        map.project(ne),
                        { 
                            layers: [waterLayer.id],
                            filter: ['any', 
                                ['==', '$type', 'Polygon'],
                                ['==', '$type', 'MultiPolygon']
                            ]
                        }
                    );
                    console.log(`Found ${waterFeatures.length} water features using queryRenderedFeatures`);
                } catch (e) {
                    console.warn('Error with queryRenderedFeatures:', e);
                }
            }

            // Siapkan array untuk fitur hasil potongan
            const featuresInSafeArea = [];

            // Buat polygon dari safe area untuk digunakan dalam operasi spasial
            const safeAreaPolygon = turf.polygon(safeAreaData.geometry.coordinates);

            // Loop melalui setiap fitur air dan potong dengan safe area
            for (const feature of waterFeatures) {
                try {
                    // Handle MultiPolygon
                    if (feature.geometry.type === 'MultiPolygon') {
                        for (const polygonCoords of feature.geometry.coordinates) {
                            const polygonFeature = turf.polygon(polygonCoords, feature.properties);
                            
                            // Cek apakah fitur berpotongan dengan safe area
                            if (turf.booleanIntersects(polygonFeature, safeAreaPolygon)) {
                                // Jika berpotongan, tambahkan fitur asli
                                featuresInSafeArea.push(polygonFeature);
                            }
                        }
                    } 
                    // Handle Polygon
                    else if (feature.geometry.type === 'Polygon') {
                        const polygonFeature = turf.polygon(feature.geometry.coordinates, feature.properties);
                        
                        // Cek apakah fitur berpotongan dengan safe area
                        if (turf.booleanIntersects(polygonFeature, safeAreaPolygon)) {
                            // Jika berpotongan, tambahkan fitur asli
                            featuresInSafeArea.push(polygonFeature);
                        }
                    }
                } catch (e) {
                    console.warn('Error processing feature:', e);
                }
            }

            console.log(`Found ${featuresInSafeArea.length} water features in safe area`);

            // Update sumber data dengan fitur yang sudah dipotong
            map.getSource('water-in-safe-area-source').setData({
                type: 'FeatureCollection',
                features: featuresInSafeArea
            });

            console.log('Water color in safe area changed');

        } catch (error) {
            console.error('Error changing water color in safe area:', error);
        }
    };

    // Fungsi helper untuk mendapatkan titik tengah polygon (tetap dipertahankan untuk keperluan lain)
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

    // Fungsi helper untuk mengecek apakah titik berada di dalam polygon (tetap dipertahankan untuk keperluan lain)
    const isPointInPolygon = (point, polygon) => {
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