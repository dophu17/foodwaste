# Food Waste Management System

A Laravel-based web application for managing food waste in restaurants.

## About

This project is designed to help restaurants track, manage, and reduce food waste through a comprehensive web-based system built with Laravel framework.

## Features

- Food waste tracking and monitoring
- Restaurant management
- Waste analytics and reporting
- User authentication and authorization
- Responsive web interface

## Technology Stack

- **Backend**: Laravel (PHP Framework)
- **Database**: MySQL/PostgreSQL
- **Frontend**: Blade templates with CSS/JavaScript
- **Authentication**: Laravel Breeze/Jetstream

## Requirements

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Web server (Apache/Nginx)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/dophu17/foodwaste.git
   cd foodwaste
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy environment file:
   ```bash
   cp .env.example .env
   ```

4. Configure your database in `.env` file

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Run migrations:
   ```bash
   php artisan migrate
   ```

7. Start the development server:
   ```bash
   php artisan serve
   ```

## Contributing

Thank you for considering contributing to this project! Please feel free to submit issues and pull requests.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
