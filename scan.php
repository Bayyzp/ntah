<?php
// Bayy ShellScanner - responsif desktop & mobile
set_time_limit(0);
error_reporting(0);
@ini_set('zlib.output_compression', 0);

$BASE_DIR = realpath(__DIR__);

function bayy_getSubfolders($dir) {
    $folders = [];
    foreach (glob($dir.'/*', GLOB_ONLYDIR) as $subdir) {
        $folders[] = $subdir;
    }
    return $folders;
}

// Handle multiple file deletion
if (isset($_POST['delete_selected']) && !empty($_POST['selected_files'])) {
    $deleted_count = 0;
    $failed_count = 0;

    foreach ($_POST['selected_files'] as $file) {
        $target = realpath($file);
        if ($target && is_file($target) && strpos($target, $BASE_DIR) === 0) {
            if (@unlink($target)) {
                $deleted_count++;
            } else {
                $failed_count++;
            }
        }
    }

    if ($deleted_count > 0 || $failed_count > 0) {
        $msg_delete = "✅ Deleted: $deleted_count file(s) | ❌ Failed: $failed_count file(s)";
    }
}

// Handle auto delete all detected with exclusions
if (isset($_POST['hajar_selected'])) {
    $scanTarget = isset($_POST['folder']) ? realpath($_POST['folder']) : $BASE_DIR;
    if (!$scanTarget || strpos($scanTarget,$BASE_DIR)!==0) $scanTarget=$BASE_DIR;
    
    $deleted_count = 0;
    $failed_count = 0;
    $excluded_count = 0;
    
    if (!empty($_POST['exclude_files'])) {
        $excluded_files = $_POST['exclude_files'];
        
        foreach ($_POST['all_detected_files'] as $file) {
            if (in_array($file, $excluded_files)) {
                $excluded_count++;
                continue;
            }
            
            $target = realpath($file);
            if ($target && is_file($target) && strpos($target, $BASE_DIR) === 0) {
                if (@unlink($target)) {
                    $deleted_count++;
                } else {
                    $failed_count++;
                }
            }
        }
        
        $msg_delete = "🎯 Auto-delete completed: ✅ $deleted_count deleted | ❌ $failed_count failed | 🛡️ $excluded_count excluded";
    }
}

// Handle single file deletion
if (isset($_GET['delete'])) {
    $target = realpath($_GET['delete']);
    if ($target && is_file($target) && strpos($target, $BASE_DIR) === 0) {
        $msg_delete = @unlink($target)
        ? "✅ File deleted: " . htmlspecialchars(str_replace($BASE_DIR, '', $target))
        : "❌ Failed to delete file.";
    } else $msg_delete = "⚠️ Invalid delete path.";
}

if (isset($_GET['view'])) {
    $targetv = realpath($_GET['view']);
    if ($targetv && is_file($targetv) && strpos($targetv, $BASE_DIR) === 0) {
        $preview_content = @file_get_contents($targetv);
        $preview_path = $targetv;
    } else $preview_error = "⚠️ Invalid view path.";
}

// Function to get website preview URL
function bayy_getWebsitePreviewUrl($filePath) {
    $baseDir = realpath(__DIR__);
    $relativePath = str_replace($baseDir, '', $filePath);
    $relativePath = ltrim(str_replace(DIRECTORY_SEPARATOR, '/', $relativePath), '/');
    
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
               . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    $baseUrl = rtrim($baseUrl, '/');
    
    return $baseUrl . '/' . $relativePath;
}

function bayy_listPhpFiles($startDir) {
    $files = []; $allowedExt = ['php','phtml','inc','phar','php3','php4','php5','php7','html','htm'];
    try {
        $dirIter = new RecursiveDirectoryIterator($startDir, FilesystemIterator::SKIP_DOTS);
        foreach (new RecursiveIteratorIterator($dirIter) as $fileInfo) {
            if ($fileInfo->isLink()) continue;
            if ($fileInfo->isFile()) {
                $ext = strtolower(pathinfo($fileInfo->getFilename(), PATHINFO_EXTENSION));
                if (in_array($ext, $allowedExt, true)) $files[] = $fileInfo->getPathname();
            }
        }
    } catch (Exception $e) {}
    return $files;
}

function bayy_readFileData($filePath) {
    if (!is_file($filePath) || !is_readable($filePath)) return [[], ''];
    $sizeMB = round(@filesize($filePath)/1024/1024, 2);
    if ($sizeMB > 5) return [['__SKIPPED_TOO_BIG__'], ''];
    $content = @file_get_contents($filePath);
    if ($content === false) return [[], ''];
    $tokensArr = @token_get_all($content);
    $words = [];
    foreach ($tokensArr as $t) if (is_array($t)) $words[] = trim($t[1]);
    return [array_values(array_unique(array_filter($words))), $content];
}

