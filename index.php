<?php
require 'helper.php';
wajib_login();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// READ: ambil semua data
$hasil = mysqli_query($conn, 'SELECT * FROM pendaftar ORDER BY id DESC');

mulai_halaman(t('data_pendaftar'));
?>
<?php if ($flash): ?>
    <div class="ok"><?= e(t($flash)) ?></div>
<?php endif; ?>

<p><a href="tambah.php" class="btn">+ <?= e(t('tambah')) ?></a></p>

<table>
    <tr>
        <th><?= e(t('no')) ?></th>
        <th><?= e(t('nama')) ?></th>
        <th><?= e(t('jk')) ?></th>
        <th><?= e(t('kota')) ?></th>
        <th><?= e(t('hobi')) ?></th>
        <th><?= e(t('tentang')) ?></th>
        <th><?= e(t('aksi')) ?></th>
    </tr>

    <?php if (mysqli_num_rows($hasil) === 0): ?>
        <tr><td colspan="7"><?= e(t('kosong')) ?></td></tr>
    <?php endif; ?>

    <?php $no = 1; while ($row = mysqli_fetch_assoc($hasil)): ?>
        <?php $daftar_hobi = $row['hobi'] ? explode(',', $row['hobi']) : []; ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= e($row['nama_lengkap']) ?></td>
            <td><?= e(t('jk_' . $row['jenis_kelamin'])) ?></td>
            <td><?= e($row['kota_asal']) ?></td>
            <td><?= e(implode(', ', array_map(fn($h) => t('hobi_' . $h), $daftar_hobi))) ?></td>
            <td><?= nl2br(e($row['tentang_saya'])) ?></td>
            <td>
                <a href="edit.php?id=<?= (int) $row['id'] ?>" class="btn"><?= e(t('ubah')) ?></a>
                <form method="POST" action="hapus.php" class="inline"
                      onsubmit="return confirm(<?= e(json_encode(t('konfirmasi'))) ?>)">
                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                    <button type="submit" class="btn merah"><?= e(t('hapus')) ?></button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<?php akhiri_halaman(); ?>