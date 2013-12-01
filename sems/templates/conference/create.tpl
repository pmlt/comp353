{extends file="sems_master.tpl"}

{block name='content'}

<h2>Create a new conference</h2>

{include file="conference/form.tpl" data=$smarty.post}

{/block}
