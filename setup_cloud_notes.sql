-- 1. Setup the Database Environment
CREATE DATABASE IF NOT EXISTS cloud_notes;
USE cloud_notes;

-- 2. Clean Slate: Drop old tables in reverse order of their connections
DROP TABLE IF EXISTS shared_notes;
DROP TABLE IF EXISTS note_tags;
DROP TABLE IF EXISTS notes;
DROP TABLE IF EXISTS folders;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

-- 3. Create Independent Tables first (Roles and Tags)
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL
);

CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tag_name VARCHAR(50) NOT NULL UNIQUE
);

-- 4. Create Main Tables
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    role_id INT DEFAULT 1,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
);

CREATE TABLE folders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    folder_id INT,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (folder_id) REFERENCES folders(id) ON DELETE SET NULL
);

-- 5. Create Connection Tables (Mapping notes to tags and sharing notes)
CREATE TABLE note_tags (
    note_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (note_id, tag_id),
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE shared_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note_id INT NOT NULL,
    shared_with_user_id INT NOT NULL,
    permission_level VARCHAR(20) DEFAULT 'read',
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_with_user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 6. Insert Starter Data for your Lab
INSERT INTO roles (role_name) VALUES ('free_user'), ('premium_user'), ('admin');

INSERT INTO users (name, password, role_id) VALUES 
('admin', 'supersecret', 3),
('samin', '@321samin', 1),
('sazzad', '@231sazzad', 1);

INSERT INTO folders (user_id, name) VALUES 
(2, 'Samin''s Homework'), 
(3, 'Sazzad''s Hack Logs');

INSERT INTO notes (user_id, folder_id, title, content) VALUES 
(2, 1, 'Math Homework', 'Don''t forget to do the calculus.'),
(3, 2, 'Targets', 'We need to test the login bypass.');
