-- ============================================================
-- UIndy Grab-N-Go Bulk Order System — Database Setup
-- Run once: mysql -u root -p < setup.sql
-- ============================================================

--DROP DATABASE IF EXISTS GrabNGo;
--CREATE DATABASE GrabNGo;
--USE GrabNGo;

-- -------------------------------------------------------
-- Users authenticated via SAML / Entra ID
-- -------------------------------------------------------
CREATE TABLE users (
    email       VARCHAR(100) NOT NULL,
    first_name  VARCHAR(100),
    last_name   VARCHAR(100),
    last_login  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (email)
);

-- -------------------------------------------------------
-- Beverage categories (main dropdown headers)
-- -------------------------------------------------------
CREATE TABLE categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    icon_path   VARCHAR(255) DEFAULT NULL   -- optional category icon
);

-- -------------------------------------------------------
-- Menu items belonging to a category
-- -------------------------------------------------------
CREATE TABLE menu_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name        VARCHAR(150) NOT NULL,
    case_cost   DECIMAL(10,2) NOT NULL,
    pack_qty    INT NOT NULL,
    image_path  VARCHAR(255) DEFAULT NULL,  -- optional flavor image
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- -------------------------------------------------------
-- Bulk orders submitted by students
-- -------------------------------------------------------
CREATE TABLE orders (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_email  VARCHAR(100) NOT NULL,
    user_name   VARCHAR(200) NOT NULL,
    item_id     INT NOT NULL,
    item_name   VARCHAR(150) NOT NULL,
    category    VARCHAR(100) NOT NULL,
    case_cost   DECIMAL(10,2) NOT NULL,
    pack_qty    INT NOT NULL,
    status      ENUM('pending','completed','denied') DEFAULT 'pending',
    ordered_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_email) REFERENCES users(email),
    FOREIGN KEY (item_id)    REFERENCES menu_items(id)
);

-- -------------------------------------------------------
-- Admin accounts (manage via CLI — NOT through the website)
-- To add an admin run:
--   INSERT INTO admins (username, password)
--   VALUES ('your_username', SHA2('your_password', 256));
-- -------------------------------------------------------
CREATE TABLE admins (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(200) NOT NULL   -- SHA-256 hex digest
);

-- ============================================================
-- Seed: Categories
-- ============================================================
INSERT INTO categories (name) VALUES
  ('Sodas & Water'),
  ('Tropicana Juice'),
  ('Pure Leaf Tea'),
  ('Propel'),
  ('Muscle Milk'),
  ('Rockstar'),
  ('Starbucks'),
  ('16oz Celsius'),
  ('12oz Celsius'),
  ('Gatorade'),
  ('Alani');

-- ============================================================
-- Seed: Menu Items
-- ============================================================

-- Sodas & Water (category_id = 1)
INSERT INTO menu_items (category_id, name, case_cost, pack_qty) VALUES
  (1, 'Aquafina',           22, 24),
  (1, 'Lifewater',          50, 12),
  (1, 'Pepsi',              60, 24),
  (1, 'Diet Pepsi',         60, 24),
  (1, 'Pepsi Zero',         60, 24),
  (1, 'Pepsi Cherry',       60, 24),
  (1, 'Dr. Pepper',         60, 24),
  (1, 'Diet Dr. Pepper',    60, 24),
  (1, 'Mtn Dew',            60, 24),
  (1, 'Diet Mtn Dew',       60, 24),
  (1, 'Mtn Dew Baja Blast', 60, 24),
  (1, 'Starry',             60, 24),
  (1, 'Crush Orange',       60, 24),
  (1, 'Crush Grape',        60, 24),
  (1, 'Schweppes Ginger Ale',60,24),
  (1, 'Root Beer',          60, 24);

-- Tropicana Juice (category_id = 2)
INSERT INTO menu_items (category_id, name, case_cost, pack_qty) VALUES
  (2, 'Apple Juice',          40, 12),
  (2, 'Peach',                40, 12),
  (2, 'Orange Juice',         40, 12),
  (2, 'Cranberry',            40, 12),
  (2, 'Raspberry Lemonade',   40, 12),
  (2, 'Strawberry Lemonade',  40, 12);

-- Pure Leaf Tea (category_id = 3)
INSERT INTO menu_items (category_id, name, case_cost, pack_qty) VALUES
  (3, 'Extra Sweet Tea',      40, 12),
  (3, 'Sweet Tea',            40, 12),
  (3, 'Unsweetened Tea',      40, 12),
  (3, 'Raspberry Tea',        40, 12),
  (3, 'Zero Sugar Sweet Tea', 40, 12),
  (3, 'Black Cherry Tea',     40, 12),
  (3, 'Lemon Tea',            40, 12);

