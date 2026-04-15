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

-- ── Category Icons ──────────────────────────────────────────
UPDATE categories SET icon_path = '/assets/img/categories/sodas-water.png'    WHERE name = 'Sodas & Water';
UPDATE categories SET icon_path = '/assets/img/categories/tropicana-juice.png' WHERE name = 'Tropicana Juice';
UPDATE categories SET icon_path = '/assets/img/categories/pure-leaf-tea.png'   WHERE name = 'Pure Leaf Tea';
UPDATE categories SET icon_path = '/assets/img/categories/propel.png'          WHERE name = 'Propel';
UPDATE categories SET icon_path = '/assets/img/categories/muscle-milk.png'     WHERE name = 'Muscle Milk';
UPDATE categories SET icon_path = '/assets/img/categories/rockstar.png'        WHERE name = 'Rockstar';
UPDATE categories SET icon_path = '/assets/img/categories/starbucks.png'       WHERE name = 'Starbucks';
UPDATE categories SET icon_path = '/assets/img/categories/celsius-16oz.png'    WHERE name = '16oz Celsius';
UPDATE categories SET icon_path = '/assets/img/categories/celsius-12oz.png'    WHERE name = '12oz Celsius';
UPDATE categories SET icon_path = '/assets/img/categories/gatorade.png'        WHERE name = 'Gatorade';
UPDATE categories SET icon_path = '/assets/img/categories/alani.png'           WHERE name = 'Alani';
 
-- ── Sodas & Water (category_id = 1) ─────────────────────────
UPDATE menu_items SET image_path = '/assets/img/items/aquafina.png'             WHERE name = 'Aquafina';
UPDATE menu_items SET image_path = '/assets/img/items/lifewater.png'            WHERE name = 'Lifewater';
UPDATE menu_items SET image_path = '/assets/img/items/pepsi.png'                WHERE name = 'Pepsi'              AND category_id = 1;
UPDATE menu_items SET image_path = '/assets/img/items/diet-pepsi.png'           WHERE name = 'Diet Pepsi';
UPDATE menu_items SET image_path = '/assets/img/items/pepsi-zero.png'           WHERE name = 'Pepsi Zero';
UPDATE menu_items SET image_path = '/assets/img/items/pepsi-cherry.png'         WHERE name = 'Pepsi Cherry';
UPDATE menu_items SET image_path = '/assets/img/items/dr-pepper.png'            WHERE name = 'Dr. Pepper';
UPDATE menu_items SET image_path = '/assets/img/items/diet-dr-pepper.png'       WHERE name = 'Diet Dr. Pepper';
UPDATE menu_items SET image_path = '/assets/img/items/mtn-dew.png'              WHERE name = 'Mtn Dew';
UPDATE menu_items SET image_path = '/assets/img/items/diet-mtn-dew.png'         WHERE name = 'Diet Mtn Dew';
UPDATE menu_items SET image_path = '/assets/img/items/mtn-dew-baja-blast.png'   WHERE name = 'Mtn Dew Baja Blast';
UPDATE menu_items SET image_path = '/assets/img/items/starry.png'               WHERE name = 'Starry';
UPDATE menu_items SET image_path = '/assets/img/items/crush-orange.png'         WHERE name = 'Crush Orange';
UPDATE menu_items SET image_path = '/assets/img/items/crush-grape.png'          WHERE name = 'Crush Grape';
UPDATE menu_items SET image_path = '/assets/img/items/schweppes-ginger-ale.png' WHERE name = 'Schweppes Ginger Ale';
UPDATE menu_items SET image_path = '/assets/img/items/root-beer.png'            WHERE name = 'Root Beer';
 
-- ── Tropicana Juice (category_id = 2) ───────────────────────
UPDATE menu_items SET image_path = '/assets/img/items/tropicana-apple-juice.png'        WHERE name = 'Apple Juice';
UPDATE menu_items SET image_path = '/assets/img/items/tropicana-peach.png'              WHERE name = 'Peach'             AND category_id = 2;
UPDATE menu_items SET image_path = '/assets/img/items/tropicana-orange-juice.png'       WHERE name = 'Orange Juice';
UPDATE menu_items SET image_path = '/assets/img/items/tropicana-cranberry.png'          WHERE name = 'Cranberry';
UPDATE menu_items SET image_path = '/assets/img/items/tropicana-raspberry-lemonade.png' WHERE name = 'Raspberry Lemonade' AND category_id = 2;
UPDATE menu_items SET image_path = '/assets/img/items/tropicana-strawberry-lemonade.png' WHERE name = 'Strawberry Lemonade' AND category_id = 2;
 
