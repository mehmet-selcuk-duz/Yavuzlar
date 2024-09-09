<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$db = new PDO('sqlite:veriler.db');
$sorular = $db->query("SELECT * FROM sorular")->fetchAll(PDO::FETCH_ASSOC);

$suankiSoruIndex = isset($_POST['suankiSoruIndex']) ? (int)$_POST['suankiSoruIndex'] : 0;
$dogruSayisi = isset($_POST['dogruSayisi']) ? (int)$_POST['dogruSayisi'] : 0;
$yanlisSayisi = isset($_POST['yanlisSayisi']) ? (int)$_POST['yanlisSayisi'] : 0;
$toplamPuan = isset($_POST['toplamPuan']) ? (int)$_POST['toplamPuan'] : 0;

$zorlukPuanlari = [
    'kolay' => 3,
    'orta' => 6,
    'zor' => 9
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cevap'])) {
    $cevap = $_POST['cevap'];
    $soru = $sorular[$suankiSoruIndex];
    $dogruMu = (int)$cevap === (int)$soru['dogruSik'];
    
    if ($dogruMu) {
        $dogruSayisi++;
        $toplamPuan += $zorlukPuanlari[$soru['zorluk']];
    } else {
        $yanlisSayisi++;
    }
    
    $suankiSoruIndex++;
    
    if ($suankiSoruIndex >= count($sorular)) {
        $suankiSoruIndex = -1;
    }
}

$soru = $suankiSoruIndex >= 0 ? $sorular[$suankiSoruIndex] : null;
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
    <h2>Soru Ekleme / Düzenleme</h2>
    <form method="POST" action="admin_islemleri.php">
        <input type="hidden" name="soruid" id="soruid">
        <label>Soru:</label><br>
        <input type="text" name="soru" id="soru" required><br>
        <label>Zorluk Derecesi:</label><br>
        <select name="zorluk" id="zorluk">
            <option value="kolay">Kolay</option>
            <option value="orta">Orta</option>
            <option value="zor">Zor</option>
        </select><br>
        <label>Şıklar:</label><br>
        <input type="text" name="sik1" id="sik1" required><br>
        <input type="text" name="sik2" id="sik2" required><br>
        <input type="text" name="sik3" id="sik3" required><br>
        <input type="text" name="sik4" id="sik4" required><br>
        <label>Doğru Şık:</label><br>
        <input type="text" name="dogruSik" id="dogruSik" required><br>
        <button type="submit">Kaydet</button>
    </form>

    <h2>Eklenen Sorular</h2>
    <input type="text" id="soruArama" placeholder="Soru Ara..."><br>
    <div id="soruListesi">
        <?php foreach ($sorular as $index => $soru) : ?>
            <div class="soru-item">
                <p><strong>Soru:</strong> <?php echo htmlspecialchars($soru['soru']); ?></p>
                <p><strong>Zorluk:</strong> <?php echo htmlspecialchars($soru['zorluk']); ?></p>
                <p><strong>Şıklar:</strong> <?php echo htmlspecialchars($soru['sik1']) . ', ' . htmlspecialchars($soru['sik2']) . ', ' . htmlspecialchars($soru['sik3']) . ', ' . htmlspecialchars($soru['sik4']); ?></p>
                <p><strong>Doğru Şık:</strong> <?php echo htmlspecialchars($soru['dogruSik']); ?></p>
                <a href="admin_process.php?action=edit&id=<?php echo $soru['id']; ?>">Düzenle</a>
                <a href="admin_process.php?action=delete&id=<?php echo $soru['id']; ?>">Sil</a>
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

    <?php if (isset($_GET['soruid'])) : ?>
        document.getElementById('soruid').value = <?php echo $_GET['soruid']; ?>;
        const sorular = <?php echo json_encode($sorular); ?>;
        const mevcutSoru = sorular.find(soru => soru.id === <?php echo $_GET['soruid']; ?>);
        document.getElementById('soru').value = mevcutSoru.soru;
        document.getElementById('zorluk').value = mevcutSoru.zorluk;
        document.getElementById('sik1').value = mevcutSoru.sik1;
        document.getElementById('sik2').value = mevcutSoru.sik2;
        document.getElementById('sik3').value = mevcutSoru.sik3;
        document.getElementById('sik4').value = mevcutSoru.sik4;
        document.getElementById('dogruSik').value = mevcutSoru.dogruSik;
    <?php endif; ?>
</script>
</html>
