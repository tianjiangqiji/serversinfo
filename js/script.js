let servers = {};
// 加载服务器数据
fetch('data/info.json')
    .then(response => response.json())
    .then(data => {
        servers = data;
    })
    .catch(error => {
        console.error('Error loading server data:', error);
    });

function fetchData(url, options = {}) {
    return fetch(url, options)
        .then(response => response.json())
        .catch(error => {
            console.error('Error:', error);
            throw error;
        });
}

function handleResponse(response, successMessage, errorMessage) {
    if (response.status === 'success') {
        console.log(successMessage);
        location.reload();
    } else {
        alert(errorMessage);
    }
}

// 解析 ini 格式的数据
function parseIniData(data) {
    const result = {};
    const lines = data.split('\n');
    let currentSection = null;

    lines.forEach(line => {
        line = line.trim();
        if (line.startsWith('[') && line.endsWith(']')) {
            currentSection = line.slice(1, -1);
            result[currentSection] = {};
        } else if (currentSection && line.includes('=')) {
            const [key, value] = line.split('=').map(part => part.trim());
            result[currentSection][key] = value;
        }
    });

    return result;
}
// 加载服务器信息
function loadServer(serverName) {
    // 隐藏欢迎界面
    document.getElementById('welcome-message').style.display = 'none';

    // 显示加载提示
    document.getElementById('server-details').innerHTML = "<div class='loading'>Loading server details...</div>";
    document.getElementById('website-list').innerHTML = "<div class='loading'>Loading website list...</div>";

    // 显示服务器信息和网站列表容器
    document.getElementById('server-details').style.display = 'block';
    document.getElementById('website-list').style.display = 'block';

    // 加载服务器信息
    fetch('get_server_info.php?server=' + serverName)
        .then(response => response.text())
        .then(data => {
            document.getElementById('server-details').innerHTML = data;
            document.getElementById('server-details').classList.add('fade-in');
        });

    // 加载网站列表
    fetch('get_website_list.php?server=' + serverName)
        .then(response => response.text())
        .then(data => {
            document.getElementById('website-list').innerHTML = data;
            document.getElementById('website-list').classList.add('fade-in');
        });
}


// 删除网站
function deleteItem(itemName, type) {
    if (confirm(`确定要删除此 ${itemName} 吗？`)) {
        fetchData('delete_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ itemName, type })
        }).then(response => handleResponse(response, '删除成功', '删除失败'));
    }
}

// 删除服务器
function deleteServer(serverName) {
    if (confirm(`确定要删除服务器 ${serverName} 吗？`)) {
        const formData = new FormData();
        formData.append('delete_server', true);
        formData.append('server_name', serverName);

        fetchData('save_data.php', {
            method: 'POST',
            body: formData
        }).then(response => handleResponse(response, '删除服务器成功', '删除服务器失败'));
    }
}

// 更新服务器信息
function updateServer() {
    const serverName = document.getElementById('editServerForm').dataset.serverName;
    const ip = document.getElementById('editServerIp').value;
    const name = document.getElementById('editServerName').value;
    const note = document.getElementById('editServerNote').value;
    const panelUrl = document.getElementById('editServerPanelUrl').value;

    console.log('Updating server:', { serverName, ip, name, note, panelUrl }); // 调试信息

    if (ip && name) {
        const formData = new FormData();
        formData.append('edit_server', true);
        formData.append('server_name', serverName);
        formData.append('ip', ip);
        formData.append('name', name);
        formData.append('note', note);
        formData.append('panel_url', panelUrl);

        fetchData('save_data.php', {
            method: 'POST',
            body: formData
        }).then(response => handleResponse(response, '服务器更新成功', '更新服务器失败，请重试。'));
    } else {
        alert('请填写所有必填字段。');
    }
}

// 更新网站信息
function updateWebsite() {
    const serverName = document.getElementById('editWebsiteForm').dataset.serverName;
    const websiteId = document.getElementById('editWebsiteForm').dataset.websiteId;
    const name = document.getElementById('editWebsiteName').value;
    const url = document.getElementById('editWebsiteUrl').value;
    const note = document.getElementById('editWebsiteNote').value;

    console.log('Updating website:', { serverName, websiteId, name, url, note }); // 调试信息

    if (name && url) {
        const formData = new FormData();
        formData.append('edit_website', true);
        formData.append('server_name', serverName);
        formData.append('website_id', websiteId);
        formData.append('website_name', name);
        formData.append('website_url', url);
        formData.append('website_note', note);

        fetchData('save_data.php', {
            method: 'POST',
            body: formData
        }).then(response => handleResponse(response, '网站更新成功', '更新网站失败，请重试。'));
    } else {
        alert('请填写所有必填字段。');
    }
}

