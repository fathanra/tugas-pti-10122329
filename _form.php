<?php /* Form bersama untuk tambah.php & edit.php. Butuh variabel: $data, $error */ ?>
<?php if (isset($error['umum'])): ?>
    <div class="gagal"><?= e($error['umum']) ?></div>
<?php endif; ?>

<form method="POST" novalidate>
    <p class="baris">
        <label for="nama"><?= e(t('nama')) ?>:</label><br>
        <input type="text" id="nama" name="nama_lengkap" value="<?= e($data['nama_lengkap']) ?>">
        <?php tampil_error($error, 'nama_lengkap'); ?>
    </p>

    <p class="baris">
        <?= e(t('jk')) ?>:
        <?php foreach (['Laki-laki', 'Perempuan'] as $opsi): ?>
            <label>
                <input type="radio" name="jenis_kelamin" value="<?= e($opsi) ?>"
                    <?= $data['jenis_kelamin'] === $opsi ? 'checked' : '' ?>>
                <?= e(t('jk_' . $opsi)) ?>
            </label>
        <?php endforeach; ?>
        <?php tampil_error($error, 'jenis_kelamin'); ?>
    </p>

    <p class="baris">
        <label for="kota"><?= e(t('kota')) ?>:</label><br>
        <select id="kota" name="kota_asal">
            <option value=""><?= e(t('pilih')) ?></option>
            <?php foreach (DAFTAR_KOTA as $kota): ?>
                <option value="<?= e($kota) ?>" <?= $data['kota_asal'] === $kota ? 'selected' : '' ?>>
                    <?= e($kota) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php tampil_error($error, 'kota_asal'); ?>
    </p>

    <p class="baris">
        <?= e(t('hobi')) ?>:
        <?php foreach (DAFTAR_HOBI as $opsi): ?>
            <label>
                <input type="checkbox" name="hobi[]" value="<?= e($opsi) ?>"
                    <?= in_array($opsi, $data['hobi'], true) ? 'checked' : '' ?>>
                <?= e(t('hobi_' . $opsi)) ?>
            </label>
        <?php endforeach; ?>
        <?php tampil_error($error, 'hobi'); ?>
    </p>

    <p class="baris">
        <label for="tentang"><?= e(t('tentang')) ?>:</label><br>
        <textarea id="tentang" name="tentang_saya" rows="4"><?= e($data['tentang_saya']) ?></textarea>
        <?php tampil_error($error, 'tentang_saya'); ?>
    </p>

    <button type="submit" class="btn"><?= e(t('simpan')) ?></button>
    <a href="index.php" class="btn abu"><?= e(t('batal')) ?></a>
</form>