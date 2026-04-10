<?php
/**
 * Pixovia Tiny - URL Shortener
 * Developed by Pixovia LLC.
 */

$jsonFile = __DIR__ . "/urls.json";

// Generate random ID
function randomID($length = 7) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $id = '';
    for ($i = 0; $i < $length; $i++) {
        $id .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $id;
}

// Load URLs
$urls = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

// Handle form submission
$newShort = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['long_url'])) {
    $url = trim($_POST['long_url']);
    if ($url) {
        // Simple URL validation
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://" . $url;
        }
        
        // Generate unique ID
        do {
            $id = randomID();
        } while (isset($urls[$id]));

        // Save to JSON
        $urls[$id] = $url;
        file_put_contents($jsonFile, json_encode($urls, JSON_PRETTY_PRINT));
        
        // Construct the short link
        $newShort = "https://" . $_SERVER['HTTP_HOST'] . "/tiny/" . $id;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pixovia Tiny | Professional URL Shortener</title>
    <!-- Tailwind CSS for modern styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top right, #f8fafc, #eff6ff);
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
        }

        .brand-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>
<body class="flex flex-col">

    <!-- Navigation/Header -->
    <nav class="w-full p-6 flex justify-between items-center max-w-6xl mx-auto">
        <div class="flex items-center space-x-2">
            <div class="bg-blue-600 p-2 rounded-lg text-white">
                <i data-lucide="link-2" class="w-6 h-6"></i>
            </div>
            <span class="text-xl font-bold tracking-tight text-gray-900">Pixovia <span class="text-blue-600">Tiny</span></span>
        </div>
        <div class="hidden md:flex items-center space-x-6">
            <a href="https://pixovia.pages.dev/about" class="text-sm font-medium text-gray-600 hover:text-blue-600">About Pixovia</a>
            <a href="https://pixovia.pages.dev" class="bg-gray-900 text-white px-4 py-2 rounded-full text-sm font-medium">Get Started</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-xl">
            <div class="text-center mb-10">
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4 tracking-tight">
                    Shorten Your <span class="brand-gradient">Links</span>
                </h1>
                <p class="text-gray-500 text-lg">Fast, secure, and reliable URL shortening by Pixovia LLC.</p>
            </div>

            <div class="glass-card rounded-3xl p-8 md:p-10 border border-gray-100">
                <form method="POST" class="space-y-4">
                    <div>
                        <label for="long_url" class="block text-sm font-semibold text-gray-700 mb-2">Paste your long URL</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="globe" class="w-5 h-5"></i>
                            </div>
                            <input 
                                type="url" 
                                name="long_url" 
                                id="long_url"
                                class="block w-full pl-11 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 placeholder-gray-400"
                                placeholder="https://example.com/very/long/path/to/something" 
                                required
                            >
                        </div>
                    </div>
                    <button type="submit" class="w-full btn-primary text-white font-bold py-4 rounded-2xl flex items-center justify-center space-x-2">
                        <span>Shorten Link</span>
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </button>
                </form>

                <?php if ($newShort): ?>
                    <div class="mt-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6">
                            <p class="text-blue-700 text-sm font-bold mb-3 uppercase tracking-wider">Success! Link Ready:</p>
                            <div class="flex items-center space-x-2">
                                <input 
                                    type="text" 
                                    id="shortResult" 
                                    readonly 
                                    value="<?= htmlspecialchars($newShort) ?>"
                                    class="bg-white border border-blue-200 text-blue-800 font-medium px-4 py-3 rounded-xl w-full focus:outline-none"
                                >
                                <button 
                                    onclick="copyLink()" 
                                    class="bg-blue-600 text-white p-3 rounded-xl hover:bg-blue-700 transition-colors"
                                    title="Copy to clipboard"
                                >
                                    <i data-lucide="copy" id="copyIcon" class="w-6 h-6"></i>
                                </button>
                            </div>
                            <p class="mt-3 text-xs text-blue-500">
                                This link will redirect users to your original destination.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Features Mini Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-12">
                <div class="text-center p-4">
                    <div class="bg-white w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-3 shadow-sm">
                        <i data-lucide="zap" class="w-5 h-5 text-yellow-500"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Instant</h3>
                </div>
                <div class="text-center p-4">
                    <div class="bg-white w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-3 shadow-sm">
                        <i data-lucide="shield-check" class="w-5 h-5 text-green-500"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Secure</h3>
                </div>
                <div class="text-center p-4 col-span-2 md:col-span-1">
                    <div class="bg-white w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-3 shadow-sm">
                        <i data-lucide="infinity" class="w-5 h-5 text-purple-500"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Unlimited</h3>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer / Promo Section -->
    <footer class="w-full bg-white border-t border-gray-100 py-10 mt-12">
        <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0">
            <div class="text-center md:text-left">
                <p class="text-gray-400 text-sm">© <?php echo date("Y"); ?> Pixovia LLC. All rights reserved.</p>
                <p class="text-gray-400 text-xs mt-1">Visit our main site at <a href="https://pixovia.pages.dev" class="text-blue-500 hover:underline">pixovia.pages.dev</a></p>
            </div>
            
            <!-- Branding/Ad Section for Webhost -->
            <a href="https://pixovia.free.nf/webhost" class="group flex items-center space-x-4 bg-gray-50 hover:bg-blue-50 border border-gray-200 p-4 rounded-2xl transition-all">
                <div class="bg-blue-600 text-white p-2 rounded-lg group-hover:scale-110 transition-transform">
                    <i data-lucide="server" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Need Web Hosting?</h4>
                    <p class="text-xs text-gray-500">Host your webpage at <strong>Pixovia Webhost</strong></p>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
            </a>
        </div>
    </footer>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Copy Link Functionality
        function copyLink() {
            const copyText = document.getElementById("shortResult");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy");

            // UI Feedback
            const icon = document.getElementById("copyIcon");
            const originalIcon = icon.getAttribute('data-lucide');
            
            icon.setAttribute('data-lucide', 'check');
            lucide.createIcons();
            
            setTimeout(() => {
                icon.setAttribute('data-lucide', 'copy');
                lucide.createIcons();
            }, 2000);
        }
    </script>
</body>
</html>