<?php
// ==========================================
// DB CONNECTION
// ==========================================
$host = "localhost";
$user = "root"; // Default XAMPP user
$pass = "root";     // Default XAMPP password
$db   = "toko_kelontong";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// ==========================================
// HANDLE ACTIONS (CRUD)
// ==========================================
$id = $kode_produk = $nama_produk = $harga = $stok = "";
$update_mode = false;

// 1. SAVE OR UPDATE DATA
if (isset($_POST['save'])) {
    $kode_produk = $_POST['kode_produk'];
    $nama_produk = $_POST['nama_produk'];
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];

    if (!empty($_POST['id'])) {
        // Update existing item
        $id = $_POST['id'];
        $query = "UPDATE produk SET kode_produk='$kode_produk', nama_produk='$nama_produk', harga='$harga', stok='$stok' WHERE id=$id";
    } else {
        // Insert new item
        $query = "INSERT INTO produk (kode_produk, nama_produk, harga, stok) VALUES ('$kode_produk', '$nama_produk', '$harga', '$stok')";
    }
    
    $conn->query($query);
    header("Location: index.php");
    exit();
}

// 2. DELETE DATA
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM produk WHERE id=$id");
    header("Location: index.php");
    exit();
}

// 3. EDIT DATA (Fetch item details to populate the form)
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $update_mode = true;
    $result = $conn->query("SELECT * FROM produk WHERE id=$id");
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $kode_produk = $row['kode_produk'];
        $nama_produk = $row['nama_produk'];
        $harga       = $row['harga'];
        $stok        = $row['stok'];
    }
}

// FETCH ALL DATA TO DISPLAY IN THE TABLE
$products = $conn->query("SELECT * FROM produk ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koperasi Sekolah / Toko Kelontong</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f7f6; }
        h2 { color: #333; }
        .container { display: flex; gap: 30px; }
        .form-container, .table-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .form-container { width: 30%; height: fit-content; }
        .table-container { width: 70%; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"] { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
        button.btn-update { background-color: #2196F3; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn-action { text-decoration: none; padding: 5px 10px; color: white; border-radius: 3px; font-size: 12px; }
        .btn-edit { background-color: #FFA500; }
        .btn-delete { background-color: #f44336; margin-left: 5px; }
        .btn-cancel { background-color: #bbb; color: #333; text-decoration: none; padding: 8px 12px; border-radius: 4px; font-size: 14px; margin-left: 5px; }
    </style>
</head>
<body>

    <h2>🏪 Kelola Data Barang Jualan (Koperasi Sekolah)</h2>
    <hr><br>

    <div class="container">
        <div class="form-container">
            <h3><?php echo $update_mode ? "Ubah Produk" : "Tambah Produk Baru"; ?></h3>
            <form action="index.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                
                <div class="form-group">
                    <label>Kode Produk</label>
                    <input type="text" name="kode_produk" value="<?php echo $kode_produk; ?>" required placeholder="Contoh: PRD001">
                </div>
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_produk" value="<?php echo $nama_produk; ?>" required placeholder="Contoh: Buku Tulis">
                </div>
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" value="<?php echo $harga; ?>" required placeholder="Contoh: 5000">
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" value="<?php echo $stok; ?>" required placeholder="Contoh: 100">
                </div>
                
                <button type="submit" name="save" class="<?php echo $update_mode ? 'btn-update' : ''; ?>">
                    <?php echo $update_mode ? "Update Data" : "Simpan Data"; ?>
                </button>
                <?php if ($update_mode): ?>
                    <a href="index.php" class="btn-cancel">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-container">
            <h3>Daftar Stok Barang</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products->num_rows > 0): ?>
                        <?php while($row = $products->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['kode_produk']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                                <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo $row['stok']; ?> pcs</td>
                                <td>
                                    <a href="index.php?edit=<?php echo $row['id']; ?>" class="btn-action btn-edit">Edit</a>
                                    <a href="index.php?delete=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #888;">Belum ada data barang jualan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>