{extends file="event/master.tpl"}

{block name='content_title'}{$paper.title}{/block}
{block name='content'}
{include file="paper/file.tpl" with_decision=true}
{/block}