-- ── Pure Leaf Tea (category_id = 3) ─────────────────────────
UPDATE menu_items SET image_path = '/assets/img/items/pureleaf-extra-sweet-tea.png'     WHERE name = 'Extra Sweet Tea';
UPDATE menu_items SET image_path = '/assets/img/items/pureleaf-sweet-tea.png'           WHERE name = 'Sweet Tea';
UPDATE menu_items SET image_path = '/assets/img/items/pureleaf-unsweetened-tea.png'     WHERE name = 'Unsweetened Tea';
UPDATE menu_items SET image_path = '/assets/img/items/pureleaf-raspberry-tea.png'       WHERE name = 'Raspberry Tea';
UPDATE menu_items SET image_path = '/assets/img/items/pureleaf-zero-sugar-sweet-tea.png' WHERE name = 'Zero Sugar Sweet Tea';
UPDATE menu_items SET image_path = '/assets/img/items/pureleaf-black-cherry-tea.png'    WHERE name = 'Black Cherry Tea';
UPDATE menu_items SET image_path = '/assets/img/items/pureleaf-lemon-tea.png'           WHERE name = 'Lemon Tea';
 
-- ── Propel (category_id = 4) ────────────────────────────────
UPDATE menu_items SET image_path = '/assets/img/items/propel-black-cherry.png'          WHERE name = 'Black Cherry'       AND category_id = 4;
UPDATE menu_items SET image_path = '/assets/img/items/propel-grape.png'                 WHERE name = 'Grape'              AND category_id = 4;
UPDATE menu_items SET image_path = '/assets/img/items/propel-berry.png'                 WHERE name = 'Berry';
UPDATE menu_items SET image_path = '/assets/img/items/propel-strawberry-lemonade.png'   WHERE name = 'Strawberry Lemonade' AND category_id = 4;
UPDATE menu_items SET image_path = '/assets/img/items/propel-watermelon.png'            WHERE name = 'Watermelon'         AND category_id = 4;
UPDATE menu_items SET image_path = '/assets/img/items/propel-peach.png'                 WHERE name = 'Peach'              AND category_id = 4;
 
-- ── Muscle Milk (category_id = 5) ───────────────────────────
UPDATE menu_items SET image_path = '/assets/img/items/musclemilk-vanilla.png'           WHERE name = 'Vanilla'            AND category_id = 5;
UPDATE menu_items SET image_path = '/assets/img/items/musclemilk-chocolate.png'         WHERE name = 'Chocolate'          AND category_id = 5;
UPDATE menu_items SET image_path = '/assets/img/items/musclemilk-chocolate-peanut-butter.png' WHERE name = 'Chocolate Peanut Butter';
UPDATE menu_items SET image_path = '/assets/img/items/musclemilk-strawberry.png'        WHERE name = 'Strawberry'         AND category_id = 5;
 
-- ── Rockstar (category_id = 6) ──────────────────────────────
UPDATE menu_items SET image_path = '/assets/img/items/rockstar-sugar-free.png'          WHERE name = 'Sugar Free';
UPDATE menu_items SET image_path = '/assets/img/items/rockstar-whipped-strawberry.png'  WHERE name = 'Whipped Strawberry';
UPDATE menu_items SET image_path = '/assets/img/items/rockstar-orange.png'              WHERE name = 'Orange'             AND category_id = 6;
UPDATE menu_items SET image_path = '/assets/img/items/rockstar-recovery-lemonade.png'   WHERE name = 'Recovery Lemonade';
UPDATE menu_items SET image_path = '/assets/img/items/rockstar-recovery-strawberry-lemon.png' WHERE name = 'Recovery Strawberry Lemon';
UPDATE menu_items SET image_path = '/assets/img/items/rockstar-recovery-berryade.png'   WHERE name = 'Recovery Berryade';
UPDATE menu_items SET image_path = '/assets/img/items/rockstar-fruit-punch.png'         WHERE name = 'Fruit Punch'        AND category_id = 6;
 
-- ── Starbucks (category_id = 7) ─────────────────────────────
UPDATE menu_items SET image_path = '/assets/img/items/starbucks-mocha.png'              WHERE name = 'Mocha';
UPDATE menu_items SET image_path = '/assets/img/items/starbucks-vanilla.png'            WHERE name = 'Vanilla'            AND category_id = 7;
UPDATE menu_items SET image_path = '/assets/img/items/starbucks-caramel.png'            WHERE name = 'Caramel';
UPDATE menu_items SET image_path = '/assets/img/items/starbucks-tripleshot-caramel.png' WHERE name = 'Tripleshot Caramel';
UPDATE menu_items SET image_path = '/assets/img/items/starbucks-tripleshot-vanilla.png' WHERE name = 'Tripleshot Vanilla';
UPDATE menu_items SET image_path = '/assets/img/items/starbucks-tripleshot-mocha.png'   WHERE name = 'Tripleshot Mocha';
UPDATE menu_items SET image_path = '/assets/img/items/starbucks-pink-drink.png'         WHERE name = 'Pink Drink';
 
-- ── 16oz Celsius (category_id = 8) ──────────────────────────
UPDATE menu_items SET image_path = '/assets/img/items/celsius16-blueberry-lemonade.png' WHERE name = 'Energy Blueberry Lemonade';
UPDATE menu_items SET image_path = '/assets/img/items/celsius16-tropical-peach.png'     WHERE name = 'Energy Tropical Peach';
UPDATE menu_items SET image_path = '/assets/img/items/celsius16-watermelon-twist.png'   WHERE name = 'Energy Watermelon Twist';
 
