To follow my video (https://youtu.be/sPfpEoaILls) you'll need to clone this branch of the repository

## Installation

To install use composer

```bash
composer install
```

## Usage

Change the database cridentials to match your's

```env
# .env

# Database Cridentials
DB_HOST = <host-name-here>
DB_USER = <database-username-here>
DB_PASSWORD = <database-password-here>
DB_NAME = <database-name-here>
DB_CHASET = 'utf8mb4'
```

### Start Development Server

```bash
php -S localhost:8080
```