-- Propel (category_id = 4)
INSERT INTO menu_items (category_id, name, case_cost, pack_qty) VALUES
  (4, 'Black Cherry',         40, 12),
  (4, 'Grape',                40, 12),
  (4, 'Berry',                40, 12),
  (4, 'Strawberry Lemonade',  40, 12),
  (4, 'Watermelon',           40, 12),
  (4, 'Peach',                40, 12);

-- Muscle Milk (category_id = 5)
INSERT INTO menu_items (category_id, name, case_cost, pack_qty) VALUES
  (5, 'Vanilla',                  90, 12),
  (5, 'Chocolate',                90, 12),
  (5, 'Chocolate Peanut Butter',  90, 12),
  (5, 'Strawberry',               90, 12);

-- Rockstar (category_id = 6)
INSERT INTO menu_items (category_id, name, case_cost, pack_qty) VALUES
  (6, 'Sugar Free',               45, 12),
  (6, 'Whipped Strawberry',       45, 12),
  (6, 'Orange',                   45, 12),
  (6, 'Recovery Lemonade',        45, 12),
  (6, 'Recovery Strawberry Lemon',45, 12),
  (6, 'Recovery Berryade',        45, 12),
  (6, 'Fruit Punch',              45, 12);

-- Starbucks (category_id = 7)
INSERT INTO menu_items (category_id, name, case_cost, pack_qty) VALUES
  (7, 'Mocha',                60, 12),
  (7, 'Vanilla',              60, 12),
  (7, 'Caramel',              60, 12),
  (7, 'Tripleshot Caramel',   60, 12),
  (7, 'Tripleshot Vanilla',   60, 12),
  (7, 'Tripleshot Mocha',     60, 12),
  (7, 'Pink Drink',           60, 12);

-- 16oz Celsius (category_id = 8)
INSERT INTO menu_items (category_id, name, case_cost, pack_qty) VALUES
  (8, 'Energy Blueberry Lemonade', 50, 12),
  (8, 'Energy Tropical Peach',     50, 12),
  (8, 'Energy Watermelon Twist',   50, 12);

-- 12oz Celsius (category_id = 9)
INSERT INTO menu_items (category_id, name, case_cost, pack_qty) VALUES
  (9, 'Mango Lemonade',       45, 12),
  (9, 'Grape',                45, 12),
  (9, 'Cherry Cola',          45, 12),
  (9, 'Peach Vibe',           45, 12),
  (9, 'Arctic Vibe',          45, 12),
  (9, 'Galaxy Vibe',          45, 12),
  (9, 'Cosmic',               45, 12),
  (9, 'Blu Razz Lemonade',    45, 12),
  (9, 'Watermelon',           45, 12),
  (9, 'Green Apple Cherry',   45, 12),
  (9, 'Blue Crush',           45, 12),
  (9, 'Dragonberry',          45, 12);

-- Gatorade (category_id = 10)
INSERT INTO menu_items (category_id, name, case_cost, pack_qty) VALUES
  (10, 'Orange',          80, 24),
  (10, 'Lemon Lime',      80, 24),
  (10, 'Fruit Punch',     80, 24),
  (10, 'Glacier Freeze',  80, 24),
  (10, 'Cool Blue',       80, 24),
  (10, 'Rip Tide',        80, 24),
  (10, 'Grape',           80, 24),
  (10, 'Green Apple',     80, 24),
  (10, 'Watermelon',      80, 24);

-- Alani (category_id = 11)
INSERT INTO menu_items (category_id, name, case_cost, pack_qty) VALUES
  (11, 'Cherry Limeade',     60, 12),
  (11, 'Classic Cola',       60, 12),
  (11, 'Strawberry Lemon',   60, 12),
  (11, 'Wild Berry',         60, 12),
  (11, 'Orange Kiss',        60, 12),
  (11, 'Sherbet',            60, 12),
  (11, 'Hawaiian Shaved Ice',60, 12),
  (11, 'Juicy Peach',        60, 12),
  (11, 'Cotton Candy',       60, 12),
  (11, 'Breezeberry',        60, 12),
  (11, 'Cosmic',             60, 12);

-- Added to monitor if something is out of stock
ALTER TABLE menu_items
  ADD COLUMN is_available TINYINT(1) NOT NULL DEFAULT 1
  COMMENT '1 = available to order, 0 = out of stock / hidden from ordering';

CREATE TABLE IF NOT EXISTS settings (
    `key`   VARCHAR(100) NOT NULL PRIMARY KEY,
    `value` VARCHAR(255) NOT NULL DEFAULT ''
);

INSERT IGNORE INTO settings (`key`, `value`) VALUES ('ordering_paused', '0');

-- ============================================================
-- Example: create the first admin account
-- Replace 'adminuser' and 'yourpassword' with real values
-- ============================================================
-- INSERT INTO admins (username, password)
-- VALUES ('adminuser', SHA2('yourpassword', 256));
