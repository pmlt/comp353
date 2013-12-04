{extends file="event/master.tpl"}

{block name='content_title' append=true} - Modify details{/block}
{block name='content'}

{include file="event/form.tpl" data=array_merge($event, $smarty.post)}

{/block}
