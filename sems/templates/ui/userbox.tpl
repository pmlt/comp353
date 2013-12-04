<div id="userbox">
  <div class='userline'>
  {if sems_is_anonymous()}
    <div class="mainaction"><a href="{sems_login_url()}">Sign-in</a></div>
    <div class="user">Guest</div>
  {else}
    {$i = sems_get_identity()}
    <div class="mainaction"><a href="{sems_logout_url()}">Sign-out</a></div>
    <div class="user"><a href="{sems_profile_url($i->UserId)}">{$i->fullname()}</a></div>
  {/if}
  </div>
  <div class="separator">&nbsp;</div>
  {if $user_actions}
  <div class='action-title'>On this page...</div>
  <ul>
    {foreach $user_actions as $action}
    <li><a href="{$action.url}">{$action.label}</a></li>
    {/foreach}
  </ul>
  {/if}
</div>

