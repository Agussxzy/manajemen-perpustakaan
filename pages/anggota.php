<?php
include 'config/database.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM anggota WHERE nama LIKE '%$search%' OR nomor_anggota LIKE '%$search%' ORDER BY id_anggota DESC";
$result = mysqli_query($conn, $query);
?>

<div class="d-flex justify-content-between align-items-start mb-4">
    <div class="page-header mb-0">
        <h2>Anggota</h2>
        <p>Kelola data anggota perpustakaan</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-meta-ghost btn-meta-sm" data-bs-toggle="modal" data-bs-target="#modalImportAnggota">
            <i class="bi bi-upload me-1"></i> Import Excel
        </button>
        <button type="button" class="btn btn-meta-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAnggota">
            <i class="bi bi-plus-lg me-1"></i> Tambah Anggota
        </button>
    </div>
</div>

<!-- Search -->
<div class="meta-card-compact mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <input type="hidden" name="page" value="anggota">
        <div class="col">
            <label class="form-label">Cari</label>
            <input type="text" name="search" class="form-control" placeholder="Nama atau nomor anggota..." value="<?= htmlspecialchars($search) ?>">
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
    <div class="meta-card-header">Daftar Anggota</div>
    <div class="table-responsive">
        <table class="meta-table">
            <thead>
                <tr>
                    <th>No. Anggota</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th style="width:180px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td style="font-weight:700;"><?= htmlspecialchars($row['nomor_anggota']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['alamat']) ?></td>
                            <td><?= htmlspecialchars($row['no_telepon']) ?></td>
                            <td>
                                <button class="btn btn-meta-ghost btn-meta-sm me-1" onclick="editAnggota(<?= $row['id_anggota'] ?>, '<?= addslashes($row['nomor_anggota']) ?>', '<?= addslashes($row['nama']) ?>', '<?= addslashes($row['alamat']) ?>', '<?= addslashes($row['no_telepon']) ?>')">
                                    Edit
                                </button>
                                <a href="proses/hapus_anggota.php?id=<?= $row['id_anggota'] ?>" class="btn btn-meta-sm" style="background:transparent;color:var(--critical);border:2px solid rgba(239,68,68,0.2);border-radius:100px;padding:8px 16px;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:700;letter-spacing:-0.14px;" onclick="return confirm('Yakin hapus anggota ini?')">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;padding:40px 16px;color:var(--stone);">Tidak ada data anggota</td>
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
<div class="modal fade" id="modalImportAnggota" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="proses/import_anggota.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Import Anggota dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File Excel (.xlsx)</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
                    </div>
                    <div style="font-size:13px;color:var(--stone);margin-bottom:12px;">
                        <strong>Format kolom:</strong> A = Nomor Anggota, B = Nama, C = Alamat, D = No. Telepon<br>
                        Baris pertama akan otomatis dilewati jika terdeteksi sebagai header.
                    </div>
                    <div style="background:#f1f4f7;border-radius:12px;padding:12px;overflow-x:auto;">
                        <svg viewBox="0 0 520 130" style="width:100%;max-width:520px;display:block;font-family:'Montserrat',sans-serif;">
                            <rect x="0" y="0" width="520" height="130" fill="#ffffff" rx="6"/>
                            <line x1="130" y1="0" x2="130" y2="130" stroke="#ced0d4" stroke-width="1"/>
                            <line x1="260" y1="0" x2="260" y2="130" stroke="#ced0d4" stroke-width="1"/>
                            <line x1="390" y1="0" x2="390" y2="130" stroke="#ced0d4" stroke-width="1"/>
                            <line x1="0" y1="36" x2="520" y2="36" stroke="#ced0d4" stroke-width="1"/>
                            <rect x="0" y="0" width="520" height="36" fill="#e8f0fe" rx="6"/>
                            <rect x="0" y="30" width="520" height="6" fill="#e8f0fe"/>
                            <text x="65" y="23" text-anchor="middle" font-size="12" font-weight="700" fill="#0064e0">A</text>
                            <text x="195" y="23" text-anchor="middle" font-size="12" font-weight="700" fill="#0064e0">B</text>
                            <text x="325" y="23" text-anchor="middle" font-size="12" font-weight="700" fill="#0064e0">C</text>
                            <text x="455" y="23" text-anchor="middle" font-size="12" font-weight="700" fill="#0064e0">D</text>
                            <text x="65" y="58" text-anchor="middle" font-size="11" font-weight="600" fill="#1c1e21">AGT-001</text>
                            <text x="195" y="58" text-anchor="middle" font-size="11" font-weight="600" fill="#1c1e21">Ali</text>
                            <text x="325" y="80" text-anchor="middle" font-size="11" fill="#8595a4">Jakarta</text>
                            <text x="455" y="80" text-anchor="middle" font-size="11" fill="#8595a4">0812-...</text>
                            <text x="65" y="80" text-anchor="middle" font-size="11" font-weight="600" fill="#1c1e21">AGT-002</text>
                            <text x="195" y="80" text-anchor="middle" font-size="11" font-weight="600" fill="#1c1e21">Budi</text>
                            <text x="65" y="102" text-anchor="middle" font-size="11" font-weight="600" fill="#1c1e21">AGT-003</text>
                            <text x="195" y="102" text-anchor="middle" font-size="11" font-weight="600" fill="#1c1e21">Citra</text>
                            <text x="325" y="102" text-anchor="middle" font-size="11" fill="#8595a4">Bandung</text>
                            <text x="455" y="102" text-anchor="middle" font-size="11" fill="#8595a4">0821-...</text>
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

<!-- Modal Tambah Anggota -->
<div class="modal fade" id="modalTambahAnggota" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="proses/tambah_anggota.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Anggota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nomor Anggota</label>
                        <input type="text" name="nomor_anggota" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="no_telepon" class="form-control" required>
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

<!-- Modal Edit Anggota -->
<div class="modal fade" id="modalEditAnggota" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="proses/edit_anggota.php" method="POST">
                <input type="hidden" name="id_anggota" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Anggota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nomor Anggota</label>
                        <input type="text" name="nomor_anggota" id="edit_nomor_anggota" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" id="edit_alamat" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="no_telepon" id="edit_no_telepon" class="form-control" required>
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
    function editAnggota(id, nomor_anggota, nama, alamat, no_telepon) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nomor_anggota').value = nomor_anggota;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_alamat').value = alamat;
        document.getElementById('edit_no_telepon').value = no_telepon;
        new bootstrap.Modal(document.getElementById('modalEditAnggota')).show();
    }
</script>
