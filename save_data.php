<?php
// 调试信息
error_log('Received POST data: ' . print_r($_POST, true));

$servers = json_decode(file_get_contents('data/info.json'), true);

function saveServers($servers) {
    file_put_contents('data/info.json', json_encode($servers, JSON_PRETTY_PRINT));
}

if (isset($_POST['add_server'])) {
    // 添加服务器逻辑
    $server_name = 'server' . (count($servers) + 1);
    $servers[$server_name] = [
        'ip' => $_POST['ip'],
        'name' => $_POST['name'],
        'note' => $_POST['note'],
        'panel_url' => $_POST['panel_url']
    ];
} elseif (isset($_POST['add_website'])) {
    // 添加网站逻辑
    $server_name = $_POST['server_name'];
    $website_id = $server_name . '_website' . (count($servers[$server_name]) - 3 + 1);
    $servers[$server_name][$website_id . '_name'] = $_POST['website_name'];
    $servers[$server_name][$website_id . '_url'] = $_POST['website_url'];
    $servers[$server_name][$website_id . '_note'] = $_POST['website_note'];
} elseif (isset($_POST['edit_server'])) {
    // 编辑服务器逻辑
    $server_name = $_POST['server_name'];
    $servers[$server_name]['ip'] = $_POST['ip'];
    $servers[$server_name]['name'] = $_POST['name'];
    $servers[$server_name]['note'] = $_POST['note'];
    $servers[$server_name]['panel_url'] = $_POST['panel_url'];
} elseif (isset($_POST['edit_website'])) {
    // 编辑网站逻辑
    $server_name = $_POST['server_name'];
    $website_id = $_POST['website_id'];
    $servers[$server_name][$website_id . '_name'] = $_POST['website_name'];
    $servers[$server_name][$website_id . '_url'] = $_POST['website_url'];
    $servers[$server_name][$website_id . '_note'] = $_POST['website_note'];
} elseif (isset($_POST['delete_website'])) {
    // 删除网站逻辑
    $server_name = $_POST['server_name'];
    $website_id = $_POST['website_id'];
    unset($servers[$server_name][$website_id . '_name']);
    unset($servers[$server_name][$website_id . '_url']);
    unset($servers[$server_name][$website_id . '_note']);
} elseif (isset($_POST['delete_server'])) {
    // 删除服务器逻辑
    $server_name = $_POST['server_name'];
    unset($servers[$server_name]);
}

saveServers($servers);

echo json_encode(['status' => 'success']);
?>