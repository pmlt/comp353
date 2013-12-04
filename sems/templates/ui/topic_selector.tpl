
{foreach $hierarchy as $ca => $topic_list}
<p class="topic-category">{$ca}</p>
<ul>
  {foreach $topic_list as $topic}
  <li><input id="topic_{$topic.topic_id}" type="checkbox" name="topic_{$topic.topic_id}" value="{$topic.topic_id}" {if $topic.selected}checked="checked"{/if} /><label for="topic_{$topic.topic_id}">{$topic.name}</label></li>
  {/foreach}
</ul>
{/foreach}
