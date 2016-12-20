{include file="tpl:comm.head" title="矿机模拟器管理面板"}
<style>
</style>
<div id="title" class="tp">
    <h3>矿机模拟器管理面板 | <a href="{$CID}.index.{$BID}">返回首页</a></h3>
</div>
<div id="add_form">
    <form action="{$CID}.{$PID}.{$BID}" method="post">
        <p>
            矿池：
            {if $smarty.get.custom_pool}
                <input type="text" name="pool" value="{$smarty.post.pool|code}" />
                <a href="?custom_pool=0">预定义</a>
            {else}
            <select name="pool">
                {foreach $poolList as $name=>$address}
                    <option value="{$address|code}">{$name|code}</option>
                {/foreach}
            </select>
            <a href="?custom_pool=1">自定义</a>
            {/if}
        </p>
        <p>子账户名：<input type="text" name="username" value="{$smarty.post.username|code}" /></p>
        <p>矿机数：<input type="text" name="number" value="{$smarty.post.number|code}" /></p>
        <p><input type="submit" name="add" value="提交" /></p>
    </form>
</div>
<div id="error_message" class="failure">
    {$errMsg|code}
</div>
{include file="tpl:comm.foot"}
