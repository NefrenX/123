<?php
require 'config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (mb_strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } elseif ($password !== $confirm) {
        $error = 'Пароли не совпадают';
    } else {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);

        if ($checkStmt->fetch()) {
            $error = 'Пользователь с таким email уже существует';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, password)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$name, $email, $hash]);

            header('Location: login.php');
            exit;
        }
    }
}

include 'templates/header.php';
?>

<div class="form-box">
    <h2>Регистрация</h2>

    <?php if ($error): ?>
        <div class="notice"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="name">Имя</label>
            <input id="name" name="name" type="text" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required>
        </div>

        <div class="form-group">
            <label for="password">Пароль</label>
            <input id="password" name="password" type="password" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Подтверждение пароля</label>
            <input id="confirm_password" name="confirm_password" type="password" required>
        </div>

        <button type="submit">Зарегистрироваться</button>
    </form>
</div>

<?php include 'templates/footer.php'; ?>