{extends file='sems_master.tpl'}

{block name='content_title'}Sign-in with your SEMS account{/block}
{block name='content'}
<form method="post">
<table>
  <tr>
    <td><label for="email">Email</label></td>
    <td><input type="text" id="email" name="email" value="{$smarty.post.email}" /></td>
  </tr>
  <tr>
    <td><label for="password">Password</label></td>
    <td><input type="password" id="password" name="password" value="{$smarty.post.password}" /></td>
  </tr>
  <tr><td colspan="2"><input type="submit" value="Submit" /></td></tr>
</table>
</form>
{if $login_failed}
<p class="error">Login failed. Please re-check your email or password information.</p>
{/if}
<p>If you do not yet have an account, <a href="{sems_signup_url()}">click here</a> to sign up!</p>
{/block}
