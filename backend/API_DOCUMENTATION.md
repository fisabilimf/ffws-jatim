# API Documentation - FFWS Jawa Timur

## Authentication dengan Laravel Sanctum

Sistem authentication menggunakan Laravel Sanctum untuk API token-based authentication.

## Base URL
```
http://localhost:8000/api
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
    }
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
    }
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
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "user",
            "status": "active"
        }
    }
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
    }
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
    "message": "Logout successful"
}
```

### 2. User Management Endpoints

#### Get All Users
```http
GET /api/users?search=john&role=user&status=active&per_page=15
Authorization: Bearer {token}
```

#### Get User by ID
```http
GET /api/users/{id}
Authorization: Bearer {token}
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

#### Delete User
```http
DELETE /api/users/{id}
Authorization: Bearer {token}
```

### 3. Device Management Endpoints

#### Get Devices for Map Display
```http
GET /api/devices/map
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Devices data retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Stasiun Monitoring Dhompo",
            "code": "DHM001",
            "latitude": -7.1234,
            "longitude": 112.5678,
            "elevation_m": 150.5,
            "status": "warning",
            "river_basin": {
                "id": 1,
                "name": "DAS Brantas",
                "code": "BRT001"
            },
            "sensors": [
                {
                    "sensor_id": 1,
                    "sensor_code": "DHM001_WL",
                    "parameter": "water_level",
                    "unit": "meter",
                    "value": 2.5,
                    "received_at": "2024-01-01T12:00:00.000000Z",
                    "status": "warning",
                    "thresholds": {
                        "safe": 2.0,
                        "warning": 2.5,
                        "danger": 3.0
                    }
                }
            ],
            "sensor_count": 2
        }
    ]
}
```

#### Get Device Detail by ID
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
        "id": 1,
        "name": "Stasiun Monitoring Dhompo",
        "code": "DHM001",
        "latitude": -7.1234,
        "longitude": 112.5678,
        "elevation_m": 150.5,
        "status": "active",
        "river_basin": {
            "id": 1,
            "name": "DAS Brantas",
            "code": "BRT001"
        },
        "sensors": [...]
    }
}
```

### 4. Test Endpoint

#### API Health Check
```http
GET /api/test
```

**Response:**
```json
{
    "success": true,
    "message": "API FFWS Jawa Timur is running!",
    "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

## Error Responses

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation error",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

### Unauthorized (401)
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### Forbidden (403)
```json
{
    "success": false,
    "message": "Account is not active"
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "User not found"
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
    return data.data.user;
};

// Logout
const logout = async () => {
    const token = localStorage.getItem('token');
    
    await fetch('http://localhost:8000/api/auth/logout', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
        }
    });
    
    localStorage.removeItem('token');
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

// Login
const login = async (email, password) => {
    const response = await api.post('/auth/login', { email, password });
    if (response.data.success) {
        localStorage.setItem('token', response.data.data.token);
        return response.data.data.user;
    }
};

// Get current user
const getCurrentUser = async () => {
    const response = await api.get('/auth/me');
    return response.data.data.user;
};
```

## Security Notes

1. **Token Storage**: Simpan token di localStorage atau sessionStorage dengan aman
2. **HTTPS**: Gunakan HTTPS di production
3. **Token Expiration**: Token tidak memiliki expiration default, pertimbangkan untuk mengatur expiration
4. **CORS**: Konfigurasi CORS sudah diset untuk development, sesuaikan untuk production

## Next Steps

1. Implementasi endpoint untuk data actuals dan predictions
2. Tambahkan role-based authorization
3. Implementasi rate limiting
4. Tambahkan logging untuk audit trail
5. Setup environment variables untuk production
