# Arcadia Zoo: Zoo Management System

My first full-stack web project, built as part of the DWWM diploma (Développeur Web et Web Mobile). A complete zoo management system with a public-facing website and three role-based dashboards for admins, vets, and employees.

Built in 2024. Looking back, I can clearly see what I'd do differently. That's the point.

## What It Does

**Public website:**
- Browse animals by habitat zone with click-tracking analytics
- View detailed animal profiles (species, diet, lifespan, characteristics)
- Book zoo services
- Submit visitor reviews (moderated by admin)
- Contact form

**Admin dashboard:**
- Full CRUD for animals, habitats, services, and users
- Review moderation (approve/reject)
- Manage veterinary reports

**Employee dashboard:**
- Log animal feeding records

**Vet dashboard:**
- Submit and view veterinary health reports

**Analytics:**
- Animal view tracking stored in MongoDB

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8, no framework (procedural) |
| Primary DB | MySQL via PDO |
| Analytics DB | MongoDB (animal click tracking) |
| Frontend | HTML5, Bootstrap 4, custom CSS |
| Auth | PHP sessions with role-based access |

## Database Design

10 MySQL tables covering: users, roles, animals, habitats, services, reviews, veterinary reports, feeding records, visitors, and bookings. Relationships enforced via foreign keys.

## Setup

```bash
git clone https://github.com/theWQLker/arcadia-zoo-management
cd arcadia-zoo-management
composer install

# Copy and configure database credentials
cp config2.example.php config2.php
# Edit config2.php with your MySQL credentials

# Import schema
mysql -u user -p arcadia_db < arcadia_dump.sql

# Start local server
php -S localhost:8000
```

## Honest Reflection

This was written before I knew what MVC was. Looking at it now I can identify:

- **No password hashing:** plaintext storage. I knew it was wrong, I just didn't fix it in time for the exam deadline.
- **Broken role-based auth:** session logic was inverted. The dashboards work in demo but the auth check fails under real conditions.
- **No framework:** every file mixes SQL queries, business logic, and HTML output. EcoRide, my next project, was built with Slim to fix exactly this.
- **MongoDB sprawl:** three competing stats implementations. I was experimenting and never cleaned up.

What I'd do today: Laravel or Slim, proper MVC, bcrypt passwords, middleware-based auth, centralized DB config via `.env`.

## What I Learned

- Relational database design from scratch (schema, foreign keys, normalization)
- PDO prepared statements and SQL injection prevention
- Session-based authentication and role management concepts
- Dual-database architecture (MySQL + MongoDB)
- PHP OOP principles
- Bootstrap responsive layouts

---

*Part of DWWM diploma coursework, 2024*
