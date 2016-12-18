<?php
class Admin {
    public static function checkLogin() {
        session_start();

        if (true !== $_SESSION['poolsim_admin_login']) {
            header('Location: login.login.html');
            exit;
        }
    }
}
