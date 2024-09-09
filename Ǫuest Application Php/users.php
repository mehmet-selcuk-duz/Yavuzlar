<?php
session_start();

$db = new PDO('sqlite:veriler.db');

$users = $db->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

    if ($action === 'delete' && $id) {
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header('Location: users.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $id = isset($_POST['userid']) ? (int)$_POST['userid'] : null;

    if ($id) {
        $stmt = $db->prepare("UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id");
        $stmt->bindParam(':id', $id);
    } else {
        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    }

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);
    $stmt->execute();
    
    header('Location: users.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Yönetimi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Kullanıcı Yönetimi</h1>
    <button onclick="window.location.href='login.php'">Çıkış Yap</button>
    <br><br>
    <h2>Kullanıcı Ekleme / Düzenleme</h2>
    <form method="POST" action="users.php">
        <input type="hidden" name="userid" id="userid">
        <label>Kullanıcı Adı:</label><br>
        <input type="text" name="username" id="username" required><br>
        <label>Şifre:</label><br>
        <input type="password" name="password" id="password" required><br>
        <label>Rol:</label><br>
        <select name="role" id="role">
            <option value="admin">Admin</option>
            <option value="user">Kullanıcı</option>
        </select><br>
        <button type="submit">Kaydet</button>
    </form>

    <h2>Mevcut Kullanıcılar</h2>
    <input type="text" id="kullaniciArama" placeholder="Kullanıcı Ara..."><br>
    <div id="kullaniciListesi">
        <?php foreach ($users as $user) : ?>
            <div class="kullanici-item">
                <p><strong>Kullanıcı Adı:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Rol:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
                <a href="users.php?userid=<?php echo $user['id']; ?>">Düzenle</a>
                <a href="users.php?action=delete&id=<?php echo $user['id']; ?>">Sil</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
    <script>
        document.getElementById('kullaniciArama').addEventListener('input', function() {
            const aranan = this.value.toLowerCase();
            document.querySelectorAll('.kullanici-item').forEach(item => {
                const usernameText = item.querySelector('p').textContent.toLowerCase();
                item.style.display = usernameText.includes(aranan) ? '' : 'none';
            });
        });

        <?php if (isset($_GET['userid'])) : ?>
            document.getElementById('userid').value = <?php echo $_GET['userid']; ?>;
            const users = <?php echo json_encode($users); ?>;
            const mevcutUser = users.find(user => user.id === <?php echo $_GET['userid']; ?>);
            document.getElementById('username').value = mevcutUser.username;
            document.getElementById('role').value = mevcutUser.role;
        <?php endif; ?>
    </script>
</html>
