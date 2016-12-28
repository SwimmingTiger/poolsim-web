{include file="tpl:comm.head" title="矿机模拟器管理面板"}
<style>
    #poolsim_list td {
        padding: 10px 20px;
    }
</style>
<div id="title" class="tp">
    <h3>矿机模拟器管理面板 | <a href="{$cid}.add.{$BID}">新增</a> | <a href="?page={$page}&r={time()}">刷新</a><a style="float:right" href="login.exit.{$BID}">退出</a></h3>
</div>
<div id="poolsim_list"><table>
    <tr>
        <th>ID</th>
        <th>矿池</th>
        <th>子账户</th>
        <th>矿机数</th>
        <th>状态</th>
        <th>进程ID</th>
        <th>连接数</th>
        <th>在线时间</th>
        <th>操作</th>
    </tr>
    {foreach $serviceList as $service}
        <tr>
            <td>{$service->id|code}</td>
            <td>{$service->poolName|code}</td>
            <td>{$service->config->username|code}</td>
            <td>{$service->config->number_clients|code}</td>
            <td><span title="{$service->status.noticeString}">{$service->status.status|code}</span></td>
            <td>{$service->status.pid|code}</td>
            <td>{$service->status.connections|code}</td>
            <td>{$service->status.uptime|code}</td>
            <td>
                <a href="{$CID}.start.{$BID}?id={$service->id|code}">启</a>
                <a href="{$CID}.stop.{$BID}?id={$service->id|code}">停</a>
                <a href="{$CID}.edit.{$BID}?id={$service->id|code}{if $service->isCustomPool}&custom_pool=1{/if}">改</a>
                <a href="{$CID}.delete.{$BID}?id={$service->id|code}" onclick="return confirm('确定删除？')">删</a>
            </td>
        </tr>
    {/foreach}
</table></div>
<hr/>
<div id="pager">
    {if $page > 1}<a href="?page={$page-1}">上一页</a>{/if}
    {$page}/{$maxPage}页
    {if $page < $maxPage}<a href="?page={$page+1}">下一页</a>{/if}
</div>
{include file="tpl:comm.foot"}
