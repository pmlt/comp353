
<form method="post">
<table>
  <tr>
    <td>Title:</td>
    <td>{html_options name='title' options=[
      "mr" => "Mr.",
      "mrs" => "Mrs.",
      "ms" => "Ms.",
      "dr" => "Dr."] selected=$user.title}</td>
    <td class="error">{$errors.title}</td>
  </tr>
  <tr>
    <td>Firstname:</td>
    <td><input type="text" name="first_name" value="{$user.first_name}" />*</td>
    <td class="error">{$errors.first_name}</td>
  </tr>
  <tr>
    <td>Middle Name:</td>
    <td><input type="text" name="middle_name" value="{$user.middle_name}" /></td>
    <td class="error">{$errors.middle_name}</td>
  </tr>
  <tr>
    <td>Lastname:</td>
    <td><input type="text" name="last_name" value="{$user.last_name}" />*</td>
    <td class="error">{$errors.last_name}</td>
  </tr>
  <tr>
    <td>Country:</td>
    <td>{html_options name='country_id' options=$countries selected=$user.country_id}*</td>
    <td class="error">{$errors.country_id}</td>
  </tr>
  <tr>
    <td>Organization:</td>
    <td>{html_options name='organization_id' options=$organizations selected=$user.organization_id}*</td>
    <td class="error">{$errors.organization_id}</td>
  </tr>
  <tr>
    <td>Department:</td>
    <td><input type="text" name="department" value="{$user.department}" />*</td>
    <td class="error">{$errors.department}</td>
  </tr>
  <tr>
    <td>Address:</td>
    <td><input type="text" name="address" value="{$user.address}" /></td>
    <td class="error">{$errors.address}</td>
  </tr>
  <tr>
    <td>City:</td>
    <td><input type="text" name="city" value="{$user.city}" /></td>
    <td class="error">{$errors.city}</td>
  </tr>
  <tr>
    <td>Province/State:</td>
    <td><input type="text" name="province" value="{$user.province}" /></td>
    <td class="error">{$errors.province}</td>
  </tr>
  <tr>
    <td>Postcode:</td>
    <td><input type="text" name="postcode" value="{$user.postcode}" /></td>
    <td class="error">{$errors.postcode}</td>
  </tr>
  <tr>
    <td>Topics of interest: </td>
    <td>{include file="ui/topic_selector.tpl"}</td>
  </tr>
  {if !$edit}
  <tr>
    <td>Email:</td>
    <td><input type="text" name="email" value="{$user.email}" /></td>
    <td class="error">{$errors.email}</td>
  </tr>
  <tr>
    <td>Email (Re-type):</td>
    <td><input type="text" name="email_confirm" value="{$user.email_confirm}" /></td>
    <td class="error">{$errors.email_confirm}</td>
  </tr>
  <tr>
    <td>Password: </td>
    <td><input type="password" name="password" value="" />*</td>
    <td class="error">{$errors.password}</td>
  </tr>
  <tr>
    <td>Password (Re-type): </td>
    <td><input type="password" name="password_confirm" value="" />*</td>
    <td class="error">{$errors.password_confirm}</td>
  </tr>
  {/if}
  <tr><td colspan="3"><input type="submit" value="Submit" /></td></tr>
</table>
</form>
