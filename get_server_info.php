<?php
$server = $_GET['server'];
$servers = json_decode(file_get_contents('data/info.json'), true);
$data = $servers[$server];

// 使用 Bootstrap 卡片组件美化输出
echo "<div class='card mb-4 shadow-sm'>";
echo "    <div class='card-header bg-primary text-white'>";
echo "        <h5 class='card-title mb-0'>{$data['name']}</h5>";
echo "    </div>";
echo "    <div class='card-body'>";
echo "        <div class='mb-3'>";
echo "            <label class='form-label text-muted small'>IP 地址</label>";
echo "            <p class='form-control-static fw-bold'>{$data['ip']}</p>";
echo "        </div>";
echo "        <div class='mb-3'>";
echo "            <label class='form-label text-muted small'>访问地址</label>";
echo "            <p class='form-control-static'>";
echo "                <a href='{$data['panel_url']}' target='_blank' class='text-decoration-none text-primary'>{$data['panel_url']}</a>";
echo "            </p>";
echo "        </div>";
echo "        <div class='mb-3'>";
echo "            <label class='form-label text-muted small'>注释</label>";
echo "            <p class='form-control-static text-secondary'>{$data['note']}</p>";
echo "        </div>";
echo "        <div class='d-flex justify-content-end gap-2'>";
echo "            <button class='btn btn-primary' onclick='editServer(\"$server\")'>编辑</button>";
echo "        </div>";
echo "    </div>";
echo "</div>";
?>