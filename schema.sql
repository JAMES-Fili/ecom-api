use ecom;
CREATE TABLE categories (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL, 
    description VARCHAR(60)
);
CREATE TABLE products (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(40) NOT NULL,
    description VARCHAR(100),
    price INTEGER,
    imageUrl VARCHAR(255),
    cat_id INTEGER,
    brand VARCHAR(100),
    model VARCHAR(100),
    specifications VARCHAR(255),
    stockQuantity INTEGER,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cat_id) REFERENCES categories(id)
);

CREATE TABLE customers (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(30),
    email VARCHAR(70),
    address VARCHAR(50),
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE bargain_offers (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    product_id INTEGER,
    customer_id INTEGER,
    offeredPrice INTEGER,
    quantity INTEGER,
    message VARCHAR(255),
    status INTEGER,
    createdAt TIMESTAMP,
    adminResponse VARCHAR(255),
    FOREIGN KEY(product_id) REFERENCES products(id),
    FOREIGN KEY(customer_id) REFERENCES customers(id)
);
CREATE TABLE orders (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    customer_id INTEGER,
    status INTEGER,
    paymentMethod TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    whatsappMessageId VARCHAR(255),
    FOREIGN KEY(customer_id) REFERENCES customers(id)
);
CREATE TABLE order_items (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    order_id INTEGER,
    product_id INTEGER,
    quantity INTEGER,
    FOREIGN KEY(order_id) REFERENCES orders(id),
    FOREIGN KEY(product_id) REFERENCES products(id)
);
CREATE TABLE site_settings (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    whatsappNumber VARCHAR(20),
    businessName VARCHAR(255),
    businessAddress VARCHAR(255),
    businessEmail VARCHAR(255),
    deliverySettings VARCHAR(255),
    paymentSettings VARCHAR(255),
    featuredProducts TEXT
);
CREATE TABLE admin_users (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(70),
    password VARCHAR(30),
    role VARCHAR(40)
);
CREATE TABLE inventory_logs (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    product_id INTEGER,
    quantityChange INTEGER,
    newQuabtity INTEGER,
    reason VARCHAR(20),
    admin_id INTEGER,
    createdAt TIMESTAMP,
    FOREIGN KEY(product_id) REFERENCES products(id),
    FOREIGN KEY(admin_id) REFERENCES admin_users(id)
);