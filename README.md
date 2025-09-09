# FFWS Jatim

## Overview
The FFWS Jatim project is a comprehensive system designed to support flood forecasting and warning services. It consists of three main components:

1. **Backend**: A Laravel-based application that handles the core logic, API endpoints, and database management.
2. **Forecasting**: A Python-based module for flood forecasting using machine learning models.
3. **Frontend**: A user interface for interacting with the system.

---

## Project Structure

```
backend/
    app/                # Laravel application logic
    config/             # Configuration files
    database/           # Migrations, seeders, and factories
    public/             # Publicly accessible files
    resources/          # Views, CSS, and JS assets
    routes/             # API and web routes
    tests/              # Unit and feature tests
forecasting/
    app/                # Core Python modules for forecasting
    models/             # Pre-trained machine learning models
    scalers/            # Scalers for data preprocessing
    requirements.txt    # Python dependencies
frontend/
    ...                 # Frontend application files
```

---

## Backend

The backend is built using Laravel, a PHP framework. It provides the following features:

- API endpoints for data interaction.
- Database management using migrations and seeders.
- Web routes for serving views.

### Key Files
- `artisan`: Laravel's command-line tool.
- `composer.json`: Dependency management for PHP packages.
- `routes/web.php`: Defines web routes.
- `routes/console.php`: Defines console commands.

---

## Forecasting

The forecasting module is implemented in Python and uses machine learning models to predict flood levels. It includes:

- Pre-trained models for different regions (e.g., GRU, LSTM, TCN).
- Data preprocessing utilities.
- Configuration and threshold settings.

### Key Files
- `app/forecast.py`: Main forecasting logic.
- `models/`: Contains pre-trained models.
- `scalers/`: Data preprocessing scalers.
- `requirements.txt`: Lists Python dependencies.

---

## Frontend

The frontend provides a user-friendly interface for interacting with the system. It is designed to:

- Display flood forecasts and warnings.
- Allow users to input and view data.

---

## Installation

### Backend
1. Navigate to the `backend` directory:
   ```bash
   cd backend
   ```
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Set up the environment file:
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

### Forecasting
1. Navigate to the `forecasting` directory:
   ```bash
   cd forecasting
   ```
2. Install Python dependencies:
   ```bash
   pip install -r requirements.txt
   ```

### Frontend
Instructions for setting up the frontend will depend on the specific framework or tools used.

---

## Usage

### Backend
Start the Laravel development server:
```bash
php artisan serve
```

### Forecasting
Run the forecasting script:
```bash
python cudatest.py
```

---

## Contributing

1. Fork the repository.
2. Create a new branch:
   ```bash
   git checkout -b feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m "Description of changes"
   ```
4. Push to the branch:
   ```bash
   git push origin feature-name
   ```
5. Open a pull request.

---

## License

This project is licensed under the MIT License. See the `LICENSE` file for details.
