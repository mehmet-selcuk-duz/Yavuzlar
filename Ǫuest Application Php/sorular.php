<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$db = new PDO('sqlite:veriler.db');

$query = "SELECT * FROM sorular";
$stmt = $db->query($query);
$sorular = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        $userId = $_SESSION['user_id'];

        $query = "SELECT skore FROM users WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $currentScore = $stmt->fetchColumn();

        $newScore = $currentScore + $toplamPuan;

        $updateQuery = "UPDATE users SET skore = :newScore WHERE id = :id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':newScore', $newScore, PDO::PARAM_INT);
        $updateStmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $updateStmt->execute();

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
    <title>Quiz Yap</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Quiz Yarışması</h1>
    <button id="geriBtn" onclick="window.location.href='index.php'">Geri</button>
    <br><br>

    <div id="quiz-container" style="<?php echo $soru ? '' : 'display: none;'; ?>">
        <h2>Soru:</h2>
        <p><?php echo htmlspecialchars($soru['soru']); ?></p>
        <form method="POST">
            <input type="hidden" name="suankiSoruIndex" value="<?php echo $suankiSoruIndex; ?>">
            <input type="hidden" name="dogruSayisi" value="<?php echo $dogruSayisi; ?>">
            <input type="hidden" name="yanlisSayisi" value="<?php echo $yanlisSayisi; ?>">
            <input type="hidden" name="toplamPuan" value="<?php echo $toplamPuan; ?>">
            <?php
            if ($soru) {
                for ($i = 1; $i <= 4; $i++) {
                    if (!empty($soru["sik$i"])) {
                        echo '<button type="submit" name="cevap" value="' . $i . '" class="cevap-btn">' . htmlspecialchars($soru["sik$i"]) . '</button>';
                    }
                }
            }
            ?>
        </form>
    </div>

    <div id="sonuc" style="<?php echo $soru ? 'display: none;' : 'display: block;'; ?>">
        <h2>Sonuç</h2>
        <p>Doğru Cevap Sayısı: <span><?php echo $dogruSayisi; ?></span></p>
        <p>Yanlış Cevap Sayısı: <span><?php echo $yanlisSayisi; ?></span></p>
        <p>Toplam Puan: <span><?php echo $toplamPuan; ?></span></p>
    </div>
</body>
</html>
