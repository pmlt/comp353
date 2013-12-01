{extends file="sems_master.tpl"}

{block name='content'}

<h2>Create a new event</h2>

{include file="event/form.tpl" data=$smarty.post}

{/block}
