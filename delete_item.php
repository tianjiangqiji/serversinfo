<?php
$data = json_decode(file_get_contents('php://input'), true);
$itemName = $data['itemName'];
$type = $data['type'];

$servers = json_decode(file_get_contents('data/info.json'), true);

if ($type === 'server') {
    // 删除服务器
    unset($servers[$itemName]);
} elseif ($type === 'website') {
    // 删除网站
    foreach ($servers as $server => &$serverData) {
        foreach ($serverData as $key => $value) {
            if (strpos($key, $itemName) === 0) { // 删除以 $itemName 开头的键
                unset($serverData[$key]);
            }
        }
    }
}

// 保存到 info.json
file_put_contents('data/info.json', json_encode($servers, JSON_PRETTY_PRINT));

echo json_encode(['status' => 'success']);
?>