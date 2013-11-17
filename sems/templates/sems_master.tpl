{extends file="master.tpl"}
{block name='head'}
<link href="css/sems.css" rel="stylesheet"/>
<script src="scripts/sems.js"></script>
{/block}

{block name='body'}
{include file='ui/header.tpl'}
<div id="content">
{block name='content'}{/block}
</div>
{include file='ui/footer.tpl'}
{/block}