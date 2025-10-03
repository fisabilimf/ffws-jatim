# API Documentation - FFWS Jawa Timur

## Authentication dengan Laravel Sanctum

Sistem authentication menggunakan Laravel Sanctum untuk API token-based authentication.

## Base URL
```
http://localhost:8000/api
```

## Response Format

Semua response API mengikuti format standar:
```json
{
    "success": true|false,
    "message": "Pesan response",
    "data": {...},
    "errors": null|{...},
    "status_code": 200
}
```

## Endpoints

### 1. Authentication Endpoints

#### Register User
```http
POST /api/auth/register
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "user" // optional: "admin" or "user"
}
```

**Response:**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "user",
            "status": "active",
            "created_at": "2024-01-01T00:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    },
    "errors": null,
    "status_code": 201
}
```

#### Login User
```http
POST /api/auth/login
```

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "user",
            "status": "active"
        },
        "token": "2|def456...",
        "token_type": "Bearer"
    },
    "errors": null,
    "status_code": 200
}
```

#### Get Current User
```http
GET /api/auth/me
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "user",
            "status": "active"
        }
    },
    "errors": null,
    "status_code": 200
}
```

#### Refresh Token
```http
POST /api/auth/refresh
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Token refreshed successfully",
    "data": {
        "token": "3|ghi789...",
        "token_type": "Bearer"
    },
    "errors": null,
    "status_code": 200
}
```

#### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Logout successful",
    "data": null,
    "errors": null,
    "status_code": 200
}
```

### 2. User Management Endpoints

#### Get All Users
```http
GET /api/users?search=john&role=user&status=active&per_page=15
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com",
                "role": "user",
                "status": "active",
                "created_at": "2024-01-01T00:00:00.000000Z"
            }
        ],
        "first_page_url": "http://localhost:8000/api/users?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://localhost:8000/api/users?page=1",
        "links": [...],
        "next_page_url": null,
        "path": "http://localhost:8000/api/users",
        "per_page": 15,
        "prev_page_url": null,
        "to": 1,
        "total": 1
    },
    "errors": null,
    "status_code": 200
}
```

#### Get User by ID
```http
GET /api/users/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "user",
            "status": "active",
            "bio": "User bio",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    },
    "errors": null,
    "status_code": 200
}
```

#### Update User
```http
PUT /api/users/{id}
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "name": "John Updated",
    "email": "john.updated@example.com",
    "role": "admin",
    "status": "active",
    "bio": "Updated bio"
}
```

**Response:**
```json
{
    "success": true,
    "message": "User updated successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Updated",
            "email": "john.updated@example.com",
            "role": "admin",
            "status": "active",
            "bio": "Updated bio",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    },
    "errors": null,
    "status_code": 200
}
```

#### Delete User
```http
DELETE /api/users/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "User deleted successfully",
    "data": null,
    "errors": null,
    "status_code": 200
}
```

### 3. Device Management Endpoints

#### Get Device by ID
```http
GET /api/devices/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Device retrieved successfully",
    "data": {
        "name": "Device Sungai Brantas 1",
        "code": "BRT001",
        "latitude": "-7.250000",
        "longitude": "112.750000",
        "elevation_m": "100.50",
        "status": "active",
        "mas_river_basin_id": 1,
        "river_basin_name": "Sungai Brantas"
    },
    "errors": null,
    "status_code": 200
}
```

### 4. Sensor Management Endpoints

#### Get All Sensors
```http
GET /api/sensors
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Data sensor berhasil diambil",
    "data": [
        {
            "device_id": 1,
            "sensor_code": "SEN001",
            "parameter": "water_level",
            "unit": "cm",
            "description": "Water Level Sensor",
            "mas_model_id": 1,
            "threshold_safe": 50.0,
            "threshold_warning": 100.0,
            "threshold_danger": 150.0,
            "status": "active",
            "last_seen": "2024-01-01T12:00:00.000000Z",
            "device": {
                "id": 1,
                "name": "Device Sungai Brantas 1",
                "code": "BRT001"
            },
            "mas_model": {
                "id": 1,
                "name": "Model LSTM v1.0"
            }
        }
    ],
    "errors": null,
    "status_code": 200
}
```

#### Get Sensor by ID
```http
GET /api/sensors/{id}
Authorization: Bearer {token}
```

#### Get Sensors by Device ID
```http
GET /api/sensors/device/{deviceId}
Authorization: Bearer {token}
```

#### Get Sensors by Parameter
```http
GET /api/sensors/parameter/{parameter}
Authorization: Bearer {token}
```

**Parameters yang tersedia:**
- `water_level` - Sensor level air
- `rainfall` - Sensor curah hujan

#### Get Sensors by Status
```http
GET /api/sensors/status/{status}
Authorization: Bearer {token}
```

**Status yang tersedia:**
- `active` - Sensor aktif
- `inactive` - Sensor tidak aktif

### 5. River Basin Endpoints

#### Get River Basin by ID
```http
GET /api/river-basins/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "River Basin retrieved successfully",
    "data": {
        "id": 1,
        "name": "Sungai Brantas",
        "code": "BRT",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "errors": null,
    "status_code": 200
}
```

### 6. Data Endpoints

#### Get Data Actuals (Placeholder)
```http
GET /api/data/actuals
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Data actuals endpoint - implementasi selanjutnya",
    "data": null,
    "errors": null,
    "status_code": 200
}
```

### 7. Test Endpoint

#### API Health Check
```http
GET /api/test
```

**Response:**
```json
{
    "success": true,
    "message": "API berjalan dengan baik",
    "data": null,
    "errors": null,
    "status_code": 200
}
```

## Error Responses

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation error",
    "data": null,
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    },
    "status_code": 422
}
```

