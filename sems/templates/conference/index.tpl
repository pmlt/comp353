{extends file="conference/master.tpl"}

{block name='content_title'}Welcome!{/block}
{block name='content'}

<p>{$conf.description}</p>

<p>Topics of interest are: {include file="ui/topic_hierarchy.tpl"}</p>

<p>Conference chair: <a href="{sems_profile_url($chair->UserId)}">{$chair->fullname()}</a></p>

<p>Scheduled events in this conference:</p>

<table>
<tr>
  <th>Event name</th>
  <th>Description</th>
  <th>Date</th>
</tr>
{foreach $events as $e}
<tr>
  <td><a href="{sems_event_url($conf.conference_id,$e.event_id)}">{$e.title}</a></td><td>{$e.description}</td><td>{sems_datetime($e.start_date)}</td>
</tr>
{foreachelse}
<tr><td>Sorry, no events have been scheduled yet.</td></tr>
{/foreach}
</table>
{/block}
