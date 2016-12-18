<?php
$tpl = $PAGE->start();

$id = (int) $_GET['id'];

try {
    $config = new SimConfig();
    $config->loadFromDb($id);
    
    $service = new SimService($config);
    
    $actionResult = $service->stopService();
    $tpl->assign('actionResult', $actionResult);
}
catch (Exception $e) {
    $errMsg = $e->getMessage();
    $tpl->assign('errMsg', $errMsg);
}

$tpl->display('tpl:action_result');

