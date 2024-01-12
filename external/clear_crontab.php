<?php
date_default_timezone_set('UTC');
if (!defined('XHGUI_ROOT_DIR')) {
    require dirname(dirname(__FILE__)) . '/src/bootstrap.php';
}

$time = time() - 86400 * 3;
$date = date('Ymd', $time); //当前日期-2
$fileDir = '/tmp/xhprof';//目录

if (is_dir($fileDir)) {
    //删除前3天的文件夹数据，保留.zip文件
    $files = array_diff(scandir($fileDir), array('.', '..'));
    foreach ($files as $file) {
        if (substr($file, 0, 8) >= $date) continue;
        unlink("{$fileDir}/{$file}");
    }
}

// 清除数据
$container = Xhgui_ServiceContainer::instance();
$res = $container['db']->results->remove(
    array('meta.request_date' => array('$lt' => date('Y-m-d', $time)))
);