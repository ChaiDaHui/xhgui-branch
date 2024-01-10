<?php
date_default_timezone_set('UTC');
if (!defined('XHGUI_ROOT_DIR')) {
    require dirname(dirname(__FILE__)) . '/src/bootstrap.php';
}

// 执行参数
$curTime = time();          // 当前时间
$timeInterval = 1;          // 时间间隔不可修改，存在 sed 匹配异常问题

// 匹配日志时间
$fileName = date('Ymd', $curTime - $timeInterval * 60);
$pregTime = date('Y-m-d H:i', $curTime - $timeInterval * 60);
$filePath = '/tmp//xhprof/' . $fileName . '.xhprof';
$cmd = "sed -n '/^$pregTime/p' $filePath";

if (!file_exists($filePath)) return;

try {
    // 读取数据
    exec($cmd, $output);
    if (empty($output)) return;

    $container = Xhgui_ServiceContainer::instance();
    $saver = $container['saverMongo'];

    // 处理数据内容
    $dealNum = 0;
    foreach ($output as $logVal) {
        $data = json_decode(substr($logVal, 20), true);
        if ($data) {
            $saver->save($data);
        }
    }
} catch (Exception $e) {
    @file_put_contents('/tmp/xhprof/importError.log', json_encode(array(
        'time' => time(),
        'message' => $e->getMessage(),
        'trace' => $e->getTrace()
    )) . PHP_EOL, FILE_APPEND);
}