-- ── 12oz Celsius (category_id = 9) ──────────────────────────
UPDATE menu_items SET image_path = '/assets/img/items/celsius12-mango-lemonade.png'     WHERE name = 'Mango Lemonade';
UPDATE menu_items SET image_path = '/assets/img/items/celsius12-grape.png'              WHERE name = 'Grape'              AND category_id = 9;
UPDATE menu_items SET image_path = '/assets/img/items/celsius12-cherry-cola.png'        WHERE name = 'Cherry Cola';
UPDATE menu_items SET image_path = '/assets/img/items/celsius12-peach-vibe.png'         WHERE name = 'Peach Vibe';
UPDATE menu_items SET image_path = '/assets/img/items/celsius12-arctic-vibe.png'        WHERE name = 'Arctic Vibe';
UPDATE menu_items SET image_path = '/assets/img/items/celsius12-galaxy-vibe.png'        WHERE name = 'Galaxy Vibe';
UPDATE menu_items SET image_path = '/assets/img/items/celsius12-cosmic.png'             WHERE name = 'Cosmic'             AND category_id = 9;
UPDATE menu_items SET image_path = '/assets/img/items/celsius12-blu-razz-lemonade.png'  WHERE name = 'Blu Razz Lemonade';
UPDATE menu_items SET image_path = '/assets/img/items/celsius12-watermelon.png'         WHERE name = 'Watermelon'         AND category_id = 9;
UPDATE menu_items SET image_path = '/assets/img/items/celsius12-green-apple-cherry.png' WHERE name = 'Green Apple Cherry';
UPDATE menu_items SET image_path = '/assets/img/items/celsius12-blue-crush.png'         WHERE name = 'Blue Crush';
UPDATE menu_items SET image_path = '/assets/img/items/celsius12-dragonberry.png'        WHERE name = 'Dragonberry';
 
-- ── Gatorade (category_id = 10) ─────────────────────────────
UPDATE menu_items SET image_path = '/assets/img/items/gatorade-orange.png'              WHERE name = 'Orange'             AND category_id = 10;
UPDATE menu_items SET image_path = '/assets/img/items/gatorade-lemon-lime.png'          WHERE name = 'Lemon Lime';
UPDATE menu_items SET image_path = '/assets/img/items/gatorade-fruit-punch.png'         WHERE name = 'Fruit Punch'        AND category_id = 10;
UPDATE menu_items SET image_path = '/assets/img/items/gatorade-glacier-freeze.png'      WHERE name = 'Glacier Freeze';
UPDATE menu_items SET image_path = '/assets/img/items/gatorade-cool-blue.png'           WHERE name = 'Cool Blue';
UPDATE menu_items SET image_path = '/assets/img/items/gatorade-rip-tide.png'            WHERE name = 'Rip Tide';
UPDATE menu_items SET image_path = '/assets/img/items/gatorade-grape.png'               WHERE name = 'Grape'              AND category_id = 10;
UPDATE menu_items SET image_path = '/assets/img/items/gatorade-green-apple.png'         WHERE name = 'Green Apple';
UPDATE menu_items SET image_path = '/assets/img/items/gatorade-watermelon.png'          WHERE name = 'Watermelon'         AND category_id = 10;
 
-- ── Alani (category_id = 11) ────────────────────────────────
UPDATE menu_items SET image_path = '/assets/img/items/alani-cherry-limeade.png'         WHERE name = 'Cherry Limeade';
UPDATE menu_items SET image_path = '/assets/img/items/alani-classic-cola.png'           WHERE name = 'Classic Cola';
UPDATE menu_items SET image_path = '/assets/img/items/alani-strawberry-lemon.png'       WHERE name = 'Strawberry Lemon';
UPDATE menu_items SET image_path = '/assets/img/items/alani-wild-berry.png'             WHERE name = 'Wild Berry';
UPDATE menu_items SET image_path = '/assets/img/items/alani-orange-kiss.png'            WHERE name = 'Orange Kiss';
UPDATE menu_items SET image_path = '/assets/img/items/alani-sherbet.png'                WHERE name = 'Sherbet';
UPDATE menu_items SET image_path = '/assets/img/items/alani-hawaiian-shaved-ice.png'    WHERE name = 'Hawaiian Shaved Ice';
UPDATE menu_items SET image_path = '/assets/img/items/alani-juicy-peach.png'            WHERE name = 'Juicy Peach';
UPDATE menu_items SET image_path = '/assets/img/items/alani-cotton-candy.png'           WHERE name = 'Cotton Candy';
UPDATE menu_items SET image_path = '/assets/img/items/alani-breezeberry.png'            WHERE name = 'Breezeberry';
UPDATE menu_items SET image_path = '/assets/img/items/alani-cosmic.png'                 WHERE name = 'Cosmic'             AND category_id = 11;


-- ============================================================
-- Example: create the first admin account
-- Replace 'adminuser' and 'yourpassword' with real values
-- ============================================================
-- INSERT INTO admins (username, password)
-- VALUES ('adminuser', SHA2('yourpassword', 256));
