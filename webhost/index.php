<?php
// PHP Logic for File Handling
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '25M');
ini_set('max_execution_time', '300');

$message = "";
$messageType = ""; // success or error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pageName = preg_replace('/[^a-zA-Z0-9-_]/', '', $_POST['page_name']); 
    $targetDir = __DIR__ . '/' . $pageName;

    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (isset($_FILES['zip_file']) && $_FILES['zip_file']['error'] == 0) {
        $zipPath = $_FILES['zip_file']['tmp_name'];
        $zip = new ZipArchive;
        
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($targetDir);
            $zip->close();
            $message = "✅ Success! Site deployed at: <a href='$pageName/' target='_blank' style='color:#60a5fa; text-decoration:underline;'>pixovia.free.nf/webhost/$pageName</a>";
            $messageType = "success";
        } else {
            $message = "❌ Error: Failed to extract ZIP file.";
            $messageType = "error";
        }
    } else {
        $message = "⚠️ Please upload a valid ZIP file (Max 20MB).";
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pixovia WebHost | Free Ads-Free Hosting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
            min-height: 100vh;
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
        }
        .gradient-text {
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .upload-area:hover {
            border-color: #38bdf8;
            background: rgba(56, 189, 248, 0.05);
        }
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="p-4 md:p-8">

    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <header class="text-center mb-12">
            <div class="inline-block p-2 bg-blue-500/10 rounded-full mb-4 px-4 text-sm font-semibold text-blue-400 border border-blue-500/20">
                🚀 Pixovia WebHost by Pixovia LLC
            </div>
            <h1 class="text-4xl md:text-6xl font-bold mb-4 tracking-tight">
                Truly Free <span class="gradient-text">Web Hosting</span>
            </h1>
            <p class="text-slate-400 max-w-2xl mx-auto text-lg">
                The perfect playground for students, developers, and content delivery teams. No ads. No credit card. Just code.
            </p>
        </header>

        <div class="grid lg:grid-cols-5 gap-8">
            <!-- Left Sidebar: Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="glass p-6">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-bolt text-yellow-400 mr-2"></i> Fast Deployment
                    </h3>
                    <ul class="space-y-4 text-slate-300">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-emerald-400 mt-1 mr-3"></i>
                            <span>Upload HTML, CSS, JS or PHP files.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-emerald-400 mt-1 mr-3"></i>
                            <span>Max ZIP size: <strong>20MB</strong> per upload.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-emerald-400 mt-1 mr-3"></i>
                            <span>Unlimited projects and subdirectories.</span>
                        </li>
                    </ul>
                </div>

                <div class="glass p-6 border-blue-500/20 bg-blue-500/5">
                    <h3 class="text-xl font-bold mb-2 flex items-center text-blue-400">
                        <i class="fas fa-photo-video mr-2"></i> Pixovia Library
                    </h3>
                    <p class="text-sm text-slate-400 mb-4">
                        Need to host large assets? Use our dedicated library for files up to 2GB with direct URLs.
                    </p>
                    <a href="https://pixovia.pages.dev/library" class="inline-block w-full text-center py-2 bg-blue-600 hover:bg-blue-500 rounded-xl transition font-semibold">
                        Access Library
                    </a>
                </div>
            </div>

            <!-- Right: Upload Form -->
            <div class="lg:col-span-3">
                <form action="" method="post" enctype="multipart/form-data" class="glass p-8 space-y-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <i class="fas fa-cloud-upload-alt text-8xl"></i>
                    </div>

                    <?php if ($message): ?>
                        <div class="p-4 rounded-xl <?php echo $messageType === 'success' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20'; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-400">Project Endpoint</label>
                        <div class="flex items-center bg-slate-900/50 rounded-xl border border-white/10 p-1">
                            <span class="pl-4 pr-2 text-slate-500 text-sm hidden sm:inline">pixovia.free.nf/webhost/</span>
                            <input type="text" name="page_name" required 
                                   class="bg-transparent border-none focus:ring-0 w-full p-2 text-white placeholder-slate-600"
                                   placeholder="your-project-name">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-400">Static Files (ZIP)</label>
                        <div class="upload-area border-2 border-dashed border-white/10 rounded-2xl p-8 text-center transition cursor-pointer relative" onclick="document.getElementById('fileInput').click()">
                            <input type="file" id="fileInput" name="zip_file" accept=".zip" required class="hidden" onchange="updateFileName(this)">
                            <div id="uploadContent">
                                <i class="fas fa-file-archive text-4xl text-blue-400 mb-4 floating"></i>
                                <p class="text-slate-300 font-medium">Click or drag your .zip folder here</p>
                                <p class="text-xs text-slate-500 mt-2">Max file size: 20MB</p>
                            </div>
                            <div id="fileSelected" class="hidden">
                                <i class="fas fa-check-circle text-4xl text-emerald-400 mb-2"></i>
                                <p id="fileNameDisplay" class="text-emerald-400 font-medium"></p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 rounded-2xl font-bold text-lg shadow-xl shadow-blue-500/20 transition-all transform active:scale-95">
                        Launch Project <i class="fas fa-paper-plane ml-2"></i>
                    </button>
                    
                    <p class="text-center text-xs text-slate-500">
                        By uploading, you agree to host lawful content only. Use Pixovia Library for heavy media.
                    </p>
                </form>
            </div>
        </div>

        <footer class="mt-20 py-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center text-slate-500 text-sm">
            <p>&copy; 2026 Pixovia LLC. All Rights Reserved.</p>
            <div class="flex space-x-6 mt-4 md:mt-0">
                <a href="#" class="hover:text-blue-400">Documentation</a>
                <a href="#" class="hover:text-blue-400">Support</a>
                <a href="#" class="hover:text-blue-400">Privacy</a>
            </div>
        </footer>
    </div>

    <script>
        function updateFileName(input) {
            const display = document.getElementById('fileNameDisplay');
            const content = document.getElementById('uploadContent');
            const selected = document.getElementById('fileSelected');
            
            if (input.files && input.files[0]) {
                display.innerText = input.files[0].name;
                content.classList.add('hidden');
                selected.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>