<?php
$tpl = $PAGE->start();

$id = (int) $_GET['id'];

try {
    if ($_POST['edit']) {
        $pool = str::splitHostAndPort(trim($_POST['pool']));
        $number = (int)$_POST['number'];
        $username = trim($_POST['username']);

        $config = new SimConfig();
        $config->loadFromDb($id);
        $config->setStratumServer($pool['host'], $pool['port']);
        $config->setClientsNumber($number);
        $config->setUserName($username);
    
        $config->saveToDb($id);
        $tpl->assign('actionResult', '修改成功');

        $tpl->display('tpl:action_result');
    }
    else {
        $meta = new MetaData('pool_server');
        $poolList = $meta->keyValues();
        $tpl->assign('poolList', $poolList);

        $config = new SimConfig();
        $config->loadFromDb($id);
        $tpl->assign('config', $config);

        $tpl->display('tpl:edit_form');
    }
}
catch (Exception $e) {
    $errMsg = $e->getMessage();
    $tpl->assign('errMsg', $errMsg);
    
    $tpl->display('tpl:action_result');
}

