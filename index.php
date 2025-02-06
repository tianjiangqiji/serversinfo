<?php
// 启动会话
session_start();

// 加载哈希密码
$passwords = json_decode(file_get_contents('data/passwords.json'), true);
$stored_hash = $passwords['password_hash'];

// 检查用户是否已经登录
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // 用户已经登录，继续加载页面内容
} else {
    // 用户未登录，显示登录表单
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 检查提交的密码是否正确
        if (isset($_POST['password']) && password_verify($_POST['password'], $stored_hash)) {
            // 密码正确，设置会话变量
            $_SESSION['loggedin'] = true;
            // 重定向到当前页面，避免表单重复提交
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            // 密码错误，显示错误消息
            $error = '密码错误';
        }
    }
    // 显示登录表单
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>服务器统一管理</title>
        <link rel="icon" href="/serversinfo/data/favicon.png" type="image/png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <h2 class="text-center">登录</h2>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="password" class="form-label">密码</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">登录</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit; // 停止执行后续代码，直到用户登录
}

// 用户已登录，继续加载页面内容
$servers = json_decode(file_get_contents('data/info.json'), true);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>服务器管理器</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="/serversinfo/data/favicon.png" type="image/png">
</head>
<body>
    <!-- 切换服务器列表按钮 -->
    <button id="toggle-server-list" class="btn btn-primary" onclick="toggleServerList()">☰</button>
    <div class="container-fluid">
        <div class="row">
            <!-- 左侧服务器列表 -->
            <div class="col-3" id="server-list-container">
                <h4>服务器</h4>
                <ul class="list-group mb-3" id="server-list">
                    <?php foreach ($servers as $server => $data): ?>
                        <?php if (isset($data['ip'])): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center" onclick="loadServer('<?php echo $server; ?>')">
                                <?php echo $data['name']; ?>
                                <button class="btn btn-sm btn-danger" onclick="deleteServer('<?php echo $server; ?>')">删除</button>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <button class="btn btn-primary w-100" onclick="addServer()">+ 添加服务器</button>
                <button class="btn btn-secondary w-100 mt-3" onclick="openUpdatePasswordModal()">设置新密码</button>
            </div>
            <!-- 右侧详细信息区域 -->
            <div class="col-9" id="server-info">
                <!-- 欢迎界面 -->
                <div id="welcome-message" class="text-center text-muted mt-5">
                    <h3>欢迎使用服务器管理器</h3>
                    <p>请从左侧选择一个服务器来查看详细信息</p>
                </div>
                <!-- 服务器信息 -->
                <div id="server-details" style="display: none;"></div>
                <!-- 网站列表 -->
                <div id="website-list" style="display: none;"></div>
            </div>
        </div>
    </div>
    
    <!-- 添加服务器模态框 -->
    <div class="modal fade" id="addServerModal" tabindex="-1" aria-labelledby="addServerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addServerModalLabel">添加服务器</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="关闭"></button>
                </div>
                <div class="modal-body">
                    <form id="addServerForm">
                        <div class="mb-3">
                            <label for="serverIp" class="form-label">IP地址</label>
                            <input type="text" class="form-control" id="serverIp" required>
                        </div>
                        <div class="mb-3">
                            <label for="serverName" class="form-label">名称</label>
                            <input type="text" class="form-control" id="serverName" required>
                        </div>
                        <div class="mb-3">
                            <label for="serverNote" class="form-label">备注</label>
                            <input type="text" class="form-control" id="serverNote">
                        </div>
                        <div class="mb-3">
                            <label for="serverPanelUrl" class="form-label">控制面板URL</label>
                            <input type="text" class="form-control" id="serverPanelUrl">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" onclick="saveServer()">保存</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 添加网站模态框 -->
    <div class="modal fade" id="addWebsiteModal" tabindex="-1" aria-labelledby="addWebsiteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addWebsiteModalLabel">添加网站</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="关闭"></button>
                </div>
                <div class="modal-body">
                    <form id="addWebsiteForm">
                        <div class="mb-3">
                            <label for="websiteName" class="form-label">名称</label>
                            <input type="text" class="form-control" id="websiteName" required>
                        </div>
                        <div class="mb-3">
                            <label for="websiteUrl" class="form-label">URL</label>
                            <input type="text" class="form-control" id="websiteUrl" required>
                        </div>
                        <div class="mb-3">
                            <label for="websiteNote" class="form-label">备注</label>
                            <input type="text" class="form-control" id="websiteNote">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" onclick="saveWebsite()">保存</button>
                </div>
            </div>
        </div>
    </div>
    <!-- 编辑服务器模态框 -->
    <div class="modal fade" id="editServerModal" tabindex="-1" aria-labelledby="editServerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editServerModalLabel">编辑服务器</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="关闭"></button>
                </div>
                <div class="modal-body">
                    <form id="editServerForm">
                        <div class="mb-3">
                            <label for="editServerIp" class="form-label">IP地址</label>
                            <input type="text" class="form-control" id="editServerIp" required>
                        </div>
                        <div class="mb-3">
                            <label for="editServerName" class="form-label">名称</label>
                            <input type="text" class="form-control" id="editServerName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editServerNote" class="form-label">备注</label>
                            <input type="text" class="form-control" id="editServerNote">
                        </div>
                        <div class="mb-3">
                            <label for="editServerPanelUrl" class="form-label">控制面板URL</label>
                            <input type="text" class="form-control" id="editServerPanelUrl">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" onclick="updateServer()">保存</button>
                </div>
            </div>
        </div>
    </div>
    <!-- 编辑网站模态框 -->
    <div class="modal fade" id="editWebsiteModal" tabindex="-1" aria-labelledby="editWebsiteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editWebsiteModalLabel">编辑网站</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="关闭"></button>
                </div>
                <div class="modal-body">
                    <form id="editWebsiteForm">
                        <div class="mb-3">
                            <label for="editWebsiteName" class="form-label">名称</label>
                            <input type="text" class="form-control" id="editWebsiteName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editWebsiteUrl" class="form-label">URL</label>
                            <input type="text" class="form-control" id="editWebsiteUrl" required>
                        </div>
                        <div class="mb-3">
                            <label for="editWebsiteNote" class="form-label">备注</label>
                            <textarea class="form-control" id="editWebsiteNote" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" onclick="updateWebsite()">保存</button>
                </div>
            </div>
        </div>
    </div>
    <!-- 设置新密码模态框 -->
    <div class="modal fade" id="updatePasswordModal" tabindex="-1" aria-labelledby="updatePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePasswordModalLabel">设置新密码</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="关闭"></button>
                </div>
                <div class="modal-body">
                    <form id="updatePasswordForm">
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">新密码</label>
                            <input type="password" class="form-control" id="newPassword" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" onclick="updatePassword()">保存</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- 自定义JS -->
    <script src="js/script.js"></script>
    <script>
        function toggleServerList() {
            const serverListContainer = document.getElementById('server-list-container');
            if (serverListContainer.style.display === 'none' || serverListContainer.style.display === '') {
                serverListContainer.style.display = 'block';
            } else {
                serverListContainer.style.display = 'none';
            }
        }

        function openUpdatePasswordModal() {
            new bootstrap.Modal(document.getElementById('updatePasswordModal')).show();
        }

        function updatePassword() {
            const newPassword = document.getElementById('newPassword').value;

            if (newPassword) {
                const formData = new FormData();
                formData.append('new_password', newPassword);

                fetch('update_password.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    const updatePasswordModal = bootstrap.Modal.getInstance(document.getElementById('updatePasswordModal'));
                    updatePasswordModal.hide();
                })
                .catch(error => {
                    console.error('Error updating password:', error);
                    alert('更新密码失败，请重试。');
                });
            } else {
                alert('请输入新密码。');
            }
        }
    </script>
</body>
</html>