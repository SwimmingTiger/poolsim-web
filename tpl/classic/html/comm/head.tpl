{header content_type="text/html" charset="utf-8"}
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
	{if $time !== null}<meta http-equiv="refresh" content="{$time};url={if $url === null}{page::geturl()|code}{else}{$url|code}{/if}"/>{/if}
	{if $css === null}{$css=$PAGE->getTplUrl("css/{$PAGE->getCookie("css_{$PAGE->tpl}", "default")}.css")}{/if}
	<link rel="stylesheet" type="text/css" href="{$css|code}?r=3"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1" />
	<title>{$title|code}</title>
</head>
<body>
