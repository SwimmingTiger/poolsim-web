<?php
session_start();

$_SESSION['poolsim_admin_login'] = false;

header('Location: index.index.html');
