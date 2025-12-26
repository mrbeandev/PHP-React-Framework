# Unified PHP-React Coexistence Template

A high-performance, unified architecture that bridges the gap between **PHP's backend simplicity** and **React's frontend robustness**. This template provides a "Next.js-like" developer experience for the PHP ecosystem, allowing both stacks to coexist and communicate seamlessly in a single project.

## 🚀 The Philosophy: "Simplicity meets Robustness"

Traditional modern stacks often force a radical split between Backend and Frontend, leading to complex CORS issues, separate deployments, and fragmented workflows. 

This template solves that by providing:
- **Unified Entry Point**: A single `public/index.php` act as an intelligent router. It handles API requests, serves static assets with proper MIME types, and manages SPA routing automatically.
- **Eloquent ORM for React**: Leverage **Laravel Eloquent ORM** directly in your React project for elegant data modeling without the overhead of a full framework.
- **Zero-Config DX**: High-speed Vite development with an integrated PHP proxy loop. One command starts your entire stack.
- **Production Ready**: Built-in support for Apache (`.htaccess`) and Nginx, making your modern React app as easy to deploy as a legacy PHP site.

---

## 🛠️ Getting Started

### 1. Prerequisites
- PHP 8.1+
- Node.js & npm
- Composer

### 2. Installation & Setup
```bash
# Install all dependencies
npm install && composer install

# Setup local SQLite database
touch database/database.sqlite
npm run migrate
```

### 3. Development
Run the specialized dev-loop to start PHP and Vite concurrently:
```bash
npm run dev
```
- **Frontend**: [http://localhost:5173](http://localhost:5173) (With Vite HMR)
- **Backend API**: Running on `localhost:8000` (Proxied via `/api/*`)

### 4. Demo Application (TaskFlow)
This template includes a pre-built example called **TaskFlow** to demonstrate the framework's capabilities. 
To see it in action:
```bash
npm run seed
```

---

## 📦 Production & Deployment

### Build the Project
```bash
npm run build
```

### Preview Local Production
Test the *actual* production behavior (including the PHP router logic):
```bash
npm run preview
```

---

## 📂 Architecture Overview

```text
├── app/              # PHP Models & Logic (App\Models)
├── database/         # Data persistence & Migrations
├── public/           # Unified Web Root
│   ├── index.php     # THE BRAINS: API Router + Asset Server
│   └── dist/         # Compiled Production Assets
├── src/              # React Frontend (HMR Enabled)
├── bootstrap.php     # Framework Initialization
└── package.json      # Unified Project Control
```

## 📜 Available Commands

| Command | Action |
| :--- | :--- |
| `npm run dev` | Starts the unified development environment. |
| `npm run build` | Generates optimized production assets. |
| `npm run preview` | Previews the build using the unified PHP router. |
| `npm run migrate` | Syncs Eloquent models with the database. |
| `npm run seed` | Adds demo data to the example application. |

---
Built with ❤️ by **MrbeanDev** for a better PHP+React ecosystem.