### Unauthorized (401)
```json
{
    "success": false,
    "message": "Invalid credentials",
    "data": null,
    "errors": null,
    "status_code": 401
}
```

### Forbidden (403)
```json
{
    "success": false,
    "message": "Account is not active",
    "data": null,
    "errors": null,
    "status_code": 403
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "User not found",
    "data": null,
    "errors": null,
    "status_code": 404
}
```

### Server Error (500)
```json
{
    "success": false,
    "message": "Terjadi kesalahan saat mengambil data device",
    "data": null,
    "errors": null,
    "status_code": 500
}
```

## Frontend Integration

### JavaScript Example (Fetch API)

```javascript
// Login
const login = async (email, password) => {
    const response = await fetch('http://localhost:8000/api/auth/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password })
    });
    
    const data = await response.json();
    
    if (data.success) {
        localStorage.setItem('token', data.data.token);
        return data.data.user;
    }
    
    throw new Error(data.message);
};

// Get authenticated user
const getCurrentUser = async () => {
    const token = localStorage.getItem('token');
    
    const response = await fetch('http://localhost:8000/api/auth/me', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
        }
    });
    
    const data = await response.json();
    
    if (data.success) {
        return data.data.user;
    }
    
    throw new Error(data.message);
};

// Get all sensors
const getSensors = async () => {
    const token = localStorage.getItem('token');
    
    const response = await fetch('http://localhost:8000/api/sensors', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
        }
    });
    
    const data = await response.json();
    
    if (data.success) {
        return data.data;
    }
    
    throw new Error(data.message);
};

// Get device by ID
const getDevice = async (deviceId) => {
    const token = localStorage.getItem('token');
    
    const response = await fetch(`http://localhost:8000/api/devices/${deviceId}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
        }
    });
    
    const data = await response.json();
    
    if (data.success) {
        return data.data;
    }
    
    throw new Error(data.message);
};

// Logout
const logout = async () => {
    const token = localStorage.getItem('token');
    
    const response = await fetch('http://localhost:8000/api/auth/logout', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
        }
    });
    
    const data = await response.json();
    
    if (data.success) {
        localStorage.removeItem('token');
    } else {
        throw new Error(data.message);
    }
};
```

### Axios Example

```javascript
import axios from 'axios';

// Setup axios instance
const api = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: {
        'Content-Type': 'application/json',
    }
});

// Add token to requests
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Add response interceptor to handle errors globally
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

// Login
const login = async (email, password) => {
    const response = await api.post('/auth/login', { email, password });
    if (response.data.success) {
        localStorage.setItem('token', response.data.data.token);
        return response.data.data.user;
    }
    throw new Error(response.data.message);
};

