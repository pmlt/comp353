<table>
  <tr>
    <td><a href="{sems_home_url()}">SEMS Home</a></td>
    {$i = sems_get_identity()}
    {if $i}
    <td>(<a href="{sems_profile_url($i->UserId)}">{$i->fullname()}</a>) <a href="{sems_logout_url()}">Logout</a></td>
    {else}
    <td><a href="{sems_login_url()}">Login</a></td>
    {/if}
  </tr>
</table>
