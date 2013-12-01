{if $actions}
  <ul>
    {foreach $actions as $action}
      <li><a href="{$action.url}">{$action.label}</a></li>
    {/foreach}
  </ul>
{/if}
