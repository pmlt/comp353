{extends file="sems_master.tpl"}

{block name='title'}{$conf.name}{/block}
{block name='content'}

<h2>Modify {$message.title}</h2>

{include file="message/form.tpl" data=array_merge($message, $smarty.post)}

{/block}
