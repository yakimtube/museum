CREATE DATABASE museum_guide CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE museum_guide;

CREATE TABLE languages (
    id VARCHAR(2) PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE access_codes (
    code VARCHAR(10) PRIMARY KEY,
    valid_from DATETIME NOT NULL,
    valid_until DATETIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE exhibits (
    id VARCHAR(10) PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE exhibit_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exhibit_id VARCHAR(10),
    language_id VARCHAR(2),
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_path VARCHAR(255),
    audio_path VARCHAR(255),
    FOREIGN KEY (exhibit_id) REFERENCES exhibits(id),
    FOREIGN KEY (language_id) REFERENCES languages(id)
);

CREATE TABLE visit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    access_code VARCHAR(10),
    exhibit_id VARCHAR(10),
    language_id VARCHAR(2),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (access_code) REFERENCES access_codes(code),
    FOREIGN KEY (exhibit_id) REFERENCES exhibits(id),
    FOREIGN KEY (language_id) REFERENCES languages(id)
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default languages
INSERT INTO languages (id, name) VALUES
('en', 'English'),
('fr', 'Français'),
('es', 'Español'),
('ar', 'العربية');

-- Create default admin user with password: Admin123!
DELETE FROM admins WHERE username = 'admin';
INSERT INTO admins (username, password_hash) VALUES
('admin', '$2y$10$YourNewSecureHashHere');