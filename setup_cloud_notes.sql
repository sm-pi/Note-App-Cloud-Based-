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
    email VARCHAR(150) NOT NULL,
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

-- 6. Insert Starter Data
INSERT INTO roles (role_name) VALUES ('free_user'), ('premium_user'), ('admin');

-- Expanded User List (11 Users)
INSERT INTO users (name, email, password, role_id) VALUES 
('admin', 'admin@cloudnotes.local', 'supersecret', 3),
('samin', 'samin@cloudnotes.local', '@321samin', 1),
('sazzad', 'sazzad@cloudnotes.local', '@231sazzad', 1),
('victim', 'victim@cloudnotes.local', '1234', 1),
('attacker', 'attacker@cloudnotes.local', '1234', 1),
('alice', 'alice@cloudnotes.local', 'alice_pass', 2),
('bob', 'bob@cloudnotes.local', 'bob_pass', 1),
('charlie', 'charlie@cloudnotes.local', 'charlie_pass', 1),
('dave', 'dave@cloudnotes.local', 'dave_pass', 2),
('eve', 'eve@cloudnotes.local', 'eve_pass', 1),
('mallory', 'mallory@cloudnotes.local', 'mal_pass', 1);

-- Expanded Folders
INSERT INTO folders (user_id, name) VALUES 
(2, 'Samin''s Homework'), 
(3, 'Sazzad''s Hack Logs'),
(6, 'Alice''s Project Files'),
(7, 'Bob''s Meeting Notes');

-- Expanded Notes
INSERT INTO notes (user_id, folder_id, title, content) VALUES 
(2, 1, 'Math Homework', 'Don''t forget to do the calculus.'),
(3, 2, 'Targets', 'We need to test the login bypass.'),
(6, 3, 'Project Alpha', 'Drafting the initial requirements for the new sprint.'),
(7, 4, 'Weekly Sync', 'Discussed the Q3 roadmap and team allocations.');
