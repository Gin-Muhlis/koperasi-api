# Zie Koperasi - Backend

**Zie Koperasi** is a comprehensive backend system designed to manage cooperative data, including savings, loans, installment payments, and product sales. The backend is built using **Laravel 10** with **PHP 8+**, and utilizes **Laravel Passport** for secure token-based authentication. This backend serves as the API layer for the Zie Koperasi frontend, providing robust functionality for both administrators and members.

## Features

### Admin Features
The backend provides powerful APIs for administrators to manage the cooperative’s operations:
1. **Savings and Loan Management**  
   - APIs to manage savings and loan categories.
   - Add or update savings and loan data, both individually and in bulk.

2. **Profile and Member Management**  
   - APIs to manage cooperative profiles, member data, and products.

3. **Installment Payment Handling**  
   - Manage installment payments for loans, ensuring proper financial tracking.

4. **Invoice Management**  
   - Generate invoices containing multiple entries for loans and savings, handling data for multiple members in a single request.

### User Features
The backend also offers APIs that allow cooperative members to access and view their financial data:
1. **View Savings and Loans**  
   - APIs for members to check their savings and loan statuses.

2. **Installment Payments**  
   - Access data on installment payments, including payment history and upcoming dues.

### Authentication and Security
Zie Koperasi backend uses **Laravel Passport** to handle authentication and token management, providing secure access to the API. This allows for:
- Token-based authentication for user sessions.
- Secure access control for both admin and user endpoints.

## Tech Stack

- **Laravel 10**: A PHP framework that provides a clean, structured backend.
- **PHP 8+**: Ensures modern performance and features.
- **Laravel Passport**: Manages OAuth2 token-based authentication for secure API access.

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/Gin-Muhlis/koperasi-api.git


2. Masuk ke direktori project:
   ```bash 
   cd koperasi-api

3. Install dependencies:
   ```bash 
   composer install

4. Atur file environment:
   ```bash
   - Duplikat file .env.example menjadi .env.
   - Ubah detail konfigurasi di file .env sesuai dengan pengaturan database dan konfigurasi lainnya.

5. Jalankan migrasi database:
   ```bash 
   php artisan migrate --seed

6. Install Passport for token management:
   ```bash 
   php artisan passport:install

7. Jalankan server development:
   ```bash 
   php artisan serve