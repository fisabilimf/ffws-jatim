# MySQL Database Setup for FFWS-JATIM

## Option 1: Local MySQL Installation

### Step 1: Install MySQL
- Download MySQL Community Server from: https://dev.mysql.com/downloads/mysql/
- Or install via package manager:
  - Windows: `winget install Oracle.MySQL`
  - macOS: `brew install mysql`
  - Ubuntu: `sudo apt install mysql-server`

### Step 2: Start MySQL Service
```bash
# Windows (as Administrator)
net start mysql

# macOS/Linux
sudo service mysql start
# or
brew services start mysql
```

### Step 3: Set Root Password (if not set during installation)
```bash
mysql -u root
ALTER USER 'root'@'localhost' IDENTIFIED BY 'your_password_here';
FLUSH PRIVILEGES;
EXIT;
```

### Step 4: Create Database
```bash
mysql -u root -p
CREATE DATABASE ffws_jatim CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Step 5: Update .env File
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ffws_jatim
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

## Option 2: XAMPP/WAMP Stack

### For XAMPP:
1. Install XAMPP from: https://www.apachefriends.org/
2. Start Apache and MySQL from XAMPP Control Panel
3. Open phpMyAdmin (http://localhost/phpmyadmin)
4. Create database named `ffws_jatim`
5. Use these credentials in .env:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ffws_jatim
DB_USERNAME=root
DB_PASSWORD=
```

## Option 3: Docker MySQL (Recommended for Development)

### Run MySQL in Docker:
```bash
docker run --name mysql-ffws -e MYSQL_ROOT_PASSWORD=password -e MYSQL_DATABASE=ffws_jatim -p 3306:3306 -d mysql:8.0

# Or using docker-compose (create docker-compose.yml):
version: '3.8'
services:
  mysql:
    image: mysql:8.0
    container_name: mysql-ffws
    environment:
      MYSQL_ROOT_PASSWORD: password
      MYSQL_DATABASE: ffws_jatim
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

Then use:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ffws_jatim
DB_USERNAME=root
DB_PASSWORD=password
```

## Test Connection
After setting up MySQL, test the connection:
```bash
php artisan migrate:status
```

## Run Migrations
Once connected successfully:
```bash
php artisan migrate
```

## Seed Database (Optional)
```bash
php artisan db:seed
```

## Start Laravel Server
```bash
php artisan serve --port=8000
```

Your API will be available at: http://localhost:8000/api/

## Common Issues & Solutions

### Issue: "Access denied for user 'root'@'localhost'"
**Solution:** Check your MySQL password and make sure MySQL service is running.

### Issue: "Unknown database 'ffws_jatim'"
**Solution:** Create the database first:
```sql
CREATE DATABASE ffws_jatim CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Issue: "Connection refused"
**Solution:** Make sure MySQL service is running on port 3306.

### Issue: "PDO extension not found"
**Solution:** Enable PHP MySQL extensions in php.ini:
```ini
extension=pdo_mysql
extension=mysqli
```