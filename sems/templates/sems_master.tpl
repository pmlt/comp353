{extends file="master.tpl"}
{block name='title'}{foreach $breadcrumb as $bc name='bc'}{$bc.label}{if !$smarty.foreach.bc.last} &gt;{/if}{/foreach}{/block}

{block name='head'}
<link href="{sems_root()}/css/ui-lightness/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<link href="{sems_root()}/css/sems.css" rel="stylesheet"/>
<script src="{sems_root()}/js/jquery-1.10.2.min.js"></script>
<script src="{sems_root()}/js/jquery-ui-1.10.3.custom.min.js"></script>
{* <script src="scripts/sems.js"></script> *}
{/block}

{block name='body'}
<div id="main-body">

{block name='header'}
<header>

<h1>{block name='header_title'}SEMS - Scholarly Event Management System{/block}</h1>

</header>
{/block}

<div id="breadcrumb">
{include file="ui/breadcrumb.tpl"}
</div>

<div id="content">
{block name='userbox'}
{include file="ui/userbox.tpl"}
{/block}
<h2>{block name='content_title'}{/block}</h2>
<div class="content-wrapper">
{block name='content'}{/block}
</div>

{block name='footer'}
<footer>
{include file='ui/footer.tpl'}
</footer>
{/block}

</div> {* Content wrapper *}
</div> {* Body *}

{/block}
