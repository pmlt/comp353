{extends file='sems_master.tpl'}

{block name='content'}
<h2>403 - Forbidden.</h2>

<p>We're sorry, but you do not have enough privilege to access this page.</p>

{if $reason}<p>Reason: {$reason}</p>{/if}

<p><a href="{sems_home_url()}">Click here to return to the homepage.</a></p>
{/block}
