{foreach $hierarchy as $ca => $topics}
<p>{$ca}</p>
<ul>
  {foreach $topics as $topic}
  <li>{$topic.name}</li>
  {/foreach}
</ul>
{/foreach}
