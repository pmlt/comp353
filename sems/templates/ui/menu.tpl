<table>
  <tr>
    <td><a href="{sems_home_url()}">SEMS Home</a></td>
    {if sems_is_anonymous()}
    <td><a href="{sems_login_url()}">Login</a></td>
    {else}
    {$i = sems_get_identity()}
    <td>(<a href="{sems_profile_url($i->UserId)}">{$i->fullname()}</a>) <a href="{sems_logout_url()}">Logout</a></td>
    {/if}
  </tr>
</table>

{include file="ui/actions.tpl"}
