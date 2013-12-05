{extends file="event/master.tpl"}

{block name='content_title' append=true} - Paper Review Assignment{/block}
{block name='content'}
<form method="post">

<h3>Assignments</h3>
<table>
<tr>
  <th>Paper</th>
  <th>Reviewer</th>
  <th>Revoke the review assignment.</th>
</tr>
{foreach $reviews as $review}
<tr>
  <td><a href="{sems_paper_url($conf.conference_id,$event.event_id,$review.paper_id)}">{$review.title}</a></td>
  <td>{$review.reviewer}</td>
  <td><button name="revoke" value="{$review.review_id}">Revoke</button></td>
</tr>
{foreachelse}
<tr colspan="3"><td>You have not created any review assignments yet.</td></tr>
{/foreach}
</table>

<h3>Unaddressed review bids</h3>
<table>
<tr>
  <th>Paper</th>
  <th>Bidder</th>
  <th>Assign a review.</th>
</tr>
{foreach $bids as $bid}
<tr>
  <td><a href="{sems_paper_url($conf.conference_id,$event.event_id,$bid.paper_id)}">{$bid.title}</a></td>
  <td>{$bid.bidder}</td>
  <td><button name="assign" value="{$bid.paper_id},{$bid.user_id}">Assign</button></td>
</tr>
{foreachelse}
<tr><td>Assigned all bids!</td></tr>
{/foreach}
</table>
</form>
{/block}
