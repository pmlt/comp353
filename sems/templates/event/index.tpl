{extends file="sems_master.tpl"}

{block name='title'}{$conf.name}{/block}
{block name='content'}
<h2>{$event.title}</h2>

<p>{$event.description}</p>

<p>Topics of interest are: {include file="ui/topic_hierarchy.tpl"}</p>

<p>Program chair: {$chair->fullname()}</p>

<p>Current status: {sems_event_state_str(sems_event_state($event))}</p>

<p>Schedule for this event:</p>

<table>
  <tr>
    <th>Phase</th><th>Start</th><th>End</th>
  </tr>
  <tr>
    <td>Accepting paper submissions</td>
    <td>{sems_datetime($event.submit_start_date)}</td>
    <td>{sems_datetime($event.submit_end_date)}</td>
  </tr>
  <tr>
    <td>Bidding period for reviewers</td>
    <td>{sems_datetime($event.auction_start_date)}</td>
    <td>{sems_datetime($event.auction_end_date)}</td>
  </tr>
  <tr>
    <td>Review period</td>
    <td>{sems_datetime($event.review_start_date)}</td>
    <td>{sems_datetime($event.review_end_date)}</td>
  </tr>
  <tr>
    <td>Final decision</td>
    <td colspan="2">{sems_datetime($event.decision_date)}</td>
  </tr>
  <tr>
    <td>Meeting</td>
    <td>{sems_datetime($event.start_date)}</td>
    <td>{sems_datetime($event.end_date)}</td>
  </tr>
</table>

<p>Messages:</p>

{foreach $messages as $message}
<h3><a href="{sems_message_url($conf.conference_id, $event.event_id, $message.message_id)}">{$message.title}</a></h3>
<p>{$message.excerpt}</p>

{/foreach}

{/block}
