// VectorTilesAPI.jsx (versi AMAN)

import React, { useEffect } from 'react';

const VectorTilesAPI = ({ map, mapLoaded, selectedLocation, isRiverLayerActive }) => {
    const SOURCE_ID = 'water-elevation-source';
    const LAYER_ID = 'water-elevation-layer';
    const RADIUS_METERS = 5000; // 5 km

    const cleanupLayer = () => {
        if (!map || typeof map.getLayer !== 'function') return;
        try {
            if (map.getLayer(LAYER_ID)) map.removeLayer(LAYER_ID);
            if (map.getSource(SOURCE_ID)) map.removeSource(SOURCE_ID);
        } catch (e) {
            console.warn('Cleanup layer error:', e.message);
        }
    };

    useEffect(() => {
        // ✅ Hentikan jika tidak aktif atau data tidak lengkap
        if (!isRiverLayerActive || !mapLoaded || !selectedLocation) {
            cleanupLayer();
            return;
        }

        // ✅ Pastikan turf tersedia
        if (typeof turf === 'undefined') {
            console.error('Turf.js belum dimuat! Tambahkan di public/index.html');
            return;
        }

        // ✅ Pastikan koordinat valid
        const { lat, lng } = selectedLocation;
        if (typeof lat !== 'number' || typeof lng !== 'number' || isNaN(lat) || isNaN(lng)) {
            cleanupLayer();
            return;
        }

        const processWaterFeatures = () => {
            try {
                if (!map?.isStyleLoaded?.()) return;
                cleanupLayer();

                const point = turf.point([lng, lat]);
                const bufferedArea = turf.buffer(point, RADIUS_METERS / 1000, { units: 'kilometers' });

                const waterLayer = map.getStyle().layers.find(
                    layer => layer['source-layer'] === 'water' && layer.type === 'fill'
                );

                if (!waterLayer) {
                    console.warn('Water layer not found in style');
                    return;
                }

                const waterFeatures = map.querySourceFeatures(waterLayer.source, {
                    sourceLayer: waterLayer['source-layer'],
                    filter: ['any', ['==', '$type', 'Polygon'], ['==', '$type', 'MultiPolygon']],
                });

                const featuresInBuffer = [];
                for (const feature of waterFeatures) {
                    try {
                        const feat = turf.feature(feature.geometry);
                        if (turf.booleanIntersects(feat, bufferedArea)) {
                            feature.properties.elevation = Math.random() * 15;
                            featuresInBuffer.push(feature);
                        }
                    } catch (e) {
                        // Skip invalid geometry
                    }
                }

                if (featuresInBuffer.length > 0) {
                    map.addSource(SOURCE_ID, {
                        type: 'geojson',
                        data: turf.featureCollection(featuresInBuffer),
                    });

                    map.addLayer({
                        id: LAYER_ID,
                        type: 'fill',
                        source: SOURCE_ID,
                        paint: {
                            'fill-color': [
                                'interpolate', ['linear'], ['get', 'elevation'],
                                0, '#87CEEB',
                                5, '#4682B4',
                                10, '#191970',
                                15, '#000033'
                            ],
                            'fill-opacity': 0.85
                        }
                    }, 'building');
                }
            } catch (error) {
                console.error('Error in VectorTilesAPI:', error);
                cleanupLayer();
            }
        };

        const timer = setTimeout(processWaterFeatures, 600);
        return () => {
            clearTimeout(timer);
            cleanupLayer();
        };
    }, [map, mapLoaded, isRiverLayerActive, selectedLocation]);

    return null;
};

export default VectorTilesAPI;