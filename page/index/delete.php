<?php
Admin::checkLogin();
$tpl = $PAGE->start();

$id = (int) $_GET['id'];

try {
    $config = new SimConfig();
    $config->loadFromDb($id);
    
    $service = new SimService($config);
    
    $actionResult = $service->destory();
    $tpl->assign('actionResult', '删除成功');
}
catch (Exception $e) {
    $errMsg = $e->getMessage();
    $tpl->assign('errMsg', $errMsg);
}

$tpl->display('tpl:action_result');

