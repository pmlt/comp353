{extends file="sems_master.tpl"}

{block name='title'}{$conf.name}{/block}
{block name='content'}

<h2>Create a new message for {$event.title}</h2>

{include file="message/form.tpl" data=$smarty.post}

{/block}
