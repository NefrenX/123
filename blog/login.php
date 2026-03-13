<?php
require 'config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверный email или пароль';
    }
}

include 'templates/header.php';
?>

<div class="form-box">
    <h2>Вход</h2>

    <?php if ($error): ?>
        <div class="notice"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required>
        </div>

        <div class="form-group">
            <label for="password">Пароль</label>
            <input id="password" name="password" type="password" required>
        </div>

        <button type="submit">Войти</button>
    </form>
</div>

<?php include 'templates/footer.php'; ?>