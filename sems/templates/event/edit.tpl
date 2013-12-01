{extends file="sems_master.tpl"}

{block name='title'}{$conf.name}{/block}
{block name='content'}

<h2>Modify {$event.title}</h2>

{include file="event/form.tpl" data=array_merge($event, $smarty.post)}

{/block}
