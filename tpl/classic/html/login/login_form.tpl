{include file="tpl:comm.head" title="矿机模拟器管理面板"}
<style>
</style>
<div id="title" class="tp">
    <h3>矿机模拟器管理面板</h3>
</div>
<div id="add_form">
    <form action="{$CID}.{$PID}.{$BID}" method="post">
        <p>请输入管理员密码</p>
        <p><input type="password" name="password" value="{$smarty.post.password|code}" /></p>
        <p><input type="submit" name="login" value="登录" /></p>
    </form>
</div>
<div id="error_message" class="failure">
    {$errMsg|code}
</div>
{include file="tpl:comm.foot"}