// Get current user
const getCurrentUser = async () => {
    const response = await api.get('/auth/me');
    if (response.data.success) {
        return response.data.data.user;
    }
    throw new Error(response.data.message);
};

// Get all sensors
const getSensors = async () => {
    const response = await api.get('/sensors');
    if (response.data.success) {
        return response.data.data;
    }
    throw new Error(response.data.message);
};

// Get sensors by device
const getSensorsByDevice = async (deviceId) => {
    const response = await api.get(`/sensors/device/${deviceId}`);
    if (response.data.success) {
        return response.data.data;
    }
    throw new Error(response.data.message);
};

// Get device details
const getDevice = async (deviceId) => {
    const response = await api.get(`/devices/${deviceId}`);
    if (response.data.success) {
        return response.data.data;
    }
    throw new Error(response.data.message);
};

// Logout
const logout = async () => {
    const response = await api.post('/auth/logout');
    if (response.data.success) {
        localStorage.removeItem('token');
    } else {
        throw new Error(response.data.message);
    }
};
```

## Data Models

### User Model
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user|admin",
    "status": "active|inactive",
    "bio": "User biography",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

### Device Model
```json
{
    "id": 1,
    "name": "Device Sungai Brantas 1",
    "code": "BRT001",
    "mas_river_basin_id": 1,
    "latitude": "-7.250000",
    "longitude": "112.750000",
    "elevation_m": 100.50,
    "status": "active|inactive"
}
```

### Sensor Model
```json
{
    "id": 1,
    "device_id": 1,
    "sensor_code": "SEN001",
    "parameter": "water_level|rainfall",
    "unit": "cm|mm",
    "description": "Sensor description",
    "mas_model_id": 1,
    "threshold_safe": 50.0,
    "threshold_warning": 100.0,
    "threshold_danger": 150.0,
    "status": "active|inactive",
    "last_seen": "2024-01-01T12:00:00.000000Z"
}
```

### River Basin Model
```json
{
    "id": 1,
    "name": "Sungai Brantas",
    "code": "BRT",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

## Query Parameters

### Pagination
- `per_page`: Jumlah item per halaman (default: 15)
- `page`: Nomor halaman (default: 1)

### Filtering
- `search`: Pencarian berdasarkan nama atau email (untuk users)
- `role`: Filter berdasarkan role (admin, user)
- `status`: Filter berdasarkan status (active, inactive)
- `parameter`: Filter sensor berdasarkan parameter (water_level, rainfall)
- `device_id`: Filter sensor berdasarkan device ID

## Security Notes

1. **Token Storage**: Simpan token di localStorage atau sessionStorage dengan aman
2. **HTTPS**: Gunakan HTTPS di production
3. **Token Expiration**: Token tidak memiliki expiration default, pertimbangkan untuk mengatur expiration
4. **CORS**: Konfigurasi CORS sudah diset untuk development, sesuaikan untuk production
5. **Rate Limiting**: Implementasi rate limiting untuk mencegah abuse
6. **Input Validation**: Semua input sudah divalidasi di backend

## Rate Limiting

API menggunakan Laravel rate limiting:
- Authentication endpoints: 60 requests per minute per IP
- Other endpoints: 1000 requests per minute per authenticated user

## Next Steps

1. ✅ **Completed**: Authentication system dengan Laravel Sanctum
2. ✅ **Completed**: User management endpoints
3. ✅ **Completed**: Device management endpoints
4. ✅ **Completed**: Sensor management endpoints
5. ✅ **Completed**: River basin endpoints
6. 🔄 **In Progress**: Implementasi endpoint untuk data actuals dan predictions
7. ⏳ **Planned**: Tambahkan role-based authorization middleware
8. ⏳ **Planned**: Implementasi rate limiting
9. ⏳ **Planned**: Tambahkan logging untuk audit trail
10. ⏳ **Planned**: Setup environment variables untuk production
11. ⏳ **Planned**: Implementasi real-time notifications dengan WebSocket
12. ⏳ **Planned**: Tambahkan API versioning
