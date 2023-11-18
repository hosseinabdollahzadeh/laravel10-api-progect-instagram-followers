# Laravel API Project

This repository contains a Laravel API project that allows users to follow other users and perform various operations. It includes Swagger documentation for easy API exploration.

## Getting Started

Follow these steps to set up and run the project on your local machine:

### Prerequisites

- PHP 7.3 or higher (including PHP 8.0)
- Composer

### Installation

1. Clone the project repository:

```bash
git clone <repository-url> 
```

2. Navigate to the project directory:
```bash
cd /path/to/project
```
3. Install the project dependencies using Composer:
```bash
composer install
```
4. Copy the .env.example file to .env You can do this by running the following command in your project's root directory:
```bash
cp .env.example .env
```
Open the .env file in a text editor.
Locate the lines that define the database connection settings. They typically look like this:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```
5. Generate the necessary database tables and seed data:
```bash
php artisan migrate:fresh --seed
```
This command will create the required tables and populate them with 100 users, each with an initial coin balance of 50.

### Running the Application

1. Start the local development server:
```bash
php artisan key:generate
php artisan serve
```
This will start the Laravel development server on http://localhost:8000.

2. Open your web browser and navigate to http://localhost:8000/api/documentation to access the Swagger documentation.

### Contributing
Contributions are welcome! If you find any issues or want to contribute to the project, please create a pull request or submit an issue.

### License
This project is licensed by "abdollahzadeh.hossein@gmail.com"
