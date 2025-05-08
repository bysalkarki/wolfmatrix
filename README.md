# PHP REST API with Secure JWT Auth, Nested Categories & High Performance

This project is a robust PHP backend system implementing secure JWT authentication, scalable REST APIs for nested categories, international phone validation, and optimized performance for large datasets.

---

## 🔐 1. JWT Authentication with Refresh Tokens

### ✅ Features:
- **Stateless Auth**: Uses short-lived access tokens and refresh tokens for secure, scalable login sessions. (used package tymon/jwt-auth)

### 🛡 a. Security Vulnerabilities Prevention
- **SQL Injection**: Handled via prepared statements (PDO).
- **XSS**: Output escaping and CSP headers applied.
- **CSRF**: JWT stored outside cookies and verified on each request, with CSRF tokens if needed.

### 🚦 b. Rate Limiting (3 attempts/minute)
- Implemented with middleware to track failed login attempts per IP.
- Blocks further attempts after 3 failures per minute per user/IP (used laravel throttle middleware).

### 🛑 c. Brute-force Protection with Exponential Backoff
- After each failed login, the delay before retry increases exponentially (1s, 2s, 4s, ...) (App\Services\LoginAttemptService).
- Backoff resets after a successful login or timeout period.

### 🧱 d. Design Patterns Used
- **Repository Pattern**: For categories module
- **Service Pattern**:  Used service pattern for JWT, CSVImport and login service

---

## 🌐 2. REST API

### 🧩 a. CRUD for Nested Categories (Materialized Path)
- Implements nested category trees using materialized path (e.g., `1/2/5`).
- Enables efficient querying of entire branches, parents, or children.

### 📥 b. Bulk CSV Import with Memory Optimization
- Large CSV files are processed in chunks using `fgetcsv()` with stream handling.
- Ensures memory footprint stays constant regardless of file size.

### 🕓 c. Soft Delete with Historical Data Preservation
- Records include a `deleted_at` timestamp instead of being physically removed. (audit table store each individual changes using event listiner pattern for categories)
---

## 📱 3. International Phone Number Validation

- implemented using regex for the popular countries only (number should start with + and country code) (App/Rules/InternationPhone).



## 📦 Setup Instructions

```bash
git clone https://github.com/your-org/php-api-system.git
cd php-api-system
composer install
cp .env.example .env
php artisan migrate
php artisan serve
