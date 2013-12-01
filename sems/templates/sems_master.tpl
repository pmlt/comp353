{extends file="master.tpl"}
{block name='head'}
<link href="{sems_root()}/css/ui-lightness/jquery-ui-1.10.3.custom.css" rel="stylesheet">
{* <link href="css/sems.css" rel="stylesheet"/> *}
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script src="{sems_root()}/js/jquery-ui-1.10.3.custom.min.js"></script>
{* <script src="scripts/sems.js"></script> *}
{/block}

{block name='body'}
<h1>{block name='title'}SEMS - Scholarly Event Management System{/block}</h1>
{include file='ui/header.tpl'}
<div id="content">
{block name='content'}{/block}
</div>
{include file='ui/footer.tpl'}
{/block}
