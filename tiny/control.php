<?php
// ====== CONFIG ======
$password = "mypassword"; // ← change this to your own strong password
$jsonFile = __DIR__ . "/urls.json";

// ====== AUTH ======
session_start();
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

if (!isset($_SESSION['logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if ($_POST['password'] === $password) {
            $_SESSION['logged_in'] = true;
        } else {
            $error = "Incorrect password!";
        }
    }

    if (!isset($_SESSION['logged_in'])) {
        ?>
        <form method="POST" style="max-width:300px;margin:100px auto;text-align:center;">
            <h2>🔑 TinyURL Admin</h2>
            <input type="password" name="password" placeholder="Enter Password" required
                   style="width:100%;padding:10px;margin:10px 0;">
            <button type="submit" style="padding:8px 16px;">Login</button>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        </form>
        <?php
        exit();
    }
}

// ====== LOAD JSON ======
$urls = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

// ====== ADD NEW URL ======
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['short_id']) && isset($_POST['long_url'])) {
    $id = trim($_POST['short_id']);
    $url = trim($_POST['long_url']);

    if ($id && $url) {
        $urls[$id] = $url;
        file_put_contents($jsonFile, json_encode($urls, JSON_PRETTY_PRINT));
        $message = "✅ Added: $id → $url";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>TinyURL Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f6f6; padding: 20px; }
        .box { background: white; padding: 20px; border-radius: 10px; max-width: 600px; margin: auto; }
        input[type=text], input[type=url] { width: 100%; padding: 8px; margin: 6px 0; }
        button { padding: 8px 16px; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    </style>
</head>
<body>
<div class="box">
    <h2>📎 TinyURL Admin</h2>
    <form method="POST">
        <input type="text" name="short_id" placeholder="Short ID (e.g. yt)" required>
        <input type="url" name="long_url" placeholder="Full URL (https://...)" required>
        <button type="submit">Add / Update</button>
    </form>

    <?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>

    <form method="POST" style="margin-top:10px;">
        <button name="logout">Logout</button>
    </form>

    <h3>Existing URLs</h3>
    <table>
        <tr><th>ID</th><th>Destination</th><th>Short Link</th></tr>
        <?php foreach ($urls as $k => $v): ?>
            <tr>
                <td><?= htmlspecialchars($k) ?></td>
                <td><a href="<?= htmlspecialchars($v) ?>" target="_blank"><?= htmlspecialchars($v) ?></a></td>
                <td><a href="https://<?= $_SERVER['HTTP_HOST'] ?>/tiny/<?= htmlspecialchars($k) ?>" target="_blank">
                    <?= $_SERVER['HTTP_HOST'] ?>/tiny/<?= htmlspecialchars($k) ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
