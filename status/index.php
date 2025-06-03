<?php
// --- Configuration ---
$apiKey = 'u763351-5254e887714410e3f1b94852'; // Replace with your main or read-only key
$monitorIDs = [
    '800580449',
    '800580189',
    '800580440'
];

$url = 'https://api.uptimerobot.com/v2/getMonitors';

$data = [
    'api_key' => $apiKey,
    'format' => 'json',
    'logs' => 1,
    'monitors' => implode('-', $monitorIDs)
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ],
];

$context  = stream_context_create($options);
$response = file_get_contents($url, false, $context);
$result = json_decode($response, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Status Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; padding: 20px; }
        .container { background: white; border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,0.1); padding: 20px; max-width: 900px; margin: auto; }
        .status-header { display: flex; align-items: center; gap: 10px; }
        .circle { width: 30px; height: 30px; border-radius: 50%; background: #4ade80; }
        h1 { margin: 0; }
        .service { margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
        .bar { display: inline-block; width: 5px; height: 20px; margin-right: 1px; border-radius: 2px; }
        .bar.up { background: #4ade80; }
        .bar.down { background: #94a3b8; }
        .name { font-weight: bold; }
        .uptime { color: #16a34a; }
        .status { color: #16a34a; font-weight: bold; margin-left: 10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="status-header">
        <div class="circle"></div>
        <h1>All systems <span class="uptime">Operational</span></h1>
    </div>

    <h2 style="margin-top: 40px;">Services</h2>

    <?php foreach ($result['monitors'] as $monitor): ?>
        <div class="service">
            <div>
                <span class="name"><?= htmlspecialchars($monitor['friendly_name']) ?></span> →
                <span class="uptime">| <?= $monitor['all_time_uptime_ratio'] ?>%</span>
                <span class="status">● Operational</span>
            </div>
            <div style="margin-top: 10px;">
                <?php
                    $logs = array_slice($monitor['logs'], -50); // last 50 logs
                    foreach ($logs as $log) {
                        $type = $log['type']; // 1 = down, 2 = up
                        $class = ($type == 1) ? 'down' : 'up';
                        echo "<span class='bar $class'></span>";
                    }
                ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
