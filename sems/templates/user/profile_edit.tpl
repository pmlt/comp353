{extends file="sems_master.tpl"}

{block name='content'}
{include file='user/form.tpl' user=$ident->UserData edit=true}
{/block}
