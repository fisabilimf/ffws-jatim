# Forecasting Service (Flask + Keras) — per River Basin

Service untuk forecasting deret waktu sensor (rainfall/water_level) menggunakan model Keras (GRU/LSTM/TCN) dan scaler dari database.

## Fokus
Sekali request untuk `river_basin_id`, sistem akan memproses semua sensor di basin tersebut secara berurutan.

---

## Setup

1. **Python Version**: Gunakan Python 3.10 atau 3.11.
2. **Install Dependencies**: Jalankan perintah berikut:
   ```bash
   pip install -r requirements.txt
   ```
3. **Konfigurasi Environment**:
   - Salin file `.env.example` menjadi `.env`.
   - Sesuaikan koneksi MySQL dan parameter `MODELS_BASE_DIR`.
4. **Database Schema**:
   - Pastikan skema tabel sesuai dengan kebutuhan: `users`, `mas_models`, `mas_devices`, `mas_sensors`, `data_actual`, `data_prediction`, `mas_scalers`, `mas_river_basins`.
5. **Menjalankan Aplikasi**:
   - **Development**:
     ```bash
     FLASK_APP=wsgi.py flask run --port 8000
     ```
   - **Production**:
     ```bash
     gunicorn -w 2 -b 0.0.0.0:8000 wsgi:app
     ```

---

## Endpoints

### Healthcheck
- **GET** `/health` — Mengecek status aplikasi.

### Model dan Sensor
- **GET** `/api/models` — Mendapatkan daftar model.
- **GET** `/api/sensors` — Mendapatkan daftar sensor.
- **GET** `/api/river-basins` — Mendapatkan daftar river basin.

### Forecasting

#### Forecast Satu Sensor
- **POST** `/api/forecast/run`
  - Body:
    ```json
    {
      "sensor_id": 101,
      "model_id": 5
    }
    ```
  - `model_id` opsional (default: `mas_sensors.mas_model_id`).

#### Forecast Semua Sensor di Basin
- **POST** `/api/forecast/run-basin`
  - Body:
    ```json
    {
      "river_basin_id": 1,
      "only_active": true
    }
    ```

---

## Alur Kerja

1. Ambil daftar sensor melalui `mas_devices.mas_river_basin_id`.
2. Untuk setiap sensor:
   - Ambil data historis dari `data_actual`.
   - Lakukan preprocessing (sorting, resampling/imputasi/outlier clipping jika diperlukan).
   - Transformasi data menggunakan `x_scaler.transform()`.
   - Prediksi menggunakan `model.predict()`.
   - Inverse transform hasil prediksi menggunakan `y_scaler.inverse_transform()`.
   - Hitung status threshold dan simpan hasil ke `data_prediction`.

---

## Catatan

- `file_path` pada database dapat berupa path relatif; otomatis akan diprefix dengan `MODELS_BASE_DIR`.
- Jika data historis kurang dari `n_steps_in` untuk sensor tertentu, sensor tersebut akan dilaporkan error tanpa menghentikan batch.
- Proses `predict_for_basin` berjalan secara sekuensial. Untuk paralelisme, gunakan worker queue seperti Celery atau RQ (di luar scope repo ini).