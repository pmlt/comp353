{extends file="event/master.tpl"}

{block name='content'}

<p>{$event.description}</p>

<h3>Program chair</h3>
<p><a href="{sems_profile_url($chair->UserId)}">{$chair->fullname()}</a></p>

<h3>Topics of interest</h3>
{include file="ui/topic_hierarchy.tpl"}

<h3>Current status</h3>
<p>{sems_event_state_str(sems_event_state($event))}</p>

<h3>Schedule for this event:</h3>

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

<h3>Messages</h3>

{foreach $messages as $message}
<h4><a href="{sems_message_url($conf.conference_id, $event.event_id, $message.message_id)}">{$message.title}</a></h4>
<p>{$message.excerpt}</p>

{/foreach}

{/block}
