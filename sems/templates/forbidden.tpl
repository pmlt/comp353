{extends file='sems_master.tpl'}

{block name='content_title'}403 - Forbidden.{/block}
{block name='content'}
<p>We're sorry, but you do not have enough privilege to access this page.</p>

{if $reason}<p><strong>Reason: </strong>{$reason}</p>{/if}

<p><a href="{sems_home_url()}">Click here to return to the homepage.</a></p>
{/block}
