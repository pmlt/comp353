{extends file="event/master.tpl"}

{block name='content'}

<p>{$event.description}</p>

<div class="three-columns-layout">

  <div class="col">
  <h3>In the news...</h3>

    <dl>
    {foreach $messages as $message}
    <dt><a href="{sems_message_url($conf.conference_id, $event.event_id, $message.message_id)}">{$message.title}</a></dt>
    <dd><span class="datetime">{sems_datetime($message.publish_date)}</span> {$message.excerpt} <a class="readmore" href="{sems_message_url($conf.conference_id, $event.event_id, $message.message_id)}">[Read more]</a></dd>
    {/foreach}
    </dl>

  </div>
  <div class="col">
  <h3>Committee</h3>
  <dl>
    <dt>Program Chair</dt>
    <dd><a href="{sems_profile_url($chair->UserId)}">{$chair->fullname()}</a></dd>
    {foreach $committee as $member}
    <dt>Reviewer<dt>
    <dd><a href="{sems_profile_url($member.user_id)}">{$member.first_name} {$member.last_name}</a></dd>
    {/foreach}
  </dl>

  {if $reviews}
  <h3>Your Reviews</h3>

  <dl>
    {foreach $reviews as $review}
    <dt><a href="{sems_review_url($conf.conference_id, $event.event_id, $review.review_id)}">{$review.title}</a></dt>
    <dd>{if $review.score > 0}Your score: {$review.score}/10{else}Not yet scored.{/if}</dd>
    {/foreach}
  </dl>
  {/if}

  {if $papers}
  <h3>Papers</h3>

  <dl>
    {foreach $papers as $paper}
    <dt><a href="{sems_paper_url($conf.conference_id, $event.event_id, $paper.paper_id)}">{$paper.title}</a></dt>
    <dd>By <a href="{sems_profile_url($paper.user_id)}">{$paper.user_title|ucfirst}. {$paper.first_name} {$paper.last_name}</a></dd>
    {/foreach}
  </dl>
  {/if}

  </div>
  <div class="col">

  <h3>Current status:</h3>
    <p>{sems_event_state_str(sems_event_state($event))}</p>

  <h3>Full Schedule</h3>
    <dl>
      <dt>{sems_event_state_name_str('submit')}</dt>
      <dd><span class="datetime">
        {sems_datetime($event.submit_start_date)} to 
        {sems_datetime($event.submit_end_date)}</span></dd>
      <dt>{sems_event_state_name_str('auction')}</dt>
      <dd><span class="datetime">
        {sems_datetime($event.auction_start_date)} to 
        {sems_datetime($event.auction_end_date)}</span></dd>

      <dt>{sems_event_state_name_str('review')}</dt>
      <dd><span class="datetime">
        {sems_datetime($event.review_start_date)} tp
        {sems_datetime($event.review_end_date)}</span></dd>

      <dt>{sems_event_state_name_str('decision')}</dt>
      <dd><span class="datetime">
        {sems_datetime($event.decision_date)}</span></dd>
      <dt>{sems_event_state_name_str('meet')}</dt>
      <dd><span class="datetime">
        {sems_datetime($event.start_date)} to
        {sems_datetime($event.end_date)}</span></dd>
    </dl>

  </div>
</div>
<div></div>

{*
<h3>Topics of interest</h3>
{include file="ui/topic_hierarchy.tpl"}
*}

{/block}
