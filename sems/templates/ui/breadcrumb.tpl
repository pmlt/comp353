{if $breadcrumb}
<ul>
  {foreach $breadcrumb as $elem}
  <li><a href="{$elem.url}">{$elem.label}</a></li>
  {/foreach}
</ul>
{/if}

