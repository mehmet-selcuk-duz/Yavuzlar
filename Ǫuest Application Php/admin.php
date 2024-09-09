<?php
$db = new PDO('sqlite:sorular.db');

$db->exec("CREATE TABLE IF NOT EXISTS sorular (id INTEGER PRIMARY KEY, soru TEXT, zorluk TEXT, sik1 TEXT, sik2 TEXT, sik3 TEXT, sik4 TEXT, dogruSik TEXT)");

$duzenleSoru = null;
if (isset($_POST['duzenle'])) {
    $soruid = $_POST['soruid'];
    $stmt = $db->prepare("SELECT * FROM sorular WHERE id = ?");
    $stmt->execute([$soruid]);
    $duzenleSoru = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_POST['kaydet'])) {
    $soru = $_POST['soru'];
    $zorluk = $_POST['zorluk'];
    $sik1 = $_POST['sik1'];
    $sik2 = $_POST['sik2'];
    $sik3 = $_POST['sik3'];
    $sik4 = $_POST['sik4'];
    $dogruSik = $_POST['dogruSik'];

    if (!empty($_POST['soruid'])) {
        $soruid = $_POST['soruid'];
        $stmt = $db->prepare("UPDATE sorular SET soru = ?, zorluk = ?, sik1 = ?, sik2 = ?, sik3 = ?, sik4 = ?, dogruSik = ? WHERE id = ?");
        $stmt->execute([$soru, $zorluk, $sik1, $sik2, $sik3, $sik4, $dogruSik, $soruid]);
    } else {
        $stmt = $db->prepare("INSERT INTO sorular (soru, zorluk, sik1, sik2, sik3, sik4, dogruSik) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$soru, $zorluk, $sik1, $sik2, $sik3, $sik4, $dogruSik]);
    }

    header("Location: admin.php");
    exit();
}

if (isset($_POST['sil'])) {
    $soruid = $_POST['soruid'];
    $stmt = $db->prepare("DELETE FROM sorular WHERE id = ?");
    $stmt->execute([$soruid]);
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ǫuest Application - Admin Paneli</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Admin Paneli</h1>
    <button id="geriBtn" onclick="window.location.href='index.php'">Geri</button>

    <div>
        <h2>Soru Ekleme / Düzenleme</h2>
        <form action="admin.php" method="POST">
            <input type="hidden" name="soruid" id="soruid" value="<?php if ($duzenleSoru) { echo $duzenleSoru['id']; } ?>">
            <label>Soru:</label><br>
            <input type="text" name="soru" id="soru" value="<?php if ($duzenleSoru) { echo $duzenleSoru['soru']; } ?>" required><br>
            <label>Zorluk Derecesi:</label><br>
            <select name="zorluk" id="zorluk">
                <option value="kolay" <?php if ($duzenleSoru && $duzenleSoru['zorluk'] == 'kolay') echo 'selected'; ?>>Kolay</option>
                <option value="orta" <?php if ($duzenleSoru && $duzenleSoru['zorluk'] == 'orta') echo 'selected'; ?>>Orta</option>
                <option value="zor" <?php if ($duzenleSoru && $duzenleSoru['zorluk'] == 'zor') echo 'selected'; ?>>Zor</option>
            </select><br>
            <label>Şıklar:</label><br>
            <input type="text" name="sik1" id="sik1" value="<?php if ($duzenleSoru) { echo $duzenleSoru['sik1']; } ?>" required><br>
            <input type="text" name="sik2" id="sik2" value="<?php if ($duzenleSoru) { echo $duzenleSoru['sik2']; } ?>" required><br>
            <input type="text" name="sik3" id="sik3" value="<?php if ($duzenleSoru) { echo $duzenleSoru['sik3']; } ?>" required><br>
            <input type="text" name="sik4" id="sik4" value="<?php if ($duzenleSoru) { echo $duzenleSoru['sik4']; } ?>" required><br>
            <label>Doğru Şık:</label><br>
            <input type="text" name="dogruSik" id="dogruSik" value="<?php if ($duzenleSoru) { echo $duzenleSoru['dogruSik']; } ?>" required><br>
            <button type="submit" name="kaydet">Kaydet</button>
        </form>

        <h2>Eklenen Sorular</h2>
        <input type="text" id="soruArama" placeholder="Soru Ara..." oninput="aramaYap()"><br>
        <div id="soruListesi">
            <?php
            $stmt = $db->query("SELECT * FROM sorular");
            $sorular = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($sorular as $soru) {
                echo "<div class='soru-item'>";
                echo "<p><strong>Soru:</strong> {$soru['soru']}</p>";
                echo "<p><strong>Zorluk:</strong> {$soru['zorluk']}</p>";
                echo "<p><strong>Şıklar:</strong> {$soru['sik1']}, {$soru['sik2']}, {$soru['sik3']}, {$soru['sik4']}</p>";
                echo "<p><strong>Doğru Şık:</strong> {$soru['dogruSik']}</p>";
                echo "<form method='POST' action='admin.php' style='display:inline;'>
                        <input type='hidden' name='soruid' value='{$soru['id']}'>
                        <button type='submit' name='duzenle'>Düzenle</button>
                      </form>";
                echo "<form method='POST' action='admin.php' style='display:inline;'>
                        <input type='hidden' name='soruid' value='{$soru['id']}'>
                        <button type='submit' name='sil'>Sil</button>
                      </form>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
</body>
    <script>
        function aramaYap() {
            const aramaTerimi = document.getElementById('soruArama').value.toLowerCase();
            const soruItems = document.querySelectorAll('.soru-item');

            soruItems.forEach(item => {
                const soruText = item.querySelector('p').textContent.toLowerCase();
                if (soruText.includes(aramaTerimi) || aramaTerimi === '') {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
</html>
