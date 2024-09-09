<?php
session_start();
$db = new PDO('sqlite:veriler.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: ' . ($user['role'] === 'admin' ? 'index.php' : 'sorular.php'));
        exit();
    } else {
        $error = "Geçersiz kullanıcı adı veya şifre.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Giriş Yap</h1>
    <?php if (isset($error)) echo '<p>' . htmlspecialchars($error) . '</p>'; ?>
    <form method="POST">
        <label>Kullanıcı Adı:</label><br>
        <input type="text" name="username" required><br>
        <label>Şifre:</label><br>
        <input type="password" name="password" required><br>
        <button type="submit">Giriş Yap</button>
    </form>
</body>
</html>
