# Student Portal Web Application

A simple student portal built with PHP, MySQL, and Bootstrap.

## Features

- User registration and login
- Dashboard with profile overview
- Profile management with photo upload
- Responsive design

## Requirements

- PHP 7.4+
- MySQL 5.7+
- XAMPP (for local development)

## Installation

1. **Download the project**
   - Extract files to `htdocs` folder

2. **Database Setup**
   - Start XAMPP (Apache + MySQL)
   - Create database named `wap-test`
   - Run setup script: `http://localhost/WAP-Test/database/setup.php`

3. **Configuration**
   - Update database credentials in `config/database.php`

## Usage

- **Home**: `http://localhost/WAP-Test/php/index.php`
- **Register**: `http://localhost/WAP-Test/php/signup.php`
- **Login**: `http://localhost/WAP-Test/php/login.php`
- **Dashboard**: `http://localhost/WAP-Test/php/dashboard.php`
- **Profile**: `http://localhost/WAP-Test/php/profile-update.php`

## Project Structure

```
WAP-Test/
├── assets/css/          # CSS files
├── config/              # Database configuration
├── database/            # Database setup
├── php/                 # PHP pages
├── uploads/             # Profile pictures
└── README.md
```

## Security Features

- Password hashing
- SQL injection prevention
- Session management
- File upload validation

detlete feature in next update


---
