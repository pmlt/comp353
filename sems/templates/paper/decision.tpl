{extends file="sems_master.tpl"}

{block name='content'}
<h2>Final decisions for {$event.title} papers</h2>

{$decisions = sems_paper_decision_options()}

<form method="post">
{foreach $papers as $paper}
<h3>Paper: <a href="{sems_paper_url($conf.conference_id,$event.event_id,$paper.paper_id)}">{$paper.title}</a> (submitted by: <a href="{sems_profile_url($paper.user_id)}">{$paper.first_name} {$paper.last_name}</a>)</h3>
<table border="1">
  <tr>
    <th>Reviewer</th>
    <th>Originality</th>
    <th>Strong point</th>
    <th>Comments</th>
    <th>Comments for the Chair</th>
    <th>Score</th>
  </tr>
  {foreach $paper.reviews as $review}
  <tr>
    <td>{$review.first_name} {$review.last_name}</td>
    <td>{$review.originality}</td>
    <td>{$review.strong_point}</td>
    <td>{$review.review_comments}</td>
    <td>{$review.chair_comments}</td>
    <td>{$review.score}/10 (Confidence: {$review.confidence}/10)</td>
  </tr>
  {/foreach}
</table>
<p>Final decision: {html_options name="paper_`$paper.paper_id`" options=$decisions selected=$paper.decision}</p>
{/foreach}

<p><input type="submit" value="Save decisions" /></p>
{if $saved}<p class="success">Decisions saved. You can change your mind until {sems_datetime($event.decision_date)}.</p>{/if}
</form>


{/block}
