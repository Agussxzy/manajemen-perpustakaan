<?php
include 'config/database.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM buku WHERE judul LIKE '%$search%' OR pengarang LIKE '%$search%' ORDER BY id_buku DESC";
$result = mysqli_query($conn, $query);
?>

<div class="d-flex justify-content-between align-items-start mb-4">
    <div class="page-header mb-0">
        <h2>Buku</h2>
        <p>Kelola koleksi buku perpustakaan</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-meta-ghost btn-meta-sm" data-bs-toggle="modal" data-bs-target="#modalImportBuku">
            <i class="bi bi-upload me-1"></i> Import Excel
        </button>
        <button type="button" class="btn btn-meta-primary" data-bs-toggle="modal" data-bs-target="#modalTambahBuku">
            <i class="bi bi-plus-lg me-1"></i> Tambah Buku
        </button>
    </div>
</div>

<!-- Search -->
<div class="meta-card-compact mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <input type="hidden" name="page" value="buku">
        <div class="col">
            <label class="form-label">Cari</label>
            <input type="text" name="search" class="form-control" placeholder="Judul atau pengarang..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-meta-primary">
                <i class="bi bi-search me-1"></i> Cari
            </button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="meta-card">
    <div class="meta-card-header">Daftar Buku</div>
    <div class="table-responsive">
        <table class="meta-table">
            <thead>
                <tr>
                    <th style="width:50px">ID</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                    <th style="width:80px">Tahun</th>
                    <th style="width:80px">Stok</th>
                    <th style="width:180px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td style="color:var(--steel);"><?= $row['id_buku'] ?></td>
                            <td style="font-weight:700;"><?= htmlspecialchars($row['judul']) ?></td>
                            <td><?= htmlspecialchars($row['pengarang']) ?></td>
                            <td><?= htmlspecialchars($row['penerbit']) ?></td>
                            <td><?= $row['tahun_terbit'] ?></td>
                            <td>
                                <?php if ($row['stok'] > 0): ?>
                                    <span class="badge-meta badge-success"><?= $row['stok'] ?></span>
                                <?php else: ?>
                                    <span class="badge-meta badge-critical">Habis</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-meta-ghost btn-meta-sm me-1" onclick="editBuku(<?= $row['id_buku'] ?>, '<?= addslashes($row['judul']) ?>', '<?= addslashes($row['pengarang']) ?>', '<?= addslashes($row['penerbit']) ?>', <?= $row['tahun_terbit'] ?>, <?= $row['stok'] ?>)">
                                    Edit
                                </button>
                                <a href="proses/hapus_buku.php?id=<?= $row['id_buku'] ?>" class="btn btn-meta-sm" style="background:transparent;color:var(--critical);border:2px solid rgba(239,68,68,0.2);border-radius:100px;padding:8px 16px;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:700;letter-spacing:-0.14px;" onclick="return confirm('Yakin hapus buku ini?')">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px 16px;color:var(--stone);">Tidak ada data buku</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (isset($_GET['status'])): ?>
    <div class="alert alert-meta alert-meta-<?= $_GET['status'] === 'success' ? 'success' : ($_GET['status'] === 'warning' ? 'warning' : 'danger') ?> alert-dismissible fade show" role="alert">
        <i class="bi <?= $_GET['status'] === 'success' ? 'bi-check-circle' : ($_GET['status'] === 'warning' ? 'bi-exclamation-triangle' : 'bi-x-circle') ?> me-2"></i>
        <?= htmlspecialchars(urldecode($_GET['msg'] ?? '')) ?>
        <?php if (isset($_GET['detail'])): ?>
            <small class="d-block mt-1" style="opacity:0.8;"><?= htmlspecialchars(urldecode($_GET['detail'])) ?></small>
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Modal Import Excel -->
<div class="modal fade" id="modalImportBuku" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="proses/import_buku.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Import Buku dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File Excel (.xlsx)</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
                    </div>
                    <div style="font-size:13px;color:var(--stone);margin-bottom:12px;">
                        <strong>Format kolom:</strong> A = Judul, B = Pengarang, C = Penerbit, D = Tahun, E = Stok<br>
                        Baris pertama akan otomatis dilewati jika terdeteksi sebagai header.
                    </div>
                    <div style="background:#f1f4f7;border-radius:12px;padding:12px;overflow-x:auto;">
                        <svg viewBox="0 0 520 130" style="width:100%;max-width:520px;display:block;font-family:'Montserrat',sans-serif;">
                            <rect x="0" y="0" width="520" height="130" fill="#ffffff" rx="6"/>
                            <line x1="105" y1="0" x2="105" y2="130" stroke="#ced0d4" stroke-width="1"/>
                            <line x1="210" y1="0" x2="210" y2="130" stroke="#ced0d4" stroke-width="1"/>
                            <line x1="315" y1="0" x2="315" y2="130" stroke="#ced0d4" stroke-width="1"/>
                            <line x1="420" y1="0" x2="420" y2="130" stroke="#ced0d4" stroke-width="1"/>
                            <line x1="0" y1="36" x2="520" y2="36" stroke="#ced0d4" stroke-width="1"/>
                            <rect x="0" y="0" width="520" height="36" fill="#e8f0fe" rx="6"/>
                            <rect x="0" y="30" width="520" height="6" fill="#e8f0fe"/>
                            <text x="52" y="23" text-anchor="middle" font-size="11" font-weight="700" fill="#0064e0">A</text>
                            <text x="157" y="23" text-anchor="middle" font-size="11" font-weight="700" fill="#0064e0">B</text>
                            <text x="262" y="23" text-anchor="middle" font-size="11" font-weight="700" fill="#0064e0">C</text>
                            <text x="367" y="23" text-anchor="middle" font-size="11" font-weight="700" fill="#0064e0">D</text>
                            <text x="470" y="23" text-anchor="middle" font-size="11" font-weight="700" fill="#0064e0">E</text>
                            <text x="52" y="58" text-anchor="middle" font-size="10" fill="#1c1e21">Pemrograman</text>
                            <text x="157" y="58" text-anchor="middle" font-size="10" fill="#1c1e21">Andi</text>
                            <text x="262" y="58" text-anchor="middle" font-size="10" fill="#8595a4">Gramedia</text>
                            <text x="367" y="58" text-anchor="middle" font-size="10" fill="#8595a4">2023</text>
                            <text x="470" y="58" text-anchor="middle" font-size="10" fill="#8595a4">5</text>
                            <text x="52" y="80" text-anchor="middle" font-size="10" fill="#1c1e21">Database</text>
                            <text x="157" y="80" text-anchor="middle" font-size="10" fill="#1c1e21">Budi</text>
                            <text x="262" y="80" text-anchor="middle" font-size="10" fill="#8595a4">Informatika</text>
                            <text x="367" y="80" text-anchor="middle" font-size="10" fill="#8595a4">2022</text>
                            <text x="470" y="80" text-anchor="middle" font-size="10" fill="#8595a4">3</text>
                            <text x="52" y="102" text-anchor="middle" font-size="10" fill="#1c1e21">Jaringan</text>
                            <text x="157" y="102" text-anchor="middle" font-size="10" fill="#1c1e21">Citra</text>
                            <text x="262" y="102" text-anchor="middle" font-size="10" fill="#8595a4">Erlangga</text>
                            <text x="367" y="102" text-anchor="middle" font-size="10" fill="#8595a4">2024</text>
                            <text x="470" y="102" text-anchor="middle" font-size="10" fill="#8595a4">2</text>
                        </svg>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-meta-ghost btn-meta-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-meta-cobalt btn-meta-sm">
                        <i class="bi bi-upload me-1"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Buku -->
