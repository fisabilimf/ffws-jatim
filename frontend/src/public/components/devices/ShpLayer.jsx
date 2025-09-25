import React, { useEffect, useRef } from 'react';

const ShpLayer = ({ map, geojsonData, layerId, visible, onLayerLoad, onError }) => {
  const layerAdded = useRef(false);
  const createdLayers = useRef([]);

  useEffect(() => {
    if (!map || !geojsonData || layerAdded.current) return;

    const addShpLayer = () => {
      try {
        // Validasi GeoJSON
        if (!geojsonData || !geojsonData.features || geojsonData.features.length === 0) {
          throw new Error('Invalid GeoJSON data');
        }

        // Cek apakah source sudah ada
        if (map.getSource(layerId)) {
          console.warn(`Source ${layerId} already exists, removing it first`);
          if (map.getLayer(`${layerId}-fill`)) map.removeLayer(`${layerId}-fill`);
          if (map.getLayer(`${layerId}-line`)) map.removeLayer(`${layerId}-line`);
          if (map.getLayer(`${layerId}-circle`)) map.removeLayer(`${layerId}-circle`);
          map.removeSource(layerId);
        }

        // Tambahkan source
        map.addSource(layerId, {
          type: 'geojson',
          data: geojsonData
        });

        // Analisis geometri
        const geomTypes = new Set();
        geojsonData.features.forEach(feature => {
          if (feature.geometry) {
            geomTypes.add(feature.geometry.type);
          }
        });

        if (geomTypes.size === 0) {
          throw new Error('No valid geometry found in GeoJSON');
        }

        // Tambahkan layer sesuai tipe geometri
        if (geomTypes.has('Polygon') || geomTypes.has('MultiPolygon')) {
          const layerIdFill = `${layerId}-fill`;
          createdLayers.current.push(layerIdFill);
          map.addLayer({
            id: layerIdFill,
            type: 'fill',
            source: layerId,
            layout: { visibility: visible ? 'visible' : 'none' },
            paint: {
              'fill-color': '#3bb2d0',
              'fill-opacity': 0.5,
              'fill-outline-color': '#000',
              'fill-outline-width': 1
            }
          });
        }

        if (geomTypes.has('LineString') || geomTypes.has('MultiLineString')) {
          const layerIdLine = `${layerId}-line`;
          createdLayers.current.push(layerIdLine);
          map.addLayer({
            id: layerIdLine,
            type: 'line',
            source: layerId,
            layout: { visibility: visible ? 'visible' : 'none' },
            paint: {
              'line-color': '#ff0000',
              'line-width': 2
            }
          });
        }

        if (geomTypes.has('Point') || geomTypes.has('MultiPoint')) {
          const layerIdCircle = `${layerId}-circle`;
          createdLayers.current.push(layerIdCircle);
          map.addLayer({
            id: layerIdCircle,
            type: 'circle',
            source: layerId,
            layout: { visibility: visible ? 'visible' : 'none' },
            paint: {
              'circle-radius': 5,
              'circle-color': '#ff0000'
            }
          });
        }

        layerAdded.current = true;
        if (onLayerLoad) onLayerLoad(geojsonData);
        
      } catch (error) {
        console.error('Error adding SHP layer:', error);
        if (onError) onError(error);
      }
    };

    addShpLayer();

    return () => {
      if (layerAdded.current && map && map.getStyle()) {
        createdLayers.current.forEach(id => {
          if (map.getLayer(id)) map.removeLayer(id);
        });
        if (map.getSource(layerId)) map.removeSource(layerId);
        layerAdded.current = false;
        createdLayers.current = [];
      }
    };
  }, [map, geojsonData, layerId, visible, onLayerLoad, onError]);

  useEffect(() => {
    if (!map || !layerAdded.current) return;
    
    createdLayers.current.forEach(id => {
      if (map.getLayer(id)) {
        map.setLayoutProperty(id, 'visibility', visible ? 'visible' : 'none');
      }
    });
  }, [map, visible, layerId]);

  return null;
};

export default ShpLayer;