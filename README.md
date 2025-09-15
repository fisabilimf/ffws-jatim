# FFWS Jatim

## Overview
FFWS Jatim is a comprehensive project designed to provide forecasting and flood warning systems for the Jatim region. The project is divided into three main components:

1. **Backend**: Built with Laravel, it handles the core business logic, API endpoints, and database interactions.
2. **Forecasting**: A Python-based module for running forecasting models and data preprocessing.
3. **Frontend**: A modern web interface for users to interact with the system.

---

## Project Structure

```
backend/         # Laravel backend for API and business logic
forecasting/     # Python-based forecasting models and utilities
frontend/        # Frontend web application
```

### Backend
- **Framework**: Laravel
- **Key Files**:
  - `artisan`: Laravel CLI tool
  - `composer.json`: PHP dependencies
  - `routes/web.php`: Web routes
  - `app/Models/`: Eloquent models

### Forecasting
- **Language**: Python
- **Key Files**:
  - `run_dev_server.py`: Development server for forecasting
  - `requirements.txt`: Python dependencies
  - `app/`: Core Python modules for forecasting
  - `models/`: Pre-trained forecasting models
  - `scalers/`: Scaler files for data normalization

### Frontend
- **Framework**: Vite + Tailwind CSS
- **Key Files**:
  - `index.html`: Entry point
  - `src/`: Source code for the frontend
  - `tailwind.config.js`: Tailwind CSS configuration

---

## Installation

### Prerequisites
- PHP 8.1+
- Composer
- Node.js 16+
- Python 3.12+
- MySQL

### Backend Setup
1. Navigate to the `backend` directory:
   ```bash
   cd backend
   ```
2. Install dependencies:
   ```bash
   composer install
   ```
3. Set up the `.env` file:
   ```bash
   cp .env.example .env
   ```
4. Generate the application key:
   ```bash
   php artisan key:generate
   ```
5. Run migrations:
   ```bash
   php artisan migrate
   ```

### Forecasting Setup
1. Navigate to the `forecasting` directory:
   ```bash
   cd forecasting
   ```
2. Install Python dependencies:
   ```bash
   pip install -r requirements.txt
   ```
3. Set up the `.env` file:
   ```bash
   cp .env.example .env
   ```
4. Run the development server:
   ```bash
   python run_dev_server.py
   ```

### Frontend Setup
1. Navigate to the `frontend` directory:
   ```bash
   cd frontend
   ```
2. Install Node.js dependencies:
   ```bash
   npm install
   ```
3. Start the development server:
   ```bash
   npm run dev
   ```

---

## Usage

### Backend
- Start the Laravel development server:
  ```bash
  php artisan serve
  ```

### Forecasting
- Run forecasting scripts or start the development server as needed.

### Frontend
- Access the frontend at `http://localhost:3000` after starting the development server.

---

## Contributing
1. Fork the repository.
2. Create a new branch:
   ```bash
   git checkout -b feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add feature"
   ```
4. Push to the branch:
   ```bash
   git push origin feature-name
   ```
5. Open a pull request.

---

## License
This project is licensed under the MIT License. See the `LICENSE` file for details.

---

## Acknowledgments
- Laravel Framework
- Python Community
- Tailwind CSS
- Vite
