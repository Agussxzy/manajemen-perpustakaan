# AGENTS.md

## Project

PHP + MySQL digital library app (*perpustakaan*). Runs on XAMPP — no build step, no package manager beyond `npm install` for bootstrap-icons (already vendored in `assets/`).

## Run

1. Start Apache + MySQL in XAMPP
2. Create database `perpustakaan` (see DB schema below)
3. Open `http://localhost/perpustakaan/`

## Database

- Config: `config/database.php` — host `localhost`, user `root`, no password, db `perpustakaan`
- Timezone: `Asia/Jakarta`
- Tables (inferred from queries):
  - `users` — id_user, username (UNIQUE), password (hashed), nama_lengkap
  - `buku` — id_buku, judul, pengarang, penerbit, tahun_terbit, stok
  - `anggota` — id_anggota, nama, + other member fields
  - `peminjaman` — id_buku (FK), id_anggota (FK), tanggal_pinjam, tanggal_jatuh_tempo, status (`dipinjam` | `dikembalikan`)

No SQL migration or seed files exist in the repo. The database schema must be created manually. Run `setup_users.sql` to create the users table and default admin account (username: `admin`, password: `admin123`).

## Architecture

- **Entry point**: `index.php` — routes via `?page=` query param to `pages/*.php`
- **Auth**: `config/auth.php` — included at top of `index.php`, redirects to `login.php` if no session
- **Login**: `login.php` (standalone page) → `proses/login_action.php` (verify + set session) → `proses/logout.php` (destroy session)
- **Pages** (`pages/`): dashboard, buku, anggota, peminjaman, pengembalian, laporan
- **Actions** (`proses/`): CRUD handlers — `tambah_*`, `edit_*`, `hapus_*`, `simpan_*`, `proses_*`
- Each page includes `config/database.php` directly (relative path from root)
- Each action includes `../config/database.php` (relative from `proses/`)
- Assets are vendored: `assets/css/bootstrap.min.css`, `assets/js/bootstrap.bundle.min.js`, `assets/icons/bootstrap-icons.min.css`

## Important

- refrensi design @DESIGN.md
- **No SQL sanitization**: all queries use raw `$_POST`/`$_GET` interpolation. Do not introduce prepared statements unless explicitly asked — match existing pattern.
- **Auth**: session-based login via `config/auth.php`. Default user: `admin` / `admin123`.
- **No tests, lint, or CI**: verify changes by manual browser testing.
- Forms POST directly to `proses/*.php` scripts which redirect back to `index.php?page=...&status=...`.
- Auto-dismiss alerts after 3 seconds via inline JS in `index.php`.
