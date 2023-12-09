CREATE TABLE content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    summary TEXT,
    content MEDIUMTEXT
);
