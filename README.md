<p align="center">
  <img src="https://img.icons8.com/fluency/96/graduation-cap.png" width="80" alt="SmartRapor Logo">
</p>

<h1 align="center">SMART<span>RAPOR</span></h1>
<p align="center"><strong>Student Report Card Management System</strong></p>

<p align="center">
  ![PHP](https://img.shields.io/badge/language-PHP-777BB4)
  ![Laravel](https://img.shields.io/badge/framework-Laravel-FF2D20)
  ![Tailwind](https://img.shields.io/badge/css-Tailwind-06B6D4)
  ![Alpine.js](https://img.shields.io/badge/js-Alpine.js-8BC0D0)
  ![Flowbite](https://img.shields.io/badge/ui-Flowbite-1A56DB)
  ![Vite](https://img.shields.io/badge/build-Vite-646CFF)
  ![Chart.js](https://img.shields.io/badge/chart-Chart.js-FF6384)
  ![MySQL](https://img.shields.io/badge/database-MySQL-4479A1)
</p>

<p align="center">
  ![Last Commit](https://img.shields.io/github/last-commit/drvn-sss/sistem-pengolahan-rapor-siswa?color=green)
  ![Repo Size](https://img.shields.io/github/repo-size/drvn-sss/sistem-pengolahan-rapor-siswa)
  ![License](https://img.shields.io/badge/license-MIT-blue)
</p>

---

## About

**SmartRapor** is a web-based student report card management system designed for schools to efficiently manage academic data. Built with **Laravel 12** and a modern flat UI design, it provides a centralized platform for administrators to manage students, teachers, classes, subjects, grading, and report card generation.

The application features a clean, component-based architecture using **Blade components**, **Tailwind CSS** for styling, **Alpine.js** for client-side interactivity, and **Chart.js** for dashboard analytics visualization.

---

## Features

### Authentication
- Secure login system with password visibility toggle
- Forgot password (password recovery) page
- Change password functionality

### Dashboard
- Overview statistics cards (Total Students, Teachers, Classes, Subjects)
- **Grade Trend Chart** — Line chart tracking average student grades per semester
- **Grade Distribution Chart** — Bar chart showing grade distribution (A–E)
- **Grade Completeness Chart** — Doughnut chart visualizing input progress
- **Subject Grades by Class** — Filterable bar chart by department & subject

### Master Data Management
- **Student Data** (`Data Siswa`) — Full CRUD with search and pagination
- **Teacher Data** (`Data Guru`) — Manage teacher records with NIP and contact info
- **Class Data** (`Data Kelas`) — Manage classes with department and grade level
- **Subject Data** (`Data Mapel`) — Manage subjects with curriculum mapping

### Academic Module
- **Teaching Assignment** (`Pengampu`) — Assign teachers to subjects and classes
- **Grade Input** (`Input Nilai`) — Enter assignment, midterm (UTS), and final exam (UAS) scores
- **Grade Recap** (`Rekap Nilai`) — View and filter consolidated student grades with pass/fail status
- **Attendance** (`Presensi`) — Track student attendance records

### Report Card
- **Student Report Card** (`Rapor Siswa`) — Generate and manage student report cards

### Reusable UI Components
- `<x-stat-card>` — Statistics display card
- `<x-chart-card>` — Chart container with title and icon
- `<x-search-toolbar>` — Search bar with filter integration
- `<x-action-buttons>` — Standardized CRUD action buttons
- `<x-modal>` — Reusable modal dialog
- `<x-badge>` — Status badge (success/warning)
- `<x-pagination>` — Table pagination component
- `<x-sidebar>` — Collapsible navigation sidebar with active state highlighting

---

## Tech Stack

| Layer        | Technology                                                     |
|--------------|----------------------------------------------------------------|
| **Backend**  | PHP 8.2+, Laravel 12, Eloquent ORM                            |
| **Frontend** | Blade Templates, Tailwind CSS 3.4, Alpine.js 3.x              |
| **UI Kit**   | Flowbite 4.0, Font Awesome 6                                  |
| **Build**    | Vite 7, PostCSS, Autoprefixer                                 |
| **Charts**   | Chart.js (via CDN)                                             |
| **Database** | MySQL (via XAMPP)                                              |
| **Font**     | [Inter](https://fonts.google.com/specimen/Inter) (Google Fonts)|

---

## Project Structure

```
sistem-pengolahan-rapor-siswa/
├── app/
│   ├── Http/Controllers/     # Route controllers
│   └── Models/               # Eloquent models (User, Guru, Kelas, Mapel, Pengampu)
├── database/
│   ├── migrations/           # Database schema definitions
│   └── seeders/              # Sample data seeders
├── resources/views/
│   ├── layouts/              # Master layouts (app.blade.php, guest.blade.php)
│   ├── components/           # Reusable Blade components
│   └── pages/                # Application pages
├── routes/
│   └── web.php               # Web route definitions
├── public/                   # Public assets & entry point
├── vite.config.js            # Vite build configuration
├── tailwind.config.js        # Tailwind CSS configuration
└── composer.json             # PHP dependencies
```

---

## Getting Started

### Prerequisites

- **PHP** ≥ 8.2
- **Composer** ≥ 2.x
- **Node.js** ≥ 18.x
- **MySQL** ≥ 8.0 (or MariaDB)
- **XAMPP** (recommended) or any LAMP/LEMP stack

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/drvn-sss/sistem-pengolahan-rapor-siswa.git
   cd sistem-pengolahan-rapor-siswa
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure the database**

   Update `.env` with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_raporsiswa
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   ```

7. **Start the development server**
   ```bash
   composer dev
   ```
   This concurrently starts: Laravel server, queue worker, Pail log viewer, and Vite dev server.

   Alternatively, run them individually:
   ```bash
   php artisan serve
   npm run dev
   ```

8. **Visit the application**
   ```
   http://localhost:8000
   ```

---

## Screenshots

> _Screenshots coming soon — contributions welcome!_

<!--
Add screenshots here:
![Dashboard](docs/screenshots/dashboard.png)
![Login](docs/screenshots/login.png)
![Grade Recap](docs/screenshots/rekap-nilai.png)
-->

---

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## License

This project is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).

---

<p align="center">
  Built with <a href="https://laravel.com">Laravel</a> &amp; <a href="https://tailwindcss.com">Tailwind CSS</a>
</p>
