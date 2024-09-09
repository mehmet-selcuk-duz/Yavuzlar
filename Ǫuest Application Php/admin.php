<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$db = new PDO('sqlite:veriler.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $soru = $_POST['soru'];
    $zorluk = $_POST['zorluk'];
    $sik1 = $_POST['sik1'];
    $sik2 = $_POST['sik2'];
    $sik3 = $_POST['sik3'];
    $sik4 = $_POST['sik4'];
    $dogruSik = $_POST['dogruSik'];
    $id = isset($_POST['soruid']) ? $_POST['soruid'] : null;

    if ($id) {
        $stmt = $db->prepare("UPDATE sorular SET soru = :soru, zorluk = :zorluk, sik1 = :sik1, sik2 = :sik2, sik3 = :sik3, sik4 = :sik4, dogruSik = :dogruSik WHERE id = :id");
        $stmt->bindParam(':id', $id);
    } else {
        $stmt = $db->prepare("INSERT INTO sorular (soru, zorluk, sik1, sik2, sik3, sik4, dogruSik) VALUES (:soru, :zorluk, :sik1, :sik2, :sik3, :sik4, :dogruSik)");
    }

    $stmt->bindParam(':soru', $soru);
    $stmt->bindParam(':zorluk', $zorluk);
    $stmt->bindParam(':sik1', $sik1);
    $stmt->bindParam(':sik2', $sik2);
    $stmt->bindParam(':sik3', $sik3);
    $stmt->bindParam(':sik4', $sik4);
    $stmt->bindParam(':dogruSik', $dogruSik);
    $stmt->execute();
    header('Location: admin.php');
    exit();
}

if (isset($_GET['action'])) {
    if ($_GET['action'] === 'edit') {
        $id = $_GET['id'];
        $stmt = $db->prepare("SELECT * FROM sorular WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $editSoru = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif ($_GET['action'] === 'delete') {
        $id = $_GET['id'];
        $stmt = $db->prepare("DELETE FROM sorular WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header('Location: admin.php');
        exit();
    }
}

$sorular = $db->query("SELECT * FROM sorular")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Admin Paneli</h1>
    <button onclick="window.location.href='login.php'">Çıkış Yap</button>
    <br><br>
    
    <h2><?php echo isset($editSoru) ? 'Soru Düzenleme' : 'Soru Ekleme'; ?></h2>
    <form method="POST" action="admin.php">
        <input type="hidden" name="soruid" value="<?php echo isset($editSoru['id']) ? htmlspecialchars($editSoru['id']) : ''; ?>">
        <label>Soru:</label><br>
        <input type="text" name="soru" value="<?php echo isset($editSoru['soru']) ? htmlspecialchars($editSoru['soru']) : ''; ?>" required><br>
        <label>Zorluk Derecesi:</label><br>
        <select name="zorluk">
            <option value="kolay" <?php echo (isset($editSoru['zorluk']) && $editSoru['zorluk'] === 'kolay') ? 'selected' : ''; ?>>Kolay</option>
            <option value="orta" <?php echo (isset($editSoru['zorluk']) && $editSoru['zorluk'] === 'orta') ? 'selected' : ''; ?>>Orta</option>
            <option value="zor" <?php echo (isset($editSoru['zorluk']) && $editSoru['zorluk'] === 'zor') ? 'selected' : ''; ?>>Zor</option>
        </select><br>
        <label>Şıklar:</label><br>
        <input type="text" name="sik1" value="<?php echo isset($editSoru['sik1']) ? htmlspecialchars($editSoru['sik1']) : ''; ?>" required><br>
        <input type="text" name="sik2" value="<?php echo isset($editSoru['sik2']) ? htmlspecialchars($editSoru['sik2']) : ''; ?>" required><br>
        <input type="text" name="sik3" value="<?php echo isset($editSoru['sik3']) ? htmlspecialchars($editSoru['sik3']) : ''; ?>" required><br>
        <input type="text" name="sik4" value="<?php echo isset($editSoru['sik4']) ? htmlspecialchars($editSoru['sik4']) : ''; ?>" required><br>
        <label>Doğru Şık:</label><br>
        <input type="text" name="dogruSik" value="<?php echo isset($editSoru['dogruSik']) ? htmlspecialchars($editSoru['dogruSik']) : ''; ?>" required><br>
        <button type="submit"><?php echo isset($editSoru) ? 'Güncelle' : 'Kaydet'; ?></button>
    </form>

    <h2>Eklenen Sorular</h2>
    <input type="text" id="soruArama" placeholder="Soru Ara..."><br>
    <div id="soruListesi">
        <?php foreach ($sorular as $soru) : ?>
            <div class="soru-item">
                <p><strong>Soru:</strong> <?php echo htmlspecialchars($soru['soru']); ?></p>
                <p><strong>Zorluk:</strong> <?php echo htmlspecialchars($soru['zorluk']); ?></p>
                <p><strong>Şıklar:</strong> <?php echo htmlspecialchars($soru['sik1']) . ', ' . htmlspecialchars($soru['sik2']) . ', ' . htmlspecialchars($soru['sik3']) . ', ' . htmlspecialchars($soru['sik4']); ?></p>
                <p><strong>Doğru Şık:</strong> <?php echo htmlspecialchars($soru['dogruSik']); ?></p>
                <a href="admin.php?action=edit&id=<?php echo $soru['id']; ?>">Düzenle</a>
                <a href="admin.php?action=delete&id=<?php echo $soru['id']; ?>">Sil</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
<script>
    document.getElementById('soruArama').addEventListener('input', function() {
        const aranan = this.value.toLowerCase();
        document.querySelectorAll('.soru-item').forEach(item => {
            const soruText = item.querySelector('p').textContent.toLowerCase();
            item.style.display = soruText.includes(aranan) ? '' : 'none';
        });
    });

    <?php if (isset($editSoru)) : ?>
        document.querySelector('form').scrollIntoView();
    <?php endif; ?>
</script>
</html>
