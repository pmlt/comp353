{extends file="conference/master.tpl"}

{block name='content_title'}Create a new conference{/block}
{block name='content'}

{include file="conference/form.tpl" data=$smarty.post}

{/block}
