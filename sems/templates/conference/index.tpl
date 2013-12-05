{extends file="conference/master.tpl"}

{block name='content_title'}Welcome!{/block}
{block name='content'}

<p>{$conf.description}</p>

<div class="three-columns-layout">

  <div class="col minor">
    
    <h3>In the news...</h3>

    {include file="message/listing.tpl"}

  </div>

  <div class="col major">

    <h3>Scheduled events</h3>

    <dl>
    {foreach $events as $e}
      <dt><a href="{sems_event_url($conf.conference_id,$e.event_id)}">{$e.title}</a></dt>
      <dd>
        <span class="datetime">{sems_datetime($e.start_date)} to {sems_datetime($e.end_date)}</span>
        {$e.description|truncate:60:'...'}</dd>
    {/foreach}
    </dl>
  </div>

  <div class="col minor">

    <h3>Conference chair</h3>
    <a href="{sems_profile_url($chair->UserId)}">{$chair->fullname()}</a>

    <h3>Topics of interest</h3>
    {include file="ui/topic_hierarchy.tpl"}
  </div>
</div>
<div></div>

{/block}
