{extends file="event/master.tpl"}

{block name='content_title' append=true} - Post a new message{/block}
{block name='content'}

{include file="message/form.tpl" data=$smarty.post}

{/block}
