<?php
require 'db.php';
require 'session.php';

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$userEmail = null;

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($userEmail);
    $stmt->fetch();
    $stmt->close();
}
?>

<?php if ($userEmail): ?>
    <h2>Welcome <?= htmlspecialchars($userEmail) ?></h2>
    <form method="post">
        <button name="logout">Logout</button>
    </form>
<?php else: ?>
    <a href="login.php">Login</a>
<?php endif; ?>