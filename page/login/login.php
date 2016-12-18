<?php
session_start();
$tpl = $PAGE->start();

if ($_POST['login']) {
    $password = $_POST['password'];

    if (POOLSIM_ADMIN_PASSWORD === $password) {
        $_SESSION['poolsim_admin_login'] = true;
        
        header('Location: index.index.html');
    }
    else {
        $_SESSION['poolsim_admin_login'] = false;

        $tpl->assign('errMsg', '密码错误');
        $tpl->display('tpl:login_form');
    }

}
else {
    $tpl->display('tpl:login_form');
}
