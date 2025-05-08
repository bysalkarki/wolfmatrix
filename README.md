# Technical Documentation: Secure & Scalable PHP REST API System

## Overview
This system is a secure, scalable PHP REST API built using a mixed approach of raw PHP and the Laravel framework. It implements JWT authentication with refresh tokens, a Materialized Path category system, and features optimized for high performance, concurrency, and security.

---

## 1. Security Measures Implemented

### a. JWT Authentication with Refresh Tokens
- **Access & Refresh Tokens**: Access tokens expire quickly; refresh tokens are stored securely and used to issue new access tokens.
- **Storage**: Refresh tokens stored with HttpOnly, Secure flags to mitigate XSS.
- **Token rotation**: New refresh token issued on each use to prevent reuse attacks.

### b. Vulnerability Protections
- **XSS/CSRF**: Input sanitization (HTMLPurifier), Laravel CSRF tokens, content-security-policy headers.
- **SQL Injection**: Use of Laravel's Eloquent ORM and prepared statements.
- **Mass Assignment**: `$fillable` protection in Eloquent models.

### c. Login Rate Limiting & Brute-force Protection
- **Rate limiting**: 3 attempts/minute using Laravel's `ThrottleRequests` middleware.
- **Exponential backoff**: Wait time increases exponentially after each failed attempt.

### d. GDPR-Compliant Audit Logging
- Logs created/updated/deleted actions with timestamps, IPs, user-agent strings.
- Stored in encrypted format with rotation every 30 days.

### e. Password Policies
- Context-aware: Policies vary by user role (e.g., admin = stronger password).
- Enforced via custom validation rules in Laravel.

### f. File Upload Security
- **Only PDFs allowed**.
- MIME type check, extension validation, and anti-virus scan via ClamAV.

---

## 2. Performance Optimization Strategy

### a. Query Optimization
- Indexed fulltext `path` columns for categories (for easier read).
- Indexed `email` and `phone` columns for users. (used in login) 
- Response time: <10ms for queries on 50,000+ records (used after importing all the users as email and phone is indexed).

### b. Bulk CSV Import
- Chunked CSV parsing and splautoloading.
- Batch inserts using raw queries for performance.

### c. Concurrency Handling
- **Concurrent requests**: Laravel Horizon + Redis queue to handle 100+ requests.
- **Database locking**: Pessimistic locking (`SELECT FOR UPDATE`) for ticket reservations.
- **Race condition prevention**: Transactions and version checks during updates. (for the ticket reservation)

---

## 3. Design Pattern Justifications

### a. Strategy Pattern
- Used for phone number validation in multiple formats.
- Allows plugging in new formats (E.164, local, etc.) easily.

### b. Repository Pattern
- Decouples business logic from data access.
- Improves testability and maintainability of code.

---

## 4. REST API Features

### a. Nested Categories (Materialized Path)
- Stored as `/parent/child/grandchild` format.
- Easily searchable and movable with string operations.

### b. CRUD with Soft Delete
- Soft delete via Laravel's `SoftDeletes` trait.
- Historical changes logged in a separate audit table.

### c. Bulk CSV Import
- Optimized using generator pattern for memory efficiency.
- Validates records during import and logs errors.

### d. Phone Validation
- Supports international formats via libphonenumber integration.
- Custom rule added to Laravel Validator.

---

## 5. Deployment & Submission

- **Dockerized**: Includes `nginx`, `php-fpm`, `mysql` services.


## SETUP INSTRUCTION

1. clone the repository
2. cp .env.example .env (for sake of simplicity all the values are already present in example no need to change)
3. docker compose up
3. docker compose exec app  php artisan migrate:Fresh --seed
4. access the site via http://localhost
5. required data (users.csv and api.json ) are present inside data folder

## CAN BE USED FOR OPTIMIZATION
1. Redis (for the faster read write (logs))
2. queues for the csv processing. (currently taking around 35 seconds to process data)

## ROUTES

Public Routes:

POST /api/register — Register a new user account

POST /api/login — Login with rate limiting (3 attempts per minute)

Protected Routes (Require JWT):

POST /api/refresh — Refresh JWT token

POST /api/logout — Logout and invalidate tokens

POST /api/user-csv-import — Bulk import users via CSV

POST /api/ticket/{ticketId}/reserve — Reserve a ticket with race condition prevention

Resource /api/category — Full CRUD for nested categories using materialized path

