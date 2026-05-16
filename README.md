# Manajemen Perpustakaan

Sistem informasi manajemen perpustakaan berbasis web (PHP + MySQL) dengan antarmuka Meta Design System.

## Fitur

- **Dashboard** — ringkasan jumlah buku, anggota, peminjaman aktif, dan pengembalian
- **Buku** — CRUD data buku + import dari Excel (.xlsx)
- **Anggota** — CRUD data anggota + import dari Excel (.xlsx)
- **Peminjaman** — transaksi peminjaman dengan pemilihan buku & anggota
- **Pengembalian** — proses pengembalian dan perhitungan denda
- **Laporan** — rekap peminjaman per periode, cetak PDF
- **Mode Gelap/Terang** — toggle tema yang disimpan di session
- **Import Excel** — import data anggota dan buku dari file `.xlsx`

## Persyaratan Sistem

- XAMPP (Apache + PHP 8.0+ + MySQL 5.7+)
- Browser modern (Chrome, Firefox, Edge)

## Cara Setup

1. **Clone repositori**
   ```bash
   git clone https://github.com/Agussxzy/manajemen-perpustakaan.git
   ```

2. **Letakkan di folder web server**
   - Copy folder `perpustakaan` ke `C:\xampp\htdocs\` (Windows) atau `/var/www/html/` (Linux)

3. **Buat database dan tabel**
   - Jalankan seluruh SQL di bawah ini di phpMyAdmin atau MySQL CLI:
     ```sql
     CREATE DATABASE IF NOT EXISTS perpustakaan CHARACTER SET utf8 COLLATE utf8_general_ci;
     USE perpustakaan;

     -- Tabel users untuk login
     CREATE TABLE IF NOT EXISTS users (
         id_user INT AUTO_INCREMENT PRIMARY KEY,
         username VARCHAR(50) UNIQUE NOT NULL,
         password VARCHAR(255) NOT NULL,
         nama_lengkap VARCHAR(100) NOT NULL
     );

     -- User default: admin / admin123
     INSERT INTO users (username, password, nama_lengkap) VALUES
     ('admin', '$2y$10$CHwiRmz.092uvVn8VHol0uLk/zOQSpebfk8n0CXzSGPVx/7bxymdK', 'Administrator');
     CREATE TABLE buku (
       id_buku INT AUTO_INCREMENT PRIMARY KEY,
       judul VARCHAR(100) NOT NULL,
       pengarang VARCHAR(100) NOT NULL,
       penerbit VARCHAR(100),
       tahun_terbit YEAR,
       stok INT DEFAULT 1,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );

     CREATE TABLE anggota (
       id_anggota INT AUTO_INCREMENT PRIMARY KEY,
       nomor_anggota VARCHAR(20) UNIQUE NOT NULL,
       nama VARCHAR(100) NOT NULL,
       alamat TEXT,
       no_telepon VARCHAR(15),
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );

     CREATE TABLE peminjaman (
       id_peminjaman INT AUTO_INCREMENT PRIMARY KEY,
       id_buku INT NOT NULL,
       id_anggota INT NOT NULL,
       tanggal_pinjam DATE NOT NULL,
       tanggal_jatuh_tempo DATE NOT NULL,
       tanggal_kembali DATE,
       status ENUM('dipinjam','dikembalikan') DEFAULT 'dipinjam',
       denda DECIMAL(10,2) DEFAULT 0,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (id_buku) REFERENCES buku(id_buku),
       FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota)
     );
     ```

4. **Akses aplikasi**
   - Buka `http://localhost/perpustakaan/`
   - Login dengan username: `admin`, password: `admin123`

## Konfigurasi Database

File `config/database.php`:

```php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'perpustakaan';
```

Sesuaikan jika menggunakan user/password MySQL yang berbeda.

## Format Import Excel (.xlsx)

### Anggota

| Kolom | Data |
|-------|------|
| A | Nomor Anggota |
| B | Nama |
| C | Alamat |
| D | No. Telepon |

### Buku

| Kolom | Data |
|-------|------|
| A | Judul |
| B | Pengarang |
| C | Penerbit |
| D | Tahun Terbit |
| E | Stok |

Baris pertama otomatis dilewati jika terdeteksi sebagai header.

## Teknologi

- **Backend:** PHP 8+, MySQL
- **Frontend:** Bootstrap 5, Bootstrap Icons
- **Font:** Montserrat (Google Fonts)
- **Excel Reader:** SimpleXLSX (single-file, no Composer)
