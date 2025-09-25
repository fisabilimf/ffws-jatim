// Konfigurasi threshold per stasiun untuk sistem monitoring banjir
// Dapat dikustomisasi sesuai karakteristik sungai dan lokasi geografis

export const STATION_THRESHOLDS = {
  // Stasiun Surabaya - Sungai Kalimas
  'Stasiun Surabaya': {
    safe: 2.0,      // ≤ 2.0m = Aman
    warning: 3.0,   // 2.0m - 3.0m = Waspada
    alert: 4.0,     // > 3.0m = Bahaya
    description: 'Sungai Kalimas - Area perkotaan dengan drainase terbatas'
  },
  
  // Stasiun Malang - Sungai Brantas
  'Stasiun Malang': {
    safe: 1.5,      // ≤ 1.5m = Aman
    warning: 2.5,   // 1.5m - 2.5m = Waspada
    alert: 3.5,     // > 2.5m = Bahaya
    description: 'Sungai Brantas - Area pegunungan dengan aliran deras'
  },
  
  // Stasiun Sidoarjo - Sungai Porong
  'Stasiun Sidoarjo': {
    safe: 2.5,      // ≤ 2.5m = Aman
    warning: 3.5,   // 2.5m - 3.5m = Waspada
    alert: 4.5,     // > 3.5m = Bahaya
    description: 'Sungai Porong - Area dataran rendah dengan sedimentasi tinggi'
  },
  
  // Stasiun Probolinggo - Sungai Probolinggo
  'Stasiun Probolinggo': {
    safe: 1.8,      // ≤ 1.8m = Aman
    warning: 2.8,   // 1.8m - 2.8m = Waspada
    alert: 3.8,     // > 2.8m = Bahaya
    description: 'Sungai Probolinggo - Area pesisir dengan pasang surut'
  },
  
  // Stasiun Pasuruan - Sungai Pasuruan
  'Stasiun Pasuruan': {
    safe: 2.2,      // ≤ 2.2m = Aman
    warning: 3.2,   // 2.2m - 3.2m = Waspada
    alert: 4.2,     // > 3.2m = Bahaya
    description: 'Sungai Pasuruan - Area industri dengan aliran stabil'
  },
  
  // Stasiun Mojokerto - Sungai Brantas
  'Stasiun Mojokerto': {
    safe: 2.3,      // ≤ 2.3m = Aman
    warning: 3.3,   // 2.3m - 3.3m = Waspada
    alert: 4.3,     // > 3.3m = Bahaya
    description: 'Sungai Brantas - Area pertanian dengan bendungan'
  },
  
  // Stasiun Lamongan - Sungai Lamongan
  'Stasiun Lamongan': {
    safe: 1.9,      // ≤ 1.9m = Aman
    warning: 2.9,   // 1.9m - 2.9m = Waspada
    alert: 3.9,     // > 2.9m = Bahaya
    description: 'Sungai Lamongan - Area pertanian dengan aliran lambat'
  },
  
  // Stasiun Gresik - Sungai Bengawan Solo
  'Stasiun Gresik': {
    safe: 2.1,      // ≤ 2.1m = Aman
    warning: 3.1,   // 2.1m - 3.1m = Waspada
    alert: 4.1,     // > 3.1m = Bahaya
    description: 'Sungai Bengawan Solo - Area pesisir dengan muara'
  },
  
  // Stasiun Tuban - Sungai Tuban
  'Stasiun Tuban': {
    safe: 1.7,      // ≤ 1.7m = Aman
    warning: 2.7,   // 1.7m - 2.7m = Waspada
    alert: 3.7,     // > 2.7m = Bahaya
    description: 'Sungai Tuban - Area pesisir dengan karakteristik pasang surut'
  },
  
  // Stasiun Bojonegoro - Sungai Bengawan Solo
  'Stasiun Bojonegoro': {
    safe: 2.4,      // ≤ 2.4m = Aman
    warning: 3.4,   // 2.4m - 3.4m = Waspada
    alert: 4.4,     // > 3.4m = Bahaya
    description: 'Sungai Bengawan Solo - Area hulu dengan aliran deras'
  }
};

// Fungsi untuk mendapatkan threshold berdasarkan nama stasiun
export const getStationThreshold = (stationName) => {
  return STATION_THRESHOLDS[stationName] || {
    safe: 2.5,      // Default threshold
    warning: 4.0,
    alert: 5.0,
    description: 'Threshold default - tidak dikonfigurasi'
  };
};

// Fungsi untuk menentukan status berdasarkan nilai dan threshold
export const determineStatus = (value, stationName) => {
  const threshold = getStationThreshold(stationName);
  
  if (value > threshold.alert) {
    return 'alert';
  } else if (value > threshold.warning) {
    return 'warning';
  } else {
    return 'safe';
  }
};

// Fungsi untuk mendapatkan informasi threshold lengkap
export const getThresholdInfo = (stationName) => {
  const threshold = getStationThreshold(stationName);
  return {
    ...threshold,
    stationName,
    ranges: {
      safe: `≤ ${threshold.safe}m`,
      warning: `${threshold.safe}m - ${threshold.warning}m`,
      alert: `> ${threshold.warning}m`
    }
  };
};

// Fungsi untuk update threshold (untuk admin/konfigurasi)
export const updateStationThreshold = (stationName, newThreshold) => {
  if (STATION_THRESHOLDS[stationName]) {
    STATION_THRESHOLDS[stationName] = {
      ...STATION_THRESHOLDS[stationName],
      ...newThreshold
    };
    return true;
  }
  return false;
};

// Export default untuk kemudahan import
export default {
  STATION_THRESHOLDS,
  getStationThreshold,
  determineStatus,
  getThresholdInfo,
  updateStationThreshold
};
