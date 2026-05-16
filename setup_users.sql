-- Tabel users untuk sistem login
CREATE TABLE IF NOT EXISTS users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL
);

-- User default: admin / admin123
INSERT INTO users (username, password, nama_lengkap) VALUES
('admin', '$2y$10$CHwiRmz.092uvVn8VHol0uLk/zOQSpebfk8n0CXzSGPVx/7bxymdK', 'Administrator');
