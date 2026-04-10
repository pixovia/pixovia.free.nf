<?php
// Path to store working URLs
$outputFile = __DIR__ . "/found_urls.txt";

$found = [];
$checked = 0;

// Check if form submitted
if (isset($_POST['start']) && isset($_POST['end'])) {
    $start = intval($_POST['start']);
    $end   = intval($_POST['end']);
    
    if ($end < $start) {
        $error = "End value must be greater than or equal to start value.";
    } else {
        $baseUrl = "https://assets.eduport.app/studio/migrated/hls";
        
        // Function to check URL existence
        function url_exists($url) {
            $headers = @get_headers($url);
            if ($headers && strpos($headers[0], '200') !== false) {
                return true;
            }
            return false;
        }

        for ($i = $start; $i <= $end; $i++) {
            $url = "{$baseUrl}/{$i}/480p_h264/video.m3u8";
            $checked++;
            if (url_exists($url)) {
                $found[] = $url;
            }
        }

        // Save found URLs
        if (!empty($found)) {
            file_put_contents($outputFile, implode(PHP_EOL, $found) . PHP_EOL, FILE_APPEND);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Video URL Checker</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f0f0f0; }
        input[type=number] { width: 80px; }
        .container { background: #fff; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.2);}
        textarea { width: 100%; height: 200px; margin-top: 10px; }
        .error { color: red; }
    </style>
</head>
<body>
<div class="container">
    <h2>Video URL Checker</h2>
    <form method="post">
        <label>Start ID: <input type="number" name="start" required></label><br><br>
        <label>End ID: <input type="number" name="end" required></label><br><br>
        <button type="submit">Check URLs</button>
    </form>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif (isset($checked)): ?>
        <p>Checked <strong><?= $checked ?></strong> URLs.</p>
        <p>Found <strong><?= count($found) ?></strong> working URLs.</p>
        <?php if (!empty($found)): ?>
            <textarea readonly><?= implode(PHP_EOL, $found) ?></textarea>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>