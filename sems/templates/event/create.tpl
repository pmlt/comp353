{extends file="conference/master.tpl"}

{block name='content_title'}Create a new event{/block}
{block name='content'}

{include file="event/form.tpl" data=$smarty.post}

{/block}
