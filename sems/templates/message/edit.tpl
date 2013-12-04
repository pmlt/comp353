{extends file="event/master.tpl"}

{block name='content_title' append=true} - Modify {$message.title}{/block}
{block name='content'}

{include file="message/form.tpl" data=array_merge($message, $smarty.post)}

{/block}
