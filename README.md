# KhudaLagse? — Online Food Ordering System

A lightweight, full-stack web application designed for a seamless restaurant browsing, cart management, and food ordering experience. It features a complete administrative control panel alongside an asynchronous consumer interface, built from scratch using strict Object-Oriented MVC principles in native PHP and clean Vanilla JavaScript/AJAX.

---

## 🚀 Core Features

### 👤 User Authentication & Account Security
- **Robust Session Lifecycle:** Secure registration and login validation backed by modern cryptographic hashing (`password_hash()`).
- **Remember Me Functionality:** Implements secure cookie-based persistent logins utilizing 30-day cryptographically secure tokens.
- **Dynamic Profile Management:** Secure internal profile configurations allowing real-time address modifications and verified password changes requiring existing state proofs.

### 🍔 Administrative Inventory & Control Panels
- **Analytical Metrics Dashboard:** Direct counter visualizations parsing active categories, public recipe items, out-of-stock listings, and live incoming order queues.
- **Relational Category Engine:** Direct inline generation, namespacing, and cascading data validation that safely rejects deleting categories with active product links.
- **Menu Curation Dashboard:** Full multi-part file interface supporting item creation, updates, multi-format image upload processing, and asynchronous stock availability switches.

### 🛒 Asynchronous Shopping Cart & Discovery Engine
- **Vanilla AJAX Shopping Cart:** Completely non-blocking operations including item insertions, continuous adjustments, and instant single-row drops backed by immediate client-side total recalculations.
- **Flexible Order Checkout:** Form inputs allowing customized destination parameters for specific delivery routes with structural choices between Cash or Card payments.
- **Fuzzy Search Processing:** High-performance filtering instantly parsing cross-relational data points like menu titles, categories, and descriptions.

### 📦 Order Flow Lifecycle & Active Sync Polling
- **Customer Progress Portal:** Tabular receipt ledger organizing user transactions by timestamp, built with interactive toggles exposing fully itemized line breakdowns.
- **Asynchronous Order Status Polling:** Client-side background sync executes every 10 seconds via `setInterval`, reading from a dedicated status endpoint to seamlessly morph color-coded UI indicators without requiring page reloads, cleanly detaching once marked `Delivered`.
- **Administrative Operations Queue:** Complete system queue handling date-based filters and exact state groupings. Includes clean status pipelines enforcing step-by-step logic restrictions via REST-like parameters (`Pending` ➔ `Preparing` ➔ `Out for Delivery` ➔ `Delivered`).

---

## 🛠️ Technology Stack & Architecture

- **Backend Architecture:** Native PHP 8.x executing clean Object-Oriented patterns (Strict MVC Architecture).
- **Database Engine:** MySQL via PHP Data Objects (PDO) with full Parameterized Prepared Statements neutralizing SQL Injection vectors.
- **Frontend Presentation:** Semantic HTML5, responsive CSS3 structures with custom variable properties, and native ES6 Vanilla JavaScript (Fetch API).
- **Application Router:** Centralized front-controller mapping (`index.php`) running lightweight URL processing and relative asset path injections.

---

## 📂 Project Structure

```text
WebTech_Project/
├── config/
│   └── database.php          # Central database access and configuration
├── controllers/
│   ├── AdminController.php   # Administrative actions, dashboard, and item logic
│   ├── ApiController.php     # Async RESTful endpoints (Cart, Toggles, Polling)
│   ├── AuthController.php    # Session orchestration and access mechanics
│   ├── OrderController.php   # Consumer order tracking and receipt flows
│   ├── ShopController.php    # Catalog browsing, cart data, and checkouts
│   └── UserController.php    # Personal profiles and verification updates
├── models/
│   ├── Category.php          # Category model mapping
│   ├── MenuItem.php          # Menu items data mapping
│   ├── Order.php             # Order lifecycle management
│   ├── OrderItem.php         # Itemized transactional relations
│   └── User.php              # User identities data mapper
├── views/
│   ├── admin/                # Admin panels (dashboard, categories, orders)
│   ├── auth/                 # Access forms (login and registration layouts)
│   ├── layouts/              # Universal structures (header and footer components)
│   ├── shop/                 # Retail flows (menu grid, cart table, checkout)
│   └── user/                 # Profile details and tracking components
├── uploads/                  # System target location for menu image media
├── index.php                 # App Entrypoint & Custom Router Pipeline
├── schema.sql                # Complete relational MySQL schema maps
└── README.md                 # System documentation
