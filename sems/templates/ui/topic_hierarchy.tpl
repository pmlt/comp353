{foreach $hierarchy as $ca => $topics}
<p class="topic-category">{$ca}</p>
<ul class="topics">
  {foreach $topics as $topic}
  <li>{$topic.name}</li>
  {/foreach}
</ul>
{/foreach}
