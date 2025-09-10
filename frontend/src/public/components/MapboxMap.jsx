import React, { useEffect, useRef, useState } from 'react';
import mapboxgl from 'mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';
const MapboxMap = ({ tickerData, onStationSelect }) => {
  const mapContainer = useRef(null);
  const map = useRef(null);
  const [selectedStation, setSelectedStation] = useState(null);
  const [isSidecardOpen, setIsSidecardOpen] = useState(false);
  const [waterAnimationActive, setWaterAnimationActive] = useState(false);
  const [selectedStationCoords, setSelectedStationCoords] = useState(null);
  
  // Initialize map only once
  useEffect(() => {
    // Set your Mapbox access token here
    mapboxgl.accessToken = 'pk.eyJ1IjoiZGl0b2ZhdGFoaWxsYWgxIiwiYSI6ImNtZjNveGloczAwNncya3E1YzdjcTRtM3MifQ.kIf5rscGYOzvvBcZJ41u8g';
    if (map.current) return; // initialize map only once
    map.current = new mapboxgl.Map({
      container: mapContainer.current,
      style: 'mapbox://styles/mapbox/streets-v12',
      center: [112.5, -7.5], // Jawa Timur coordinates
      zoom: 8,
      pitch: 45, // 3D view
      bearing: -17.6,
      antialias: true
    });
    // Add navigation controls
    map.current.addControl(new mapboxgl.NavigationControl(), 'top-right');
    map.current.addControl(new mapboxgl.FullscreenControl(), 'top-right');
    map.current.addControl(new mapboxgl.ScaleControl(), 'bottom-left');
    
    // Wait for map to load before adding 3D layer
    map.current.on('load', () => {
      // Add 3D Water Layer with status-based effects
      const waterLayer = {
        id: '3d-water',
        type: 'custom',
        renderingMode: '3d',
        onAdd: function(map, gl) {
          // Three.js setup
          this.camera = new THREE.Camera();
          this.scene = new THREE.Scene();
          this.waterMeshes = [];
          this.floodMeshes = [];
          
          // Create water surfaces with status-based effects
          const createWaterSurface = (point, status = 'safe', size = 1000) => {
            // Increased segments for smoother waves
            const waterGeometry = new THREE.PlaneGeometry(size, size, 64, 64);
            
            // Get intensity and color based on status
            const getWaterProperties = (status) => {
              switch (status) {
                case 'safe':
                  return { 
                    intensity: 0.2, 
                    color: new THREE.Color(0x0088cc), 
                    waveHeight: 2.0,
                    speed: 0.3
                  };
                case 'warning':
                  return { 
                    intensity: 1.0, 
                    color: new THREE.Color(0xffaa00), 
                    waveHeight: 6.0,
                    speed: 0.8
                  };
                case 'alert':
                  return { 
                    intensity: 2.5, 
                    color: new THREE.Color(0xff4444), 
                    waveHeight: 12.0,
                    speed: 1.5
                  };
                default:
                  return { 
                    intensity: 0.2, 
                    color: new THREE.Color(0x0088cc), 
                    waveHeight: 2.0,
                    speed: 0.3
                  };
              }
            };
            
            const props = getWaterProperties(status);
            
            const waterMaterial = new THREE.ShaderMaterial({
              uniforms: {
                time: { value: 0 },
                intensity: { value: props.intensity },
                waveHeight: { value: props.waveHeight },
                speed: { value: props.speed },
                color: { value: props.color },
                status: { value: status === 'safe' ? 0.0 : status === 'warning' ? 1.0 : 2.0 }
              },
              vertexShader: `
                uniform float time;
                uniform float intensity;
                uniform float waveHeight;
                uniform float speed;
                uniform float status;
                varying vec2 vUv;
                varying float vElevation;
                
                // Simplex noise function
                vec3 mod289(vec3 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
                vec4 mod289(vec4 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
                vec4 permute(vec4 x) { return mod289(((x*34.0)+1.0)*x); }
                vec4 taylorInvSqrt(vec4 x) { return 1.79284291400159 - 0.85373472095314 * x; }
                float snoise(vec3 v) { 
                  const vec2 C = vec2(1.0/6.0, 1.0/3.0);
                  const vec4 D = vec4(0.0, 0.5, 1.0, 2.0);
                  vec3 i = floor(v + dot(v, C.yyy));
                  vec3 x0 = v - i + dot(i, C.xxx);
                  vec3 g = step(x0.yzx, x0.xyz);
                  vec3 l = 1.0 - g;
                  vec3 i1 = min(g.xyz, l.zxy);
                  vec3 i2 = max(g.xyz, l.zxy);
                  vec3 x1 = x0 - i1 + C.xxx;
                  vec3 x2 = x0 - i2 + C.yyy;
                  vec3 x3 = x0 - D.yyy;
                  i = mod289(i);
                  vec4 p = permute(permute(permute(i.z + vec4(0.0, i1.z, i2.z, 1.0)) + i.y + vec4(0.0, i1.y, i2.y, 1.0)) + i.x + vec4(0.0, i1.x, i2.x, 1.0));
                  float n_ = 0.142857142857;
                  vec3 ns = n_ * D.wyz - D.xzx;
                  vec4 j = p - 49.0 * floor(p * ns.z * ns.z);
                  vec4 x_ = floor(j * ns.z);
                  vec4 y_ = floor(j - 7.0 * x_);
                  vec4 x = x_ *ns.x + ns.yyyy;
                  vec4 y = y_ *ns.x + ns.yyyy;
                  vec4 h = 1.0 - abs(x) - abs(y);
                  vec4 b3 = vec4(x.xy, y.xy);
                  vec4 p1 = h.xxxx > 0.0 ? vec4(x.xy, y.xy) : vec4(-1.0, -1.0, -1.0, -1.0);
                  vec4 p2 = h.yyyy > 0.0 ? vec4(x.zw, y.zw) : vec4(-1.0, -1.0, -1.0, -1.0);
                  vec4 p3 = h.zzzz > 0.0 ? vec4(x.xy, y.xy) : vec4(-1.0, -1.0, -1.0, -1.0);
                  vec4 p4 = h.wwww > 0.0 ? vec4(x.zw, y.zw) : vec4(-1.0, -1.0, -1.0, -1.0);
                  vec4 norm = taylorInvSqrt(vec4(dot(p1,p1), dot(p2, p2), dot(p3,p3), dot(p4,p4)));
                  p1 *= norm.x;
                  p2 *= norm.y;
                  p3 *= norm.z;
                  p4 *= norm.w;
                  vec4 values = vec4(dot(p1, x.xy), dot(p2, x.zw), dot(p3, x.xy), dot(p4, x.zw));
                  vec2 m = vec2(1.0, 1.0) - abs(vec2(values.x, values.y));
                  m = m * m;
                  m = m * m;
                  float res = -1.0 + 2.0 * mix(m.x * m.y, values.z, step(values.y, values.x));
                  return res * 0.5 + 0.5;
                }
                
                void main() {
                  vUv = uv;
                  vec3 pos = position;
                  
                  // Status-based wave behavior
                  if (status < 0.5) {
                    // Safe: Calm water with gentle waves
                    pos.z += sin(pos.x * 0.01 + time * 0.3) * waveHeight * 0.3;
                    pos.z += cos(pos.y * 0.01 + time * 0.2) * waveHeight * 0.2;
                  } else if (status < 1.5) {
                    // Warning: More aggressive waves
                    pos.z += sin(pos.x * 0.03 + time * speed) * waveHeight;
                    pos.z += cos(pos.y * 0.03 + time * speed * 0.8) * waveHeight * 0.7;
                    pos.z += sin((pos.x + pos.y) * 0.02 + time * speed * 1.2) * waveHeight * 0.5;
                  } else {
                    // Alert: Violent water with flooding effect
                    pos.z += sin(pos.x * 0.05 + time * speed) * waveHeight;
                    pos.z += cos(pos.y * 0.05 + time * speed * 0.8) * waveHeight * 0.9;
                    pos.z += sin((pos.x + pos.y) * 0.03 + time * speed * 1.5) * waveHeight * 0.8;
                    pos.z += sin(pos.x * 0.08 + time * speed * 2.0) * waveHeight * 0.6;
                  }
                  
                  // Add noise for natural movement
                  float noise = snoise(vec3(pos.x * 0.01, pos.y * 0.01, time * 0.2));
                  pos.z += noise * waveHeight * intensity;
                  
                  vElevation = pos.z;
                  gl_Position = projectionMatrix * modelViewMatrix * vec4(pos, 1.0);
                }
              `,
              fragmentShader: `
                uniform vec3 color;
                uniform float status;
                varying vec2 vUv;
                varying float vElevation;
                
                void main() {
                  vec3 waterColor = color;
                  float alpha = 0.7;
                  
                  // Status-based color effects
                  if (status < 0.5) {
                    // Safe: Normal water color
                    alpha = 0.6;
                  } else if (status < 1.5) {
                    // Warning: More visible water
                    alpha = 0.75;
                    waterColor = mix(waterColor, vec3(1.0, 0.8, 0.0), 0.3);
                  } else {
                    // Alert: High visibility flood water
                    alpha = 0.85;
                    waterColor = mix(waterColor, vec3(1.0, 0.2, 0.2), 0.4);
                  }
                  
                  // Add elevation-based color variation
                  float colorVariation = vElevation * 0.05;
                  waterColor += vec3(colorVariation, colorVariation * 0.8, colorVariation * 0.6);
                  
                  // Enhanced foam effect for alert status
                  if (status > 1.5) {
                    float foam = smoothstep(8.0, 12.0, vElevation);
                    waterColor = mix(waterColor, vec3(1.0), foam * 0.8);
                  } else {
                    float foam = smoothstep(3.0, 5.0, vElevation);
                    waterColor = mix(waterColor, vec3(1.0), foam * 0.4);
                  }
                  
                  gl_FragColor = vec4(waterColor, alpha);
                }
              `,
              transparent: true,
              side: THREE.DoubleSide
            });
            
            const waterMesh = new THREE.Mesh(waterGeometry, waterMaterial);
            const coords = mapboxgl.MercatorCoordinate.fromLngLat(point);
            waterMesh.position.set(
              coords.x * map.transform.worldSize,
              -coords.y * map.transform.worldSize,
              0
            );
            waterMesh.rotation.x = -Math.PI / 2;
            waterMesh.userData = { status, point };
            
            return waterMesh;
          };
          
          // Create flood expansion for alert status
          const createFloodExpansion = (point, size = 3000) => {
            const floodGeometry = new THREE.PlaneGeometry(size, size, 32, 32);
            const floodMaterial = new THREE.ShaderMaterial({
              uniforms: {
                time: { value: 0 },
                center: { value: new THREE.Vector2(0, 0) }
              },
              vertexShader: `
                uniform float time;
                varying vec2 vUv;
                varying float vDistance;
                
                void main() {
                  vUv = uv;
                  vec3 pos = position;
                  
                  // Calculate distance from center for flood expansion
                  vDistance = length(pos.xy);
                  
                  // Create expanding flood effect
                  float expansion = sin(time * 2.0 - vDistance * 0.01) * 2.0;
                  pos.z = max(0.0, expansion);
                  
                  gl_Position = projectionMatrix * modelViewMatrix * vec4(pos, 1.0);
                }
              `,
              fragmentShader: `
                varying vec2 vUv;
                varying float vDistance;
                
                void main() {
                  // Flood water spreading from center
                  float alpha = smoothstep(1500.0, 0.0, vDistance) * 0.4;
                  vec3 floodColor = vec3(0.8, 0.3, 0.3);
                  
                  gl_FragColor = vec4(floodColor, alpha);
                }
              `,
              transparent: true,
              side: THREE.DoubleSide
            });
            
            const floodMesh = new THREE.Mesh(floodGeometry, floodMaterial);
            const coords = mapboxgl.MercatorCoordinate.fromLngLat(point);
            floodMesh.position.set(
              coords.x * map.transform.worldSize,
              -coords.y * map.transform.worldSize,
              0
            );
            floodMesh.rotation.x = -Math.PI / 2;
            floodMesh.visible = false; // Hidden by default
            
            return floodMesh;
          };
          
          // Add water surfaces for stations based on their status
          const updateWaterSurfaces = () => {
            // Clear existing water meshes
            this.waterMeshes.forEach(mesh => this.scene.remove(mesh));
            this.floodMeshes.forEach(mesh => this.scene.remove(mesh));
            this.waterMeshes = [];
            this.floodMeshes = [];
            
            // Add new water surfaces based on current ticker data
            if (tickerData) {
              tickerData.forEach((station) => {
                const coordinates = getStationCoordinates(station.name);
                if (coordinates) {
                  // Create water surface with status-based effects
                  const size = station.status === 'alert' ? 2500 : station.status === 'warning' ? 2000 : 1500;
                  const waterMesh = createWaterSurface(coordinates, station.status, size);
                  this.waterMeshes.push(waterMesh);
                  this.scene.add(waterMesh);
                  
                  // Add flood expansion for alert status
                  if (station.status === 'alert') {
                    const floodMesh = createFloodExpansion(coordinates, 4000);
                    floodMesh.visible = true;
                    this.floodMeshes.push(floodMesh);
                    this.scene.add(floodMesh);
                  }
                }
              });
            }
          };
          
          // Initial water surface creation
          updateWaterSurfaces();
          
          // Store update function for later use
          this.updateWaterSurfaces = updateWaterSurfaces;
          
          // Lighting - simplified for better performance
          const ambientLight = new THREE.AmbientLight(0xffffff, 0.8);
          this.scene.add(ambientLight);
          
          const directionalLight = new THREE.DirectionalLight(0xffffff, 0.6);
          directionalLight.position.set(50, 50, 25);
          this.scene.add(directionalLight);
          
          // WebGL renderer
          this.renderer = new THREE.WebGLRenderer({
            canvas: map.getCanvas(),
            context: gl,
            antialias: false // Disabled for performance
          });
          this.renderer.autoClear = false;
          
          // Animation
          this.animate = () => {
            requestAnimationFrame(this.animate);
            
            const time = Date.now() * 0.001;
            
            // Update water animation
            this.waterMeshes.forEach((mesh) => {
              if (mesh.material.uniforms) {
                mesh.material.uniforms.time.value = time;
              }
            });
            
            // Update flood animation
            this.floodMeshes.forEach((mesh) => {
              if (mesh.material.uniforms) {
                mesh.material.uniforms.time.value = time;
              }
            });
            
            this.renderer.render(this.scene, this.camera);
            map.triggerRepaint();
          };
          
          this.animate();
        },
        render: function(gl, matrix) {
          const rotationX = new THREE.Matrix4().makeRotationAxis(
            new THREE.Vector3(1, 0, 0),
            map.transform.pitch
          );
          const rotationZ = new THREE.Matrix4().makeRotationAxis(
            new THREE.Vector3(0, 0, 1),
            map.transform.angle
          );
          const m = new THREE.Matrix4().fromArray(matrix);
          const l = new THREE.Matrix4()
            .makeTranslation(0, 0, 0)
            .scale(new THREE.Vector3(1, -1, 1))
            .multiply(rotationX)
            .multiply(rotationZ);
          this.camera.projectionMatrix = m.multiply(l);
          this.renderer.resetState();
          this.renderer.render(this.scene, this.camera);
          map.triggerRepaint();
        }
      };
      
      map.current.addLayer(waterLayer);
      
      // Store reference to water layer for updates
      map.current.waterLayer = waterLayer;
    });
    
    // Cleanup function
    return () => {
      if (map.current) {
        map.current.remove();
        map.current = null;
      }
    };
  }, []);
  
  // Update water surfaces when tickerData changes
  useEffect(() => {
    if (map.current && map.current.waterLayer && map.current.waterLayer.updateWaterSurfaces) {
      map.current.waterLayer.updateWaterSurfaces();
    }
  }, [tickerData]);
  
  // Update markers when tickerData changes
  useEffect(() => {
    if (!map.current || !tickerData) return;
    // Clear existing markers
    const existingMarkers = document.querySelectorAll('.custom-marker');
    existingMarkers.forEach(marker => marker.remove());
    
    // Add CSS animation for pulse effect only once
    if (!document.querySelector('#alert-pulse-style')) {
      const style = document.createElement('style');
      style.id = 'alert-pulse-style';
      style.innerHTML = `
        @keyframes alert-pulse {
          0% {
            transform: scale(1);
            opacity: 0.7;
          }
          70% {
            transform: scale(3.0);
            opacity: 0;
          }
          100% {
            transform: scale(3.0);
            opacity: 0;
          }
        }
      `;
      document.head.appendChild(style);
    }
    
    // Add new markers
    tickerData.forEach((station) => {
      const coordinates = getStationCoordinates(station.name);
      
      if (coordinates) {
        // Create custom marker element
        const markerEl = document.createElement('div');
        markerEl.className = 'custom-marker';
        markerEl.style.width = '30px';
        markerEl.style.height = '30px';
        markerEl.style.borderRadius = '50%';
        markerEl.style.backgroundColor = getStatusColor(station.status);
        markerEl.style.border = '3px solid white';
        markerEl.style.boxShadow = '0 4px 8px rgba(0,0,0,0.4)';
        markerEl.style.cursor = 'pointer';
        
        // Add pulsing effect for alert markers
        if (station.status === 'alert') {
          markerEl.innerHTML = `
            <div style="
              position: absolute;
              width: 100%;
              height: 100%;
              border-radius: 50%;
              background-color: ${getStatusColor(station.status)};
              opacity: 0.7;
              animation: alert-pulse 2s infinite;
            "></div>
          `;
        }
        
        // Add marker to map
        const marker = new mapboxgl.Marker(markerEl)
          .setLngLat(coordinates)
          .addTo(map.current);
          
        // Add click event to marker
        markerEl.addEventListener('click', () => {
          setWaterAnimationActive(true);
          setSelectedStationCoords(coordinates);
          
          // Fly to the station
          map.current.flyTo({
            center: coordinates,
            zoom: 12,
            pitch: 45,
            bearing: -17.6,
            speed: 1.2,
            curve: 1.4,
            easing: (t) => t,
            essential: true
          });
          
          // Open sidecard after a short delay
          setTimeout(() => {
            if (onStationSelect) {
              onStationSelect(station);
            } else {
              setSelectedStation(station);
              setIsSidecardOpen(true);
            }
          }, 800);
        });
      }
    });
  }, [tickerData]);
  
  // Function to get station coordinates
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
      case 'safe': return '#10B981';
      case 'warning': return '#F59E0B';
      case 'alert': return '#EF4444';
      default: return '#6B7280';
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
    <div className="w-full h-screen overflow-hidden relative z-0">
      <div ref={mapContainer} className="w-full h-full relative z-0" />
      
      {/* Sidecard - Enhanced with water status info */}
      {isSidecardOpen && selectedStation && (
        <div className="absolute top-0 left-0 w-80 h-full bg-white shadow-2xl transform transition-transform duration-300 ease-in-out z-10">
          {/* Header */}
          <div className="bg-white p-4 border-b border-gray-200">
            <div className="flex items-center justify-between">
              <button
                onClick={() => {
                  setIsSidecardOpen(false);
                  setWaterAnimationActive(false);
                }}
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
                <div className={`w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2 ${
                  selectedStation.status === 'alert' ? 'bg-red-500 animate-pulse' : 
                  selectedStation.status === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
                }`}>
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
                  <span className={`text-2xl font-bold ${selectedStation.status === 'alert' ? 'text-red-600' : selectedStation.status === 'warning' ? 'text-yellow-600' : 'text-blue-600'}`}>
                    {selectedStation.value} {selectedStation.unit}
                  </span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-gray-600">Status:</span>
                  <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusBgColor(selectedStation.status)} ${getStatusTextColor(selectedStation.status)}`}>
                    {getStatusText(selectedStation.status)}
                  </span>
                </div>
              </div>
            </div>
            
            {/* Water Animation Status */}
            <div className={`p-4 rounded-lg ${
              selectedStation.status === 'alert' ? 'bg-red-50' : 
              selectedStation.status === 'warning' ? 'bg-yellow-50' : 'bg-blue-50'
            }`}>
              <h3 className="font-semibold text-gray-800 mb-3">Status Visualisasi Air 3D</h3>
              <div className="space-y-2">
                <div className="flex items-center">
                  <div className={`w-3 h-3 rounded-full mr-2 ${
                    selectedStation.status === 'alert' ? 'bg-red-500 animate-pulse' : 
                    selectedStation.status === 'warning' ? 'bg-yellow-500' : 'bg-green-500'
                  }`}></div>
                  <span className="text-sm font-medium">
                    {selectedStation.status === 'alert' ? 'Air Banjir - Efek Melebar ke Daratan' : 
                     selectedStation.status === 'warning' ? 'Air Bergolak - Gelombang Tinggi' : 
                     'Air Tenang - Gelombang Halus'}
                  </span>
                </div>
                
                {/* Status-specific details */}
                {selectedStation.status === 'safe' && (
                  <div className="mt-2 text-xs text-gray-600 space-y-1">
                    <p>• Gelombang rendah: 2m</p>
                    <p>• Gerakan lambat dan tenang</p>
                    <p>• Warna air biru normal</p>
                    <p>• Area air: 1500m x 1500m</p>
                  </div>
                )}
                
                {selectedStation.status === 'warning' && (
                  <div className="mt-2 text-xs text-gray-600 space-y-1">
                    <p>• Gelombang sedang: 6m</p>
                    <p>• Gerakan bergoyang aktif</p>
                    <p>• Warna air kuning-oranye</p>
                    <p>• Area air: 2000m x 2000m</p>
                    <p>• Efek busa meningkat</p>
                  </div>
                )}
                
                {selectedStation.status === 'alert' && (
                  <div className="mt-2 text-xs text-red-600 space-y-1">
                    <p>• Gelombang tinggi: 12m</p>
                    <p>• Gerakan violent dan cepat</p>
                    <p>• Warna air merah peringatan</p>
                    <p>• Area air: 2500m x 2500m</p>
                    <p>• Efek banjir meluas: 4000m</p>
                    <p>• Animasi ekspansi ke daratan</p>
                  </div>
                )}
              </div>
            </div>
            
            {/* Performance Info */}
            <div className="bg-green-50 p-4 rounded-lg">
              <h3 className="font-semibold text-gray-800 mb-3">Optimisasi Performa</h3>
              <div className="space-y-2 text-sm text-gray-600">
                <div className="flex items-center">
                  <svg className="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                  </svg>
                  <span>3D Building dihilangkan untuk performa</span>
                </div>
                <div className="flex items-center">
                  <svg className="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                  </svg>
                  <span>Fokus pada efek air 3D dinamis</span>
                </div>
                <div className="flex items-center">
                  <svg className="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                  </svg>
                  <span>Antialiasing dioptimalkan</span>
                </div>
                <div className="flex items-center">
                  <svg className="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                  </svg>
                  <span>Lighting disederhanakan</span>
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
              <h4 className="font-medium text-gray-800 mb-2">Fitur Visualisasi</h4>
              <div className="space-y-2 text-sm text-gray-600">
                <p>• Efek air real-time berdasarkan status</p>
                <p>• Animasi gelombang dinamis</p>
                <p>• Perubahan warna sesuai peringatan</p>
                <p>• Efek banjir untuk status alert</p>
                <p>• Optimasi performa tanpa 3D building</p>
                {selectedStation.status === 'alert' && (
                  <p className="text-red-600 font-medium">• Simulasi penyebaran banjir aktif</p>
                )}
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
export default MapboxMap;