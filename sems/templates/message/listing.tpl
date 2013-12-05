
<dl>
  {foreach $messages as $message}
    {if $message.event_id}{$eid = $message.event_id}{else}{$eid = $event.event_id}{/if}
    <dt><a href="{sems_message_url($conf.conference_id, $eid, $message.message_id)}">{$message.title}</a></dt>
    <dd><span class="datetime">{sems_datetime($message.publish_date)}</span> {$message.excerpt} <a class="readmore" href="{sems_message_url($conf.conference_id, $eid, $message.message_id)}">[Read more]</a></dd>
  {/foreach}
</dl>
