# Multi-Vendor E-Commerce Marketplace (Laravel 13)

A clean, modular, and professional Multi-Vendor E-Commerce Marketplace built with Laravel 13, following standard Laravel MVC conventions and best practices.

---

## 🚀 Features

### 1. User Roles & Security
- **Admin**:
  - Full marketplace dashboard metrics (revenue, orders, vendors, customers, products).
  - Vendor moderation & onboarding: **Admin can create, edit, approve, reject, block, unblock, and delete vendors**.
  - Customer moderation (view, block, unblock).
  - Categories full CRUD.
  - Product catalog oversight (view, edit, status toggle, delete).
  - Orders oversight & fulfillment status updates.
  - Review moderation (delete inappropriate reviews).
- **Vendor**:
  - Dedicated vendor dashboard.
  - Store profile management (store name, description, phone, address).
  - Product management (create, view, edit, delete, multi-image upload, set primary image, stock & price updates).
  - Order fulfillment (view orders containing own store items and update fulfillment status).
  - **Ownership Isolation**: Vendors cannot view, edit, or delete another vendor's products or orders.
- **Customer**:
  - Storefront catalog with search, category filter, price sorting.
  - Persistent shopping cart with stock check.
  - Address book (multiple addresses, default address switcher).
  - Checkout with server-side totals calculation and stock locking via DB transactions.
  - **Stripe Test Payment Gateway** integration (`tok_visa` or live test cards).
  - **Email Confirmation** via Gmail SMTP / PHPMailer on successful checkout.
  - Order history and status tracking.
  - Verified-purchase product review and star rating system.

### 2. Payments & Database Storage
- Every checkout creates an immutable record in the `payments` table linked to `orders`:
  - `order_id`, `transaction_id`, `amount`, `method` (stripe/cod), `status` (paid/pending), `paid_at`.
- Stripe credentials configured in `.env` (`STRIPE_KEY` and `STRIPE_SECRET`).

### 3. Email Delivery
- Powered by PHPMailer & Gmail SMTP (`smtp.gmail.com:587`, TLS):
  - Sends a complete HTML receipt with items list, customer details, and shipping address on order placement.

---

## 🛠️ Requirements & Tech Stack
- **PHP** >= 8.3 (tested on PHP 8.5)
- **Composer**
- **SQLite / MySQL**
- **Stripe PHP SDK** (`stripe/stripe-php`)
- **PHPMailer** (`phpmailer/phpmailer`)
- **Frontend Theme**: Custom minimal Slate + Blue theme (no Bootstrap, fully responsive).

---

## 📦 Installation & Setup

1. **Clone & enter project directory**:
   ```bash
   cd /Volumes/Projects/Laravel/e-com-prototype
   ```

2. **Install Composer dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment (`.env`)**:
   Ensure `.env` contains the required keys:
   ```env
   APP_NAME="MarketPlace"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=sqlite

   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=
   MAIL_PASSWORD=""
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=""
   MAIL_FROM_NAME="${APP_NAME}"

   
   ```

4. **Run Migrations & Seed Demo Data**:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Link Storage for Product Images**:
   ```bash
   php artisan storage:link
   ```

6. **Download Real Product Placeholder Images (Optional)**:
   ```bash
   php artisan download:product-images
   ```

7. **Start Development Server**:
   ```bash
   php artisan serve
   ```
   Open in your browser: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🔑 Demo Accounts

| Role | Email | Password | Details |
|---|---|---|---|
| **Admin (Primary)** | `kanhaiyarathaur0001@gmail.com` | `Neha#145` | Full admin control |
| **Admin (Default)** | `admin@example.com` | `password` | Backup admin |
| **Vendor 1** | `vendor@example.com` | `password` | Apex Tech Store |
| **Vendor 2** | `vendor2@example.com` | `password` | Urban Fashion Hub |
| **Customer** | `customer@example.com` | `password` | Pre-seeded with address & demo orders |

---

## 🧪 Automated Testing

Run the automated test suite:
```bash
php artisan test
```
All tests verify role-based middleware, blocked user rejection, inventory decrement during orders, and ownership policies.
# e-com
