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

if (isset($_GET['delete'])) {
    $target = realpath($_GET['delete']);
    if ($target && is_file($target) && strpos($target, $BASE_DIR) === 0) {
        $msg_delete = @unlink($target) 
            ? "File deleted: " . htmlspecialchars(str_replace($BASE_DIR, '', $target))
            : "Failed to delete file.";
    } else $msg_delete = "Invalid delete path.";
}

if (isset($_GET['view'])) {
    $targetv = realpath($_GET['view']);
    if ($targetv && is_file($targetv) && strpos($targetv, $BASE_DIR) === 0) {
        $preview_content = @file_get_contents($targetv);
        $preview_path = $targetv;
    } else $preview_error = "Invalid view path.";
}

function bayy_listPhpFiles($startDir) {
    $files = []; $allowedExt = ['php','phtml','inc','phar'];
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
    if ($sizeMB > 2) return [['__SKIPPED_TOO_BIG__'], ''];
    $content = @file_get_contents($filePath);
    if ($content === false) return [[], ''];
    $tokensArr = @token_get_all($content);
    $words = [];
    foreach ($tokensArr as $t) if (is_array($t)) $words[] = trim($t[1]);
    return [array_values(array_unique(array_filter($words))), $content];
}

function bayy_analyzeFile($tokens,$content) {
    if ($tokens === ['__SKIPPED_TOO_BIG__']) return '';
    $obfus=['base64_decode','gzinflate','str_rot13','convert_uu'];
    $eval=['eval','assert','create_function'];
    $exec=['exec','shell_exec','passthru','popen','proc_open'];
    $upload=['move_uploaded_file','$_FILES','is_uploaded_file'];
    $names=['r57','c99','webshell','b374k'];

    $found=[]; foreach(array_merge($obfus,$eval,$exec,$upload,$names) as $q) 
        if(in_array($q,$tokens,true)) $found[]=$q;

    $flag=false; $reason=[];
    if(array_intersect($tokens,$obfus) && array_intersect($tokens,$eval)){ $flag=true; $reason[]='obfus+eval'; }
    if(!$flag && array_intersect($tokens,$exec)){ $flag=true; $reason[]='exec'; }
    if(!$flag && array_intersect($tokens,$upload)){ $flag=true; $reason[]='upload'; }
    foreach($names as $n) if(stripos($content,$n)!==false){ $flag=true; $reason[]='webshell'; }

    return $flag ? "tokens:".implode('|',$found)." ; reasons:".implode('|',$reason) : '';
}

$subfolders = bayy_getSubfolders($BASE_DIR);
$scanTarget = isset($_GET['folder']) ? realpath($_GET['folder']) : $BASE_DIR;
if (!$scanTarget || strpos($scanTarget,$BASE_DIR)!==0) $scanTarget=$BASE_DIR;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Bayy ShellScanner</title>
<style>
body{margin:0;font-family:sans-serif;background:#121212;color:#eee;display:flex;flex-direction:column;min-height:100vh}
header{background:#1f1f1f;padding:10px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100}
.logo{font-weight:bold;color:#4da3ff}
.menu-btn{display:none;background:none;border:none;color:#fff;font-size:1.5rem;cursor:pointer}
.layout{flex:1;display:flex;min-height:0}
.sidebar{width:220px;background:#1b1b1b;padding:10px;overflow-y:auto}
.sidebar a{display:block;padding:8px;color:#fff;text-decoration:none;border-bottom:1px solid #333}
.sidebar a:hover{background:#333}
.content{flex:1;padding:15px;overflow-y:auto}
.button{padding:8px 14px;border-radius:6px;background:#4da3ff;color:#fff;text-decoration:none;font-weight:600;margin:0 4px}
.card{background:#1e1e1e;border:1px solid #333;padding:12px;margin:10px 0;border-radius:8px}
.card .path{display:block;color:#4da3ff;word-break:break-all;margin-bottom:6px}
.actions button{padding:5px 10px;margin-right:4px;border:none;border-radius:4px;cursor:pointer;font-size:0.85rem}
.view-btn{background:#fff;color:#111}
.del-btn{background:#e74c3c;color:#fff}
pre{white-space:pre-wrap;word-wrap:break-word;overflow-x:auto;background:#000;color:#ddd;padding:12px;border-radius:6px;max-height:60vh}
footer{text-align:center;color:#888;padding:20px 10px;margin-top:auto}
/* Mobile */
@media(max-width:768px){
  .layout{flex-direction:column}
  .sidebar{display:none;position:absolute;top:50px;left:0;width:200px;height:100%;z-index:99}
  .sidebar.show{display:block}
  .menu-btn{display:inline-block}
}
</style>
<script>
function toggleMenu(){document.querySelector('.sidebar').classList.toggle('show');}
</script>
</head>
<body>
<header>
  <div class="logo">Bayy ShellScanner</div>
  <div>
    <a href="?scan=1" class="button">Scan</a>
    <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="button" style="background:#333">Refresh</a>
    <button class="menu-btn" onclick="toggleMenu()">☰</button>
  </div>
</header>
<div class="layout">
  <nav class="sidebar">
    <a href="?scan=1">Scan Root Folder</a>
    <?php foreach($subfolders as $f): ?>
      <a href="?scan=1&folder=<?php echo urlencode($f); ?>">Scan <?php echo basename($f); ?></a>
    <?php endforeach; ?>
  </nav>
  <main class="content">
    <?php if (!empty($msg_delete)) echo "<div class='card'>$msg_delete</div>"; ?>
    <?php if (!empty($preview_error)) echo "<div class='card'>$preview_error</div>"; ?>

    <?php
    if (isset($preview_content,$preview_path)) {
        echo "<div class='card'><strong>Preview: ".htmlspecialchars(str_replace($BASE_DIR,'',$preview_path))."</strong>";
        echo "<pre>".htmlspecialchars($preview_content)."</pre></div>";
    }

    if (isset($_GET['scan'])) {
        $list = bayy_listPhpFiles($scanTarget); $found=0;
        foreach ($list as $value) {
            list($tokens,$content) = bayy_readFileData($value);
            $analysis = bayy_analyzeFile($tokens,$content);
            if ($analysis!=='') {
                $found++;
                $rel=str_replace($BASE_DIR,'',$value);
                $url=ltrim(str_replace(DIRECTORY_SEPARATOR,'/',$rel),'/');
                $v=$_SERVER['PHP_SELF'].'?view='.urlencode($value);
                $d=$_SERVER['PHP_SELF'].'?delete='.urlencode($value);
                echo "<div class='card'>";
                echo "<a class='path' href='".htmlspecialchars($url)."' target='_blank'>".htmlspecialchars($rel)."</a>";
                echo "<div>Found: ".htmlspecialchars($analysis)."</div>";
                echo "<div class='actions'>";
                echo "<a href='$v'><button class='view-btn'>Preview</button></a>";
                echo "<a href='$d' onclick='return confirm(\"Yakin hapus?\");'><button class='del-btn'>Delete</button></a>";
                echo "</div></div>";
            }
        }
        if ($found===0) echo "<div class='card'>No detections found in ".htmlspecialchars($scanTarget)."</div>";
    }
    ?>
  </main>
</div>
<footer>&copy; <?php echo date('Y'); ?> Bayy ShellScanner</footer>
</body>
</html>