function bayy_analyzeFile($tokens,$content) {
    if ($tokens === ['__SKIPPED_TOO_BIG__']) return '';
    
    // HANYA PATTERN YANG BENAR-BENAR BERBAHAYA
    $obfus=['base64_decode','gzinflate','str_rot13','convert_uu','gzuncompress','gzdecode'];
    $eval=['eval','assert','create_function','preg_replace'];
    $file_manipulation=['rename','chmod','chown','file_put_contents','fwrite','fput','unlink','copy'];
    $suspicious_names=['r57','c99','webshell','b374k','wso','cpanel','phpspy'];
    
    $all_patterns = array_merge($obfus, $eval, $file_manipulation, $suspicious_names);
    
    $found=[]; 
    foreach($all_patterns as $q) {
        if(in_array($q,$tokens,true)) $found[]=$q;
    }
    
    // CRITICAL: Base64 decode + eval combination
    $critical_patterns = [
        // Base64 decode patterns
        '/base64_decode\(["\']([A-Za-z0-9+\/=]{20,})["\']\)/',
        '/eval\s*\(\s*base64_decode\s*\(\s*["\']([A-Za-z0-9+\/=]+)["\']\s*\)\s*\)/i',
        '/gzinflate\s*\(\s*base64_decode\s*\(\s*["\']([A-Za-z0-9+\/=]+)["\']\s*\)\s*\)/i',
        
        // File manipulation with dangerous combinations
        '/(rename|chmod|chown|unlink)\s*\(\s*[\'"]\.\.[\'"]/i',
        '/file_put_contents\s*\(\s*[\$_\w]+\s*,\s*[\$_\w]+\s*\)/',
        
        // Obfuscated code patterns
        '/\\x[0-9a-f]{2}/i',
        '/eval\s*\(\s*\$\w+\s*\)/i',
        '/assert\s*\(\s*\$\w+\s*\)/i'
    ];
    
    $critical_hits = 0;
    foreach($critical_patterns as $pattern) {
        if(preg_match($pattern, $content)) {
            $critical_hits++;
            $found[] = 'critical_pattern';
            break;
        }
    }

    $flag=false; 
    $reason=[];
    
    // CRITICAL DETECTION 1: Base64 decode + eval (WEBSHELL CLASSIC)
    if((array_intersect($tokens,$obfus) && array_intersect($tokens,$eval)) || $critical_hits > 0){ 
        $flag=true; 
        $reason[] = 'encoded_webshell';
    }
    
    // CRITICAL DETECTION 2: File manipulation + eval/exec
    if(!$flag && array_intersect($tokens,$file_manipulation) && 
       (array_intersect($tokens,$eval) || stripos($content, 'exec') !== false || stripos($content, 'system') !== false)){ 
        $flag=true; 
        $reason[] = 'file_malware';
    }
    
    // CRITICAL DETECTION 3: Known webshell names dengan pattern berbahaya
    if(!$flag) {
        foreach($suspicious_names as $n) {
            if(stripos($content,$n)!==false){ 
                // Cek apakah ini benar-benar webshell atau hanya nama file biasa
                $webshell_patterns = [
                    '/<\?php\s*.*' . $n . '.*\?>/is',
                    '/function.*' . $n . '.*\{/is',
                    '/class.*' . $n . '.*\{/is'
                ];
                
                foreach($webshell_patterns as $wp) {
                    if(preg_match($wp, $content)) {
                        $flag=true; 
                        $reason[] = 'known_webshell';
                        break 2;
                    }
                }
            }
        }
    }

    // CRITICAL DETECTION 4: File upload capability dengan eksekusi
    if(!$flag) {
        $upload_patterns = [
            '/move_uploaded_file.*eval/i',
            '/\$_FILES.*base64_decode/i',
            '/move_uploaded_file.*base64_decode/i'
        ];
        
        foreach($upload_patterns as $up) {
            if(preg_match($up, $content)) {
                $flag = true;
                $reason[] = 'upload_malware';
                break;
            }
        }
    }

    return $flag ? "🚨 CRITICAL: " . implode('|',array_slice($found,0,3)) . " | reasons:" . implode('|',$reason) : '';
}

