{extends file="sems_master.tpl"}

{block name='title'}{$conf.name}{/block}
{block name='content'}

<p>{$conf.description}</p>

<p>Conference chair: {$chair->fullname()}</p>

<p>Scheduled events in this conference:</p>

<table>
{foreach $events as $e}
<tr>
  <td><a href="{sems_event_url($conf.conference_id,$e.event_id)}">{$e.title}</a></td><td>{$e.description}</td><td>{sems_datetime($e.start_date)}</td>
</tr>
{foreachelse}
<tr><td>Sorry, no events have been scheduled yet.</td></tr>
{/foreach}
</table>
{/block}
