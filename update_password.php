<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    $passwords = ['password_hash' => $hashed_password];
    file_put_contents('data/passwords.json', json_encode($passwords, JSON_PRETTY_PRINT));

    echo "密码已更新成功。";
} else {
    echo "无效的请求方法。";
}
?>
