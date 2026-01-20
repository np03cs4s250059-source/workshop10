<?php
require 'db.php';
require 'session.php';

$error = "";

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid request");
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Invalid email or password";
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hashedPassword);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $id;
                header("Location: dashboard.php");
                exit;
            }
        }

        $error = "Invalid email or password";
        $stmt->close();
    }
}
?>

<form method="post">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>

<?= htmlspecialchars($error) ?>