<div class="modal fade" id="modalTambahBuku" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="proses/tambah_buku.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pengarang</label>
                        <input type="text" name="pengarang" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Penerbit</label>
                        <input type="text" name="penerbit" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun Terbit</label>
                        <input type="number" name="tahun_terbit" class="form-control" min="1900" max="2026">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-control" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-meta-ghost btn-meta-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-meta-cobalt btn-meta-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Buku -->
<div class="modal fade" id="modalEditBuku" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="proses/edit_buku.php" method="POST">
                <input type="hidden" name="id_buku" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="judul" id="edit_judul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pengarang</label>
                        <input type="text" name="pengarang" id="edit_pengarang" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Penerbit</label>
                        <input type="text" name="penerbit" id="edit_penerbit" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun Terbit</label>
                        <input type="number" name="tahun_terbit" id="edit_tahun" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" id="edit_stok" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-meta-ghost btn-meta-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-meta-cobalt btn-meta-sm">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editBuku(id, judul, pengarang, penerbit, tahun, stok) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_judul').value = judul;
        document.getElementById('edit_pengarang').value = pengarang;
        document.getElementById('edit_penerbit').value = penerbit;
        document.getElementById('edit_tahun').value = tahun;
        document.getElementById('edit_stok').value = stok;
        new bootstrap.Modal(document.getElementById('modalEditBuku')).show();
    }
</script>
