# GymTracker

GymTracker is an application for tracking workouts and fitness progress in the gym. It allows users to record their training sessions, monitor their progress, create workout plans, and much more. The application is built using Vue 3 and Laravel 10, providing performance, scalability, and intuitive user interfaces.

## Live Demo
A live version of the application is available at: [dev.dream-speak.pl/gymtracker](https://dev.dream-speak.pl/gymtracker)

Credentials for the test accounts:
- **Login**: 44TEQHvJJi
**Password**: examplegymtracker2023
- **Login**: gmc1
**Password**: examplegymtracker2023

## Features

- User registration and login
- Creation and editing of workout plans
- Recording workouts and tracking progress
- Displaying workout statistics
- Personalization of user profiles
- BMI calculator for tracking body mass index
- Calorie calculator for monitoring daily calorie intake
- And much more!

## Technologies

- Vue 3 - a modern JavaScript framework for building user interfaces
- Laravel 10 - a powerful PHP framework for rapid and secure web application development

## System Requirements

- Node.js (version >= 18.12.0)
- Npm (version >= 8.19.2)
- PHP (version >= 8.1)
- Composer (version >= 2.2.6)
- MySQL or any other database

## Installation

1. Clone the repository: `git clone https://github.com/PiciuU/GymTracker-App.git`
2. Navigate to the project directory: `cd gym-tracker`
3. Install backend dependencies: `cd backend && composer install`
4. Configure the `.env` file with the appropriate database credentials
5. Generate application key: `php artisan key:generate`
6. Run database migrations: `php artisan migrate`
7. Install frontend dependencies: `cd ../frontend && npm install`
8. Start the Laravel backend server: `cd ../backend && php artisan serve`
9. In a separate terminal, start the Vue frontend server: `cd ../frontend && npm run serve`

Please make sure to have PHP, Composer, and Node.js installed on your system before proceeding with the installation.

## Author

All rights reserved. This repository is authored by PiciuU.