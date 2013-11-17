{extends file='sems_master.tpl'}

{block name='content'}
<form method="post">
<table>
  <tr>
    <td>Title:</td>
    <td><input type="text" name="title" value="{$smarty.post.title}" /></td>
    <td class="error">{$errors.title}</td>
  </tr>
  <tr>
    <td>Firstname:</td>
    <td><input type="text" name="first_name" value="{$smarty.post.first_name}" />*</td>
    <td class="error">{$errors.first_name}</td>
  </tr>
  <tr>
    <td>Middle Name:</td>
    <td><input type="text" name="middle_name" value="{$smarty.post.middle_name}" /></td>
    <td class="error">{$errors.middle_name}</td>
  </tr>
  <tr>
    <td>Lastname:</td>
    <td><input type="text" name="last_name" value="{$smarty.post.last_name}" />*</td>
    <td class="error">{$errors.last_name}</td>
  </tr>
  <tr>
    <td>Country:</td>
    <td>{html_options name='country_id' options=$countries selected=$smarty.post.country_id}*</td>
    <td class="error">{$errors.country_id}</td>
  </tr>
  <tr>
    <td>Organization:</td>
    <td>{html_options name='organization_id' options=$organizations selected=$smarty.post.organization_id}*</td>
    <td class="error">{$errors.organization_id}</td>
  </tr>
  <tr>
    <td>Department:</td>
    <td><input type="text" name="department" value="{$smarty.post.department}" />*</td>
    <td class="error">{$errors.department}</td>
  </tr>
  <tr>
    <td>Address:</td>
    <td><input type="text" name="address" value="{$smarty.post.address}" /></td>
    <td class="error">{$errors.address}</td>
  </tr>
  <tr>
    <td>City:</td>
    <td><input type="text" name="city" value="{$smarty.post.city}" /></td>
    <td class="error">{$errors.city}</td>
  </tr>
  <tr>
    <td>Province/State:</td>
    <td><input type="text" name="province" value="{$smarty.post.province}" /></td>
    <td class="error">{$errors.province}</td>
  </tr>
  <tr>
    <td>Postcode:</td>
    <td><input type="text" name="postcode" value="{$smarty.post.postcode}" /></td>
    <td class="error">{$errors.postcode}</td>
  </tr>
  <tr>
    <td>Email:</td>
    <td><input type="text" name="email" value="{$smarty.post.email}" />*</td>
    <td class="error">{$errors.email}</td>
  </tr>
  <tr>
    <td>Email (Re-type):</td>
    <td><input type="text" name="email_confirm" value="{$smarty.post.email_confirm}" />*</td>
    <td class="error">{$errors.email_confirm}</td>
  </tr>
  <tr>
    <td>Password: </td>
    <td><input type="password" name="password" value="{$smarty.post.password}" />*</td>
    <td class="error">{$errors.password}</td>
  </tr>
  <tr>
    <td>Password (Re-type): </td>
    <td><input type="password" name="password_confirm" value="{$smarty.post.password_confirm}" />*</td>
    <td class="error">{$errors.password_confirm}</td>
  </tr>
  <tr><td colspan="2"><input type="submit" value="Submit" /></td></tr>
</table>
</form>
<p>If you do not yet have an account, <a href="{sems_signup_url()}">click here</a> to sign up!</p>
{/block}