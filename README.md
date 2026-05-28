<p align="center">
  <img src="https://img.icons8.com/fluency/96/graduation-cap.png" width="80" alt="SmartRapor Logo">
</p>

<h1 align="center">SMART<span>RAPOR</span></h1>
<p align="center"><strong>Student Report Card Management System</strong></p>

<p align="center">
  <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP"></a>
  <a href="https://laravel.com/"><img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://tailwindcss.com/"><img src="https://img.shields.io/badge/Tailwind_CSS-3.4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white" alt="Tailwind CSS"></a>
  <a href="https://alpinejs.dev/"><img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=flat-square&logo=alpinedotjs&logoColor=white" alt="Alpine.js"></a>
  <a href="https://flowbite.com/"><img src="https://img.shields.io/badge/Flowbite-4.0-1A56DB?style=flat-square&logo=flowbite&logoColor=white" alt="Flowbite"></a>
  <a href="https://vitejs.dev/"><img src="https://img.shields.io/badge/Vite-7-646CFF?style=flat-square&logo=vite&logoColor=white" alt="Vite"></a>
  <a href="https://www.chartjs.org/"><img src="https://img.shields.io/badge/Chart.js-4-FF6384?style=flat-square&logo=chartdotjs&logoColor=white" alt="Chart.js"></a>
  <a href="https://www.mysql.com/"><img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL"></a>
</p>

<p align="center">
  <a href="https://github.com/drvn-sss/sistem-pengolahan-rapor-siswa"><img src="https://img.shields.io/github/last-commit/drvn-sss/sistem-pengolahan-rapor-siswa?style=flat-square&color=green" alt="Last Commit"></a>
  <a href="https://github.com/drvn-sss/sistem-pengolahan-rapor-siswa"><img src="https://img.shields.io/github/repo-size/drvn-sss/sistem-pengolahan-rapor-siswa?style=flat-square" alt="Repo Size"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-yellow?style=flat-square" alt="License"></a>
</p>

---

## 📖 About

**SmartRapor** is a web-based student report card management system designed for schools to efficiently manage academic data. Built with **Laravel 12** and a modern flat UI design, it provides a centralized platform for administrators to manage students, teachers, classes, subjects, grading, and report card generation.

The application features a clean, component-based architecture using **Blade components**, **Tailwind CSS** for styling, **Alpine.js** for client-side interactivity, and **Chart.js** for dashboard analytics visualization.

---

## ✨ Features

### 🔐 Authentication
- Secure login system with password visibility toggle
- Forgot password (password recovery) page
- Change password functionality

### 📊 Dashboard
- Overview statistics cards (Total Students, Teachers, Classes, Subjects)
- **Grade Trend Chart** — Line chart tracking average student grades per semester
- **Grade Distribution Chart** — Bar chart showing grade distribution (A–E)
- **Grade Completeness Chart** — Doughnut chart visualizing input progress
- **Subject Grades by Class** — Filterable bar chart by department & subject

### 📁 Master Data Management
- **Student Data** (`Data Siswa`) — Full CRUD with search and pagination
- **Teacher Data** (`Data Guru`) — Manage teacher records with NIP and contact info
- **Class Data** (`Data Kelas`) — Manage classes with department and grade level
- **Subject Data** (`Data Mapel`) — Manage subjects with curriculum mapping

### 🎓 Academic Module
- **Teaching Assignment** (`Pengampu`) — Assign teachers to subjects and classes
- **Grade Input** (`Input Nilai`) — Enter assignment, midterm (UTS), and final exam (UAS) scores
- **Grade Recap** (`Rekap Nilai`) — View and filter consolidated student grades with pass/fail status
- **Attendance** (`Presensi`) — Track student attendance records

### 📄 Report Card
- **Student Report Card** (`Rapor Siswa`) — Generate and manage student report cards

### 🧩 Reusable UI Components
- `<x-stat-card>` — Statistics display card
- `<x-chart-card>` — Chart container with title and icon
- `<x-search-toolbar>` — Search bar with filter integration
- `<x-action-buttons>` — Standardized CRUD action buttons
- `<x-modal>` — Reusable modal dialog
- `<x-badge>` — Status badge (success/warning)
- `<x-pagination>` — Table pagination component
- `<x-sidebar>` — Collapsible navigation sidebar with active state highlighting

---

## 🛠️ Tech Stack

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

## 📁 Project Structure

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

## 🚀 Getting Started

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

## 📸 Screenshots

> _Screenshots coming soon — contributions welcome!_

<!--
Add screenshots here:
![Dashboard](docs/screenshots/dashboard.png)
![Login](docs/screenshots/login.png)
![Grade Recap](docs/screenshots/rekap-nilai.png)
-->

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

This project is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).

---

<p align="center">
  Built with ❤️ using <a href="https://laravel.com">Laravel</a> &amp; <a href="https://tailwindcss.com">Tailwind CSS</a>
</p>
