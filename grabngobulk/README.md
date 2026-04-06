# UIndy Grab-N-Go Bulk Order System
### Setup & Developer Guide

---

## Project Structure
```
grabngobulk/
├── index.php            ← Student landing / SSO entry point
├── menu.php             ← Main ordering page (requires SAML login)
├── place_order.php      ← AJAX: submit an order (POST, returns JSON)
├── order_success.php    ← Thank-you / confirmation page
├── logout.php           ← SAML logout
├── db.php               ← PDO database connection
├── setup.sql            ← Full database schema + seed data
│
├── auth/
│   ├── saml.php         ← SimpleSAMLphp wrapper (configure this)
│   └── login.php        ← SAML callback — stores session, upserts user
│
├── admin/
│   ├── login.php        ← Admin username/password login
│   ├── dashboard.php    ← Live order ticket monitor
│   ├── update_order.php ← AJAX: complete or deny a ticket (POST)
│   └── logout.php       ← Admin logout
│
└── assets/
    └── css/style.css    ← All styles (mobile-first, responsive)
```

---

## 1 — Database Setup

```bash
# Import the full schema and seed data
mysql -u root -p < setup.sql
```

This creates the `GrabNGo` database with tables:
- `users` — authenticated students (populated automatically on first login)
- `categories` — beverage categories
- `menu_items` — individual flavors with cost and pack size
- `orders` — submitted bulk requests
- `admins` — admin accounts (CLI-managed only)

---

## 2 — Create an Admin Account

Admin accounts **cannot** be created through the website (by design).
Run this SQL directly in MySQL:

```sql
USE GrabNGo;
INSERT INTO admins (username, password)
VALUES ('yourAdminUsername', SHA2('yourSecurePassword', 256));
```

To log in, go to: `https://yourdomain.com/grabngobulk/admin/login.php`

---

## 3 — Configure the Database Connection

Edit `db.php`:
```php
$dsn     = 'mysql:host=localhost;dbname=GrabNGo;charset=utf8mb4';
$db_user = 'root';
$db_pass = 'your_mysql_password';
```

In Docker, `host` is likely the MySQL container name, e.g. `db` or `mysql`.

---

## 4 — SAML / Entra ID (UIndy Microsoft SSO) Setup

### Step A — Install SimpleSAMLphp
```bash
composer require simplesamlphp/simplesamlphp
# OR download from https://simplesamlphp.org/ and extract
```

### Step B — Point the code at your installation
Edit `auth/saml.php`, line 1:
```php
define('SIMPLESAML_PATH', '/var/www/html/simplesamlphp');
```

### Step C — Configure an SP auth source
In `simplesamlphp/config/authsources.php`:
```php
'uindy-entra' => [
    'saml:SP',
    'entityID' => 'https://YOUR_DOMAIN/grabngobulk/',
    'idp'      => 'https://login.microsoftonline.com/UINDY_TENANT_ID/saml2',
],
```
- Replace `YOUR_DOMAIN` with your server's hostname.
- Replace `UINDY_TENANT_ID` with the Azure tenant ID that UIndy IT provides.

### Step D — Add UIndy's IdP metadata
UIndy IT will give you an XML metadata file for their Entra ID / Azure AD.
Import it into: `simplesamlphp/metadata/saml20-idp-remote.php`

### Step E — Register your SP with UIndy IT
Give IT your SP metadata URL (SimpleSAMLphp generates this at
`https://YOUR_DOMAIN/simplesaml/module.php/saml/sp/metadata/uindy-entra`).
IT will configure Entra ID to trust your app and send back:
- Email (`emailaddress` claim)
- First name (`givenname` claim)
- Last name (`surname` claim)

---

## 5 — Docker / GitHub Notes

Example `docker-compose.yml` additions:
```yaml
services:
  web:
    image: php:8.2-apache
    volumes:
      - ./grabngobulk:/var/www/html/grabngobulk
    ports:
      - "80:80"
  db:
    image: mysql:8
    environment:
      MYSQL_ROOT_PASSWORD: yourpassword
      MYSQL_DATABASE: GrabNGo
```

Change `db.php` host to `db` if using Docker networking.

---

## 6 — Adding Images

**Category icons** (small, shown in the accordion header):
- Place images in `assets/images/categories/`
- Update the `icon_path` column in the `categories` table:
  ```sql
  UPDATE categories SET icon_path = 'assets/images/categories/gatorade.png'
  WHERE name = 'Gatorade';
  ```

**Flavor images** (shown next to each item row):
- Place images in `assets/images/items/`
- Update `image_path` in `menu_items`:
  ```sql
  UPDATE menu_items SET image_path = 'assets/images/items/gatorade_orange.png'
  WHERE name = 'Orange' AND category_id = 10;
  ```

---

## 7 — Changing the Pickup Time

The "ready in X minutes" message is **not hardcoded**.
Edit one line in `order_success.php`:
```php
$pickup_minutes = 30;   // ← change this
```

---

## 8 — Future Features (Planned)

- [ ] Order status notifications (email / SMS to student)
- [ ] Dining-approved discount display in the confirmation modal
- [ ] Multi-language support (language switcher in cogwheel)
- [ ] Admin ability to set prep time per category
- [ ] Order history page for students

---

## Security Notes

- All database queries use **prepared statements** (no SQL injection risk)
- Admin passwords stored as **SHA-256 hashes**
- SAML assertions validated by SimpleSAMLphp (cryptographic signature check)
- `session_regenerate_id(true)` called on admin privilege escalation
- Admin accounts cannot be created via the web interface
