<?php
$tpl = $PAGE->start();

try {
    if ($_POST['add']) {
        $pool = str::splitHostAndPort(trim($_POST['pool']));
        $number = (int)$_POST['number'];
        $username = trim($_POST['username']);

        $config = new SimConfig();
        $config->setStratumServer($pool['host'], $pool['port']);
        $config->setClientsNumber($number);
        $config->setUserName($username);
    
        $config->saveToDb();
        $tpl->assign('actionResult', '添加成功');

        $tpl->display('tpl:action_result');

        $service = new SimService($config);
        $service->startService();
    }
    else {
        $meta = new MetaData('pool_server');
        $poolList = $meta->keyValues();
        $tpl->assign('poolList', $poolList);

        $tpl->display('tpl:add_form');
    }
}
catch (Exception $e) {
    $errMsg = $e->getMessage();
    $tpl->assign('errMsg', $errMsg);
    
    $tpl->display('tpl:action_result');
}

