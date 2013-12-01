{extends file="sems_master.tpl"}

{block name='title'}{$conf.name}{/block}
{block name='content'}

<h2>Edit this conference</h2>

{include file="conference/form.tpl" data=array_merge($conf, $smarty.post)}

{/block}
