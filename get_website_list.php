<?php
$server = $_GET['server'];
$servers = json_decode(file_get_contents('data/info.json'), true);

echo "<h4>网站</h4>";
echo "<ul class='list-group'>";

if (isset($servers[$server])) {
    $serverData = $servers[$server];
    foreach ($serverData as $key => $value) {
        if (strpos($key, $server . '_website') === 0 && strpos($key, '_name') !== false) {
            $websiteId = str_replace('_name', '', $key);
            $name = $serverData[$websiteId . '_name'];
            $url = $serverData[$websiteId . '_url'];
            $note = $serverData[$websiteId . '_note'];

            echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                    <div class='flex-grow-1'>
                        <strong>{$name}</strong> - <a href='{$url}' target='_blank'>{$url}</a> - {$note}
                    </div>
                    <div class='actions'>
                        <button class='btn btn-sm btn-primary me-2' onclick='editWebsite(\"$server\", \"$websiteId\")'>编辑</button>
                        <button class='btn btn-sm btn-danger' onclick='deleteItem(\"$websiteId\", \"website\")'>删除</button>
                    </div>
                  </li>";
        }
    }
} else {
    echo "<li class='list-group-item'>未找到服务器。</li>";
}

echo "</ul>";
echo "<button class='btn btn-primary mt-3' onclick='addWebsite(\"$server\")'>+ 添加网站</button>";
?>