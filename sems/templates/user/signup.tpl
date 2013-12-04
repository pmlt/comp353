{extends file='sems_master.tpl'}

{block name='content_title'}Create a new SEMS account{/block}
{block name='content'}
{include file="user/form.tpl" user=$smarty.post}
{/block}