// 打开添加服务器模态框
function addServer() {
    // 重置表单
    document.getElementById('addServerForm').reset();
    // 显示模态框
    new bootstrap.Modal(document.getElementById('addServerModal')).show();
}


// 打开添加网站模态框
function addWebsite(serverName) {
    // 重置表单
    document.getElementById('addWebsiteForm').reset();
    // 设置当前服务器名称
    document.getElementById('addWebsiteForm').dataset.serverName = serverName;
    // 显示模态框
    new bootstrap.Modal(document.getElementById('addWebsiteModal')).show();
}

// 保存服务器信息
function saveServer() {
    const ip = document.getElementById('serverIp').value;
    const name = document.getElementById('serverName').value;
    const note = document.getElementById('serverNote').value;
    const panelUrl = document.getElementById('serverPanelUrl').value;

    console.log('Saving server:', { ip, name, note, panelUrl }); // 调试信息

    if (ip && name) {
        const formData = new FormData();
        formData.append('add_server', true);
        formData.append('ip', ip);
        formData.append('name', name);
        formData.append('note', note);
        formData.append('panel_url', panelUrl);

        fetchData('save_data.php', {
            method: 'POST',
            body: formData
        }).then(response => handleResponse(response, '服务器保存成功', '保存服务器失败，请重试。'));
    } else {
        alert('请填写所有必填字段。');
    }
}

// 保存网站信息
function saveWebsite() {
    const serverName = document.getElementById('addWebsiteForm').dataset.serverName;
    const name = document.getElementById('websiteName').value;
    const url = document.getElementById('websiteUrl').value;
    const note = document.getElementById('websiteNote').value;

    if (name && url) {
        const formData = new FormData();
        formData.append('add_website', true);
        formData.append('server_name', serverName);
        formData.append('website_name', name);
        formData.append('website_url', url);
        formData.append('website_note', note);

        fetchData('save_data.php', {
            method: 'POST',
            body: formData
        }).then(response => handleResponse(response, '网站保存成功', '保存网站失败，请重试。'));
    } else {
        alert('请填写所有必填字段。');
    }
}

// 打开编辑服务器模态框
function editServer(serverName) {
    // 获取服务器数据
    const server = servers[serverName];
    if (server) {
        // 填充表单数据
        document.getElementById('editServerIp').value = server.ip;
        document.getElementById('editServerName').value = server.name;
        document.getElementById('editServerNote').value = server.note;
        document.getElementById('editServerPanelUrl').value = server.panel_url;

        // 设置当前服务器名称
        document.getElementById('editServerForm').dataset.serverName = serverName;

        // 显示模态框
        new bootstrap.Modal(document.getElementById('editServerModal')).show();
    }
}

// 打开编辑网站模态框
function editWebsite(serverName, websiteId) {
    console.log('Editing website:', serverName, websiteId); // 调试信息

    // 获取网站数据
    const website = {
        name: servers[serverName][`${websiteId}_name`],
        url: servers[serverName][`${websiteId}_url`],
        note: servers[serverName][`${websiteId}_note`]
    };

    if (website.name && website.url) {
        console.log('Website data:', website); // 调试信息

        // 填充表单数据
        document.getElementById('editWebsiteName').value = website.name;
        document.getElementById('editWebsiteUrl').value = website.url;
        document.getElementById('editWebsiteNote').value = website.note || '';

        // 设置当前服务器名称和网站 ID
        document.getElementById('editWebsiteForm').dataset.serverName = serverName;
        document.getElementById('editWebsiteForm').dataset.websiteId = websiteId;

        // 显示模态框
        new bootstrap.Modal(document.getElementById('editWebsiteModal')).show();
    } else {
        console.error('Servers data:', servers);
        console.error('Website not found:', serverName, websiteId); // 调试信息

    }
}
// 加载服务器列表
function loadServers() {
    fetch('get_servers.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('server-list').innerHTML = data;
        });
}

// 加载网站列表
function loadWebsites(server) {
    fetch(`get_websites.php?server=${server}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('website-list').innerHTML = data;
            document.getElementById('website-list').style.display = 'block';
        });
}