$subfolders = bayy_getSubfolders($BASE_DIR);
$scanTarget = isset($_GET['folder']) ? realpath($_GET['folder']) : $BASE_DIR;
if (!$scanTarget || strpos($scanTarget,$BASE_DIR)!==0) $scanTarget=$BASE_DIR;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 Bayy ShellScanner - Advanced Threat Detection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #1f2937;
            --darker: #111827;
            --light: #f8fafc;
            --gray: #6b7280;
            --gray-dark: #374151;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, var(--darker) 0%, var(--dark) 100%);
            color: var(--light);
            min-height: 100vh;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background: rgba(17, 24, 39, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--light);
        }

        .logo i {
            color: var(--primary);
        }

        .nav-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
        }

        .btn-success {
            background: var(--secondary);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--gray);
            color: var(--light);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Layout */
        .app-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 0;
            min-height: calc(100vh - 80px);
        }

        /* Sidebar */
        .sidebar {
            background: rgba(31, 41, 55, 0.8);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255,255,255,0.1);
            padding: 2rem 1rem;
            height: calc(100vh - 80px);
            position: sticky;
            top: 80px;
            overflow-y: auto;
        }

        .sidebar-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gray);
            margin-bottom: 1rem;
            padding-left: 12px;
        }

        .nav-links {
            list-style: none;
        }

        .nav-links li {
            margin-bottom: 4px;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--light);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-links a:hover,
        .nav-links a.active {
            background: var(--primary);
            color: white;
        }

        .nav-links i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
            overflow-y: auto;
        }

        /* Cards */
        .card {
            background: rgba(31, 41, 55, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }

        .card-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--light);
        }

        /* File Items */
        .file-item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 1rem;
            align-items: start;
            padding: 1.25rem;
            background: rgba(17, 24, 39, 0.6);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 12px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .file-item.critical {
            border-left: 4px solid var(--danger);
            background: rgba(239, 68, 68, 0.1);
        }

        .file-item:hover {
            border-color: var(--primary);
            background: rgba(17, 24, 39, 0.8);
        }

        .file-checkbox {
            margin-top: 4px;
            transform: scale(1.2);
        }

        .file-info {
            flex: 1;
        }

        .file-path {
            font-family: 'Monaco', 'Consolas', monospace;
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 6px;
            word-break: break-all;
        }

        .file-analysis {
            display: inline-block;
            background: var(--danger);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .file-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 8px;
        }

        /* Website Preview */
        .website-preview {
            grid-column: 1 / -1;
            margin-top: 1rem;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        .preview-header {
            background: var(--dark);
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .preview-title {
            font-weight: 600;
            color: var(--light);
        }

        .preview-iframe {
            width: 100%;
            height: 300px;
            border: none;
            background: white;
        }

        /* Bulk Actions */
        .bulk-actions {
            background: var(--dark);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .select-all {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
        }

        .stat-card.danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
        }

        .stat-card.success {
            background: linear-gradient(135deg, var(--secondary) 0%, #059669 100%);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Detection Info */
        .detection-info {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--danger);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .detection-info h4 {
            color: var(--danger);
            margin-bottom: 0.5rem;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(10px);
            z-index: 2000;
        }

        .modal-content {
            background: var(--dark);
            border-radius: 20px;
            padding: 2rem;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            margin: 5% auto;
            border: 1px solid rgba(255,255,255,0.1);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .app-layout {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none;
            }
            
            .nav-actions {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 768px) {
            .file-item {
                grid-template-columns: 1fr;
            }
            
            .bulk-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .website-preview {
                grid-column: 1;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <i class="fas fa-shield-alt"></i>
                    Bayy ShellScanner
                </div>
                <div class="nav-actions">
                    <a href="?scan=1" class="btn btn-primary">
                        <i class="fas fa-radar"></i>Scan Now
                    </a>
                    <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="btn btn-outline">
                        <i class="fas fa-refresh"></i>Refresh
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="app-layout">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="sidebar-title">Quick Scan</div>
                <ul class="nav-links">
                    <li>
                        <a href="?scan=1" class="<?php echo !isset($_GET['folder']) ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i>Root Directory
                        </a>
                    </li>
                    <?php foreach($subfolders as $f): ?>
                    <li>
                        <a href="?scan=1&folder=<?php echo urlencode($f); ?>" 
                           class="<?php echo (isset($_GET['folder']) && $_GET['folder'] === $f) ? 'active' : ''; ?>">
                            <i class="fas fa-folder"></i><?php echo basename($f); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="main-content">
                <?php if (!empty($msg_delete)): ?>
                <div class="card">
                    <div style="color: var(--secondary); font-weight: 600;">
                        <i class="fas fa-check-circle"></i> <?php echo $msg_delete; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($preview_error)): ?>
                <div class="card">
                    <div style="color: var(--danger); font-weight: 600;">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $preview_error; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['scan'])): ?>
                <!-- Detection Info -->


                <!-- Scan Actions -->
                <div class="card">
                    <div class="scan-actions" style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <a href="?scan=1&folder=<?php echo urlencode($scanTarget); ?>" class="btn btn-primary">
                            <i class="fas fa-redo"></i>Rescan
                        </a>
                        <a href="#" class="btn btn-danger" onclick="return showHajarModal()">
                            <i class="fas fa-bomb"></i>HAJAR - Delete Critical Threats
                        </a>
                    </div>
                </div>

                <?php
                $list = bayy_listPhpFiles($scanTarget);
                $found = 0;
                $detected_files = [];

                foreach ($list as $value) {
                    list($tokens,$content) = bayy_readFileData($value);
                    $analysis = bayy_analyzeFile($tokens,$content);
                    if ($analysis!=='') {
                        $found++;
                        $detected_files[] = [
                            'path' => $value,
                            'analysis' => $analysis,
                            'preview_url' => bayy_getWebsitePreviewUrl($value),
                            'is_critical' => true
                        ];
                    }
                }
                ?>

                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($list); ?></div>
                        <div class="stat-label">Files Scanned</div>
                    </div>
                    <div class="stat-card danger">
                        <div class="stat-number"><?php echo $found; ?></div>
                        <div class="stat-label">Critical Threats</div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-number"><?php echo count($list) - $found; ?></div>
                        <div class="stat-label">Clean Files</div>
                    </div>
                </div>

                <?php if ($found > 0): ?>
                <!-- Bulk Actions -->
                <div class="bulk-actions">
                    <div class="select-all">
                        <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)">
                        <label for="select-all">Select All Critical Threats</label>
                    </div>
                    <button type="button" class="btn btn-danger" onclick="deleteSelectedFiles()">
                        <i class="fas fa-trash"></i>Delete Selected (<span id="selectedCount">0</span>)
                    </button>
                </div>

                <form method="post" id="bulkForm">
                    <?php foreach ($detected_files as $file): ?>
                    <div class="file-item <?php echo $file['is_critical'] ? 'critical' : ''; ?>">
                        <input type="checkbox" class="file-checkbox" name="selected_files[]" 
                               value="<?php echo htmlspecialchars($file['path']); ?>" onchange="updateSelectedCount()">
                        
                        <div class="file-info">
                            <div class="file-path">
                                <i class="fas fa-file-code"></i>
                                <?php echo htmlspecialchars(str_replace($BASE_DIR, '', $file['path'])); ?>
                            </div>
                            <div class="file-analysis">
                                <i class="fas fa-skull-crossbones"></i>
                                <?php echo htmlspecialchars($file['analysis']); ?>
                            </div>
                            <div class="file-actions">
                                <a href="<?php echo $_SERVER['PHP_SELF'].'?view='.urlencode($file['path']); ?>" 
                                   class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i>View Code
                                </a>
                                <a href="<?php echo $file['preview_url']; ?>" 
                                   target="_blank" class="btn btn-outline btn-sm">
                                    <i class="fas fa-external-link-alt"></i>Open Page
                                </a>
                                <a href="<?php echo $_SERVER['PHP_SELF'].'?delete='.urlencode($file['path']); ?>" 
                                   onclick="return confirm('🚨 DELETE CRITICAL THREAT?')" 
                                   class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>Delete
                                </a>
                            </div>

                            <!-- Website Preview -->
                            <div class="website-preview">
                                <div class="preview-header">
                                    <div class="preview-title">
                                        <i class="fas fa-globe"></i> Live Preview - <?php echo htmlspecialchars(basename($file['path'])); ?>
                                    </div>
                                    <a href="<?php echo $file['preview_url']; ?>" target="_blank" class="btn btn-sm btn-outline">
                                        <i class="fas fa-expand"></i> Fullscreen
                                    </a>
                                </div>
                                <iframe src="<?php echo $file['preview_url']; ?>" 
                                        class="preview-iframe"
                                        loading="lazy"
                                        sandbox="allow-scripts allow-same-origin allow-forms">
                                </iframe>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </form>

                <!-- Hajar Form -->
                <form method="post" id="hajarForm">
                    <input type="hidden" name="folder" value="<?php echo htmlspecialchars($scanTarget); ?>">
                    <?php foreach ($detected_files as $file): ?>
                    <input type="hidden" name="all_detected_files[]" value="<?php echo htmlspecialchars($file['path']); ?>">
                    <?php endforeach; ?>
                </form>

                <?php else: ?>
                <div class="card">
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-check-circle" style="font-size: 4rem; color: var(--secondary); margin-bottom: 1rem;"></i>
                        <h3>No Critical Threats Detected! 🎉</h3>
                        <p style="color: var(--gray); margin-top: 0.5rem;">Your system is clean from encoded webshells and file manipulation malware.</p>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Hajar Modal -->
    <div id="hajarModal" class="modal">
        <div class="modal-content">
            <h3 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-bomb" style="color: var(--danger);"></i>
                HAJAR - Delete Critical Threats
            </h3>
            <p style="margin-bottom: 1rem; color: var(--danger); font-weight: 600;">
                <i class="fas fa-exclamation-triangle"></i> 
                WARNING: This will delete all critical threat files. Select files to exclude:
            </p>
            
            <div id="excludeFileList" style="max-height: 400px; overflow-y: auto; margin: 1rem 0; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 1rem;">
                <!-- Files will be populated here -->
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 1.5rem;">
                <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
                <button class="btn btn-danger" onclick="executeHajar()">
                    <i class="fas fa-bomb"></i> EXECUTE HAJAR
                </button>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk select/deselect semua checkbox
        function toggleSelectAll(source) {
            const checkboxes = document.querySelectorAll('.file-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
            });
            updateSelectedCount();
        }

        // Update counter file yang terpilih
        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('.file-checkbox:checked');
            document.getElementById('selectedCount').textContent = checkboxes.length;
        }

        // Delete file yang terpilih
        function deleteSelectedFiles() {
            const checkboxes = document.querySelectorAll('.file-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('❌ No files selected for deletion.');
                return;
            }

            if (confirm(`🚨 ARE YOU SURE?\n\nThis will delete ${checkboxes.length} critical threat file(s). This action cannot be undone!`)) {
                // Submit form secara manual
                const form = document.getElementById('bulkForm');
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_selected';
                deleteInput.value = '1';
                form.appendChild(deleteInput);
                form.submit();
            }
        }

        // Modal untuk HAJAR
        function showHajarModal() {
            const detectedFiles = <?php echo isset($detected_files) ? json_encode(array_column($detected_files, 'path')) : '[]'; ?>;
            const fileList = document.getElementById('excludeFileList');
            
            if (detectedFiles.length === 0) {
                alert('❌ No critical threats detected to delete.');
                return false;
            }
            
            fileList.innerHTML = detectedFiles.map(file => {
                const relativePath = file.replace('<?php echo $BASE_DIR; ?>', '');
                return `
                <div style="display: flex; align-items: center; gap: 12px; padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <input type="checkbox" class="exclude-checkbox" value="${file}" checked style="transform: scale(1.2);">
                    <span style="font-family: monospace; font-size: 0.9rem; flex: 1;">${relativePath}</span>
                </div>
                `;
            }).join('');
            
            document.getElementById('hajarModal').style.display = 'block';
            return false;
        }

        function closeModal() {
            document.getElementById('hajarModal').style.display = 'none';
        }

        function executeHajar() {
            const form = document.getElementById('hajarForm');
            const checkboxes = document.querySelectorAll('#excludeFileList input[type="checkbox"]');
            
            let filesToExclude = [];
            checkboxes.forEach(checkbox => {
                if (!checkbox.checked) { // Jika UNCHECKED, berarti file ini TIDAK dikecualikan (akan dihapus)
                    // File ini akan dihapus, tidak perlu ditambahkan ke exclude
                } else { // Jika CHECKED, berarti file ini dikecualikan (tidak dihapus)
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'exclude_files[]';
                    input.value = checkbox.value;
                    form.appendChild(input);
                    filesToExclude.push(checkbox.value);
                }
            });
            
            const filesToDelete = <?php echo isset($detected_files) ? count($detected_files) : 0; ?> - filesToExclude.length;
            
            if (filesToDelete === 0) {
                alert('❌ No files selected for deletion. Please uncheck files you want to DELETE.');
                return;
            }
            
            if (confirm(`🚨 FINAL WARNING!\n\nThis will PERMANENTLY DELETE ${filesToDelete} critical threat file(s).\n\nFiles to be excluded: ${filesToExclude.length}\n\nContinue?`)) {
                const hajarInput = document.createElement('input');
                hajarInput.type = 'hidden';
                hajarInput.name = 'hajar_selected';
                hajarInput.value = '1';
                form.appendChild(hajarInput);
                form.submit();
            }
        }

        // Initialize ketika halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedCount();
            
            // Add event listener untuk semua checkbox
            const checkboxes = document.querySelectorAll('.file-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });
        });

        // Close modal ketika klik di luar
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('hajarModal');
            if (e.target === modal) {
                closeModal();
            }
        });
    </script>
</body>
</html>
