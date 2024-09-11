<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $pdo = new PDO('sqlite:veriler.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query('SELECT username, skore FROM users ORDER BY skore DESC');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo 'Veritabanı bağlantı hatası: ' . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ǫuest Application Projesi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Yavuzlar Ǫuest Application</h1>
    <nav>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin') { ?>
            <a href="admin.php"><button type="button">Sorular</button></a>
            <a href="users.php"><button type="button">Kullanıcılar</button></a>
        <?php } ?>
        <a href="sorular.php"><button type="button">Quiz</button></a>
    </nav>
    <center>
        <h2>Skor Tablosu</h2>
        <table>
            <tr>
                <th>Kullanıcı</th>
                <th>Skor</th>
            </tr>
            <?php foreach ($users as $user) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['skore']); ?></td>
                </tr>
            <?php } ?>
        </table>
    </center>
</body>
</html>
