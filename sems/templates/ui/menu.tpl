<table>
  <tr>
    <td><a href="{sems_home_url()}">SEMS Home</a></td>
    {if sems_get_identity()}
    <td><a href="{sems_logout_url()}">Logout</a></td>
    {else}
    <td><a href="{sems_login_url()}">Login</a></td>
    {/if}
  </tr>
</table>