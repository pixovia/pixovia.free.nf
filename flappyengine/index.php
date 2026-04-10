<?php
/**
 * Pixovia Flappy Engine - InfinityFree Optimized
 * This file handles both the creation UI and the server-side publishing.
 */

// 1. Configuration & Setup
$storage_dir = 'published_games';
if (!is_dir($storage_dir)) {
    mkdir($storage_dir, 0755, true);
}

// 2. Handle Publishing (AJAX POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'publish') {
    header('Content-Type: application/json');
    
    $raw_data = file_get_contents('php://input');
    $data = json_decode($raw_data, true);
    
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        exit;
    }

    $game_id = 'game_' . time() . '_' . bin2hex(random_bytes(4));
    $game_path = $storage_dir . '/' . $game_id;
    mkdir($game_path, 0755, true);

    // Create the game HTML content
    $assets_json = json_encode($data['assets']);
    $game_title = htmlspecialchars($data['title'] ?? 'A Pixovia Game');
    
    $html_content = <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>$game_title | Pixovia Flappy Engine</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <style>
        body { margin: 0; overflow: hidden; background: #000; font-family: sans-serif; }
        canvas { display: block; margin: 0 auto; background: #70c5ce; }
        #branding { position: fixed; bottom: 15px; width: 100%; text-align: center; color: rgba(255,255,255,0.6); font-size: 11px; pointer-events: none; text-transform: uppercase; font-weight: bold; }
        #ui { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; }
        .score { position: absolute; top: 30px; font-size: 60px; font-weight: 900; -webkit-text-stroke: 2px #000; }
        .msg { font-size: 24px; background: rgba(15, 23, 42, 0.95); padding: 40px; border-radius: 24px; pointer-events: auto; text-align: center; border: 4px solid #f97316; }
        button { padding: 15px 40px; font-size: 22px; cursor: pointer; background: #f97316; border: none; border-radius: 50px; color: white; margin-top: 20px; font-weight: 900; }
    </style>
</head>
<body>
    <canvas id="game"></canvas>
    <div id="branding">Created on Pixovia Flappy Engine</div>
    <div id="ui">
        <div id="score" class="score">0</div>
        <div id="msg-box" class="msg">
            <h1>$game_title</h1>
            <p>Tap or Space to Jump</p>
            <button id="start-btn">PLAY</button>
        </div>
    </div>
    <script>
        const assets = $assets_json;
        // Game Engine Logic (Simplified for brevity)
        const canvas = document.getElementById('game');
        const ctx = canvas.getContext('2d');
        const startBtn = document.getElementById('start-btn');
        let gameState = 'START';
        let score = 0;

        // Assets
        const bImg = new Image(); bImg.src = assets.bird;
        const bgImg = new Image(); bgImg.src = assets.bg;
        const pImg = new Image(); pImg.src = assets.pipe;
        const jSnd = new Audio(assets.jumpSound);

        function resize() {
            canvas.height = window.innerHeight;
            canvas.width = canvas.height * (320/480);
        }
        window.onresize = resize; resize();

        function jump() { if(gameState === 'PLAYING') { bird.v = -8; jSnd.play().catch(()=>{}); } }
        window.onkeydown = e => e.code === 'Space' && jump();
        canvas.ontouchstart = e => { e.preventDefault(); jump(); };
        canvas.onmousedown = e => { e.preventDefault(); jump(); };
        
        let bird = { x: 50, y: 200, w: 34, h: 24, v: 0 };
        startBtn.onclick = () => { gameState = 'PLAYING'; document.getElementById('msg-box').style.display='none'; };

        function loop() {
            ctx.drawImage(bgImg, 0, 0, canvas.width, canvas.height);
            if(gameState === 'PLAYING') {
                bird.v += 0.4; bird.y += bird.v;
                if(bird.y > canvas.height || bird.y < 0) location.reload();
            }
            ctx.drawImage(bImg, bird.x, bird.y, bird.w, bird.h);
            requestAnimationFrame(loop);
        }
        loop();
    </script>
</body>
</html>
EOT;

    // Save the game files
    file_put_contents($game_path . '/index.html', $html_content);
    
    // Save metadata for the "Explore" page
    $metadata = [
        'id' => $game_id,
        'title' => $game_title,
        'created_at' => date('Y-m-d H:i:s'),
        'author' => 'Community Member'
    ];
    file_put_contents($game_path . '/metadata.json', json_encode($metadata));

    echo json_encode([
        'success' => true, 
        'url' => 'https://' . $_SERVER['HTTP_HOST'] . '/' . $game_path . '/'
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pixovia Flappy Engine | Create & Publish</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <!-- Meta Tags -->
    <meta name="description" content="Pixovia Flappy Engine - Create and host your own flappy bird games on InfinityFree.">
</head>
<body class="bg-slate-900 text-white font-sans">

    <!-- Header -->
    <nav class="bg-slate-800 border-b border-slate-700 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 bg-orange-500 rounded-xl flex items-center justify-center font-black">P</div>
            <span class="font-black text-xl tracking-tighter">PIXOVIA <span class="text-orange-500">FLAPPY</span></span>
        </div>
        <div class="flex gap-8 text-sm font-bold uppercase tracking-widest">
            <a href="?view=create" class="text-orange-500 border-b-2 border-orange-500 pb-1">Create</a>
            <a href="?view=explore" class="text-slate-400 hover:text-white transition">Explore</a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto p-8">
        <?php if (!isset($_GET['view']) || $_GET['view'] === 'create'): ?>
            <header class="flex justify-between items-center mb-10">
                <div>
                    <h1 class="text-4xl font-black tracking-tight">Game Studio</h1>
                    <p class="text-slate-400">Published directly to your server</p>
                </div>
                <button onclick="publishGame()" id="publish-btn" class="bg-orange-600 hover:bg-orange-500 px-8 py-3 rounded-2xl font-bold shadow-lg shadow-orange-900/40 transition-all active:scale-95">
                    Deploy to Server
                </button>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Asset inputs -->
                <div class="bg-slate-800/40 p-5 rounded-3xl border border-slate-700/50 flex flex-col gap-4">
                    <label class="font-bold">Game Title</label>
                    <input type="text" id="game-title" value="My Epic Bird Game" class="bg-slate-900 border border-slate-700 rounded-xl p-3 outline-none focus:border-orange-500">
                    
                    <div class="grid grid-cols-1 gap-3 mt-4">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Sprites & Sounds</p>
                        <input type="file" id="up-bird" class="text-xs" accept="image/*">
                        <input type="file" id="up-bg" class="text-xs" accept="image/*">
                        <input type="file" id="up-pipe" class="text-xs" accept="image/*">
                    </div>
                </div>

                <div id="preview-container" class="flex flex-col items-center bg-slate-800/20 p-4 rounded-3xl border border-dashed border-slate-700">
                    <p class="text-xs font-bold text-slate-500 mb-4">PREVIEW NOT DEPLOYED</p>
                    <div class="w-48 h-72 bg-black rounded-2xl shadow-2xl flex items-center justify-center text-slate-700 text-center p-4">
                        Select files and click Deploy to see your game live
                    </div>
                </div>
            </div>

        <?php elseif ($_GET['view'] === 'explore'): ?>
            <h1 class="text-4xl font-black mb-8">Community Games</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $games = glob($storage_dir . '/*/metadata.json');
                if (empty($games)) echo "<p class='text-slate-500'>No games published yet.</p>";
                foreach ($games as $file) {
                    $meta = json_decode(file_get_contents($file), true);
                    $url = str_replace('metadata.json', 'index.html', $file);
                    echo "
                    <div class='bg-slate-800 p-6 rounded-3xl border border-slate-700'>
                        <h3 class='font-bold text-xl mb-2'>{$meta['title']}</h3>
                        <p class='text-xs text-slate-500 mb-4'>Published: {$meta['created_at']}</p>
                        <a href='$url' target='_blank' class='inline-block bg-orange-600 px-6 py-2 rounded-xl text-sm font-bold'>Play Game</a>
                    </div>";
                }
                ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        async function getBase64(file) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
            });
        }

        async function publishGame() {
            const btn = document.getElementById('publish-btn');
            btn.innerText = "Deploying...";
            btn.disabled = true;

            const assets = {
                bird: 'https://raw.githubusercontent.com/sourabhv/FlappyBirdClone/master/assets/img/bird.png',
                bg: 'https://raw.githubusercontent.com/sourabhv/FlappyBirdClone/master/assets/img/background.png',
                pipe: 'https://raw.githubusercontent.com/sourabhv/FlappyBirdClone/master/assets/img/pipe-green.png',
                jumpSound: 'https://raw.githubusercontent.com/sourabhv/FlappyBirdClone/master/assets/audio/wing.wav'
            };

            // Get uploaded files
            const birdFile = document.getElementById('up-bird').files[0];
            const bgFile = document.getElementById('up-bg').files[0];
            const pipeFile = document.getElementById('up-pipe').files[0];

            if(birdFile) assets.bird = await getBase64(birdFile);
            if(bgFile) assets.bg = await getBase64(bgFile);
            if(pipeFile) assets.pipe = await getBase64(pipeFile);

            const payload = {
                title: document.getElementById('game-title').value,
                assets: assets
            };

            try {
                const response = await fetch('?action=publish', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                
                if(result.success) {
                    alert('Game Published Successfully! URL: ' + result.url);
                    window.location.href = '?view=explore';
                }
            } catch (e) {
                alert('Publishing failed. Check server permissions.');
            } finally {
                btn.innerText = "Deploy to Server";
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>