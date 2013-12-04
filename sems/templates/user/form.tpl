
<p>Fields indicated in <strong>bold</strong> are required.</p>

<form method="post">
<table>
  <tr>
    <td><label for="title">Title</label></td>
    <td>{html_options id='title' name='title' options=[
      "mr" => "Mr.",
      "mrs" => "Mrs.",
      "ms" => "Ms.",
      "dr" => "Dr."] selected=$user.title}</td>
    <td class="error">{$errors.title}</td>
  </tr>
  <tr>
    <td><label for="first_name"><strong>First Name</strong></label></td>
    <td><input type="text" id="first_name" name="first_name" value="{$user.first_name}" /></td>
    <td class="error">{$errors.first_name}</td>
  </tr>
  <tr>
    <td><label for="middle_name">Middle Name</label></td>
    <td><input type="text" id="middle_name" name="middle_name" value="{$user.middle_name}" /></td>
    <td class="error">{$errors.middle_name}</td>
  </tr>
  <tr>
    <td><label for="last_name"><strong>Last Name</strong></label></td>
    <td><input type="text" id="last_name" name="last_name" value="{$user.last_name}" />*</td>
    <td class="error">{$errors.last_name}</td>
  </tr>
  <tr>
    <td><label for="country_id"><strong>Country</strong></label></td>
    <td>{html_options id="country_id" name='country_id' options=$countries selected=$user.country_id}</td>
    <td class="error">{$errors.country_id}</td>
  </tr>
  <tr>
    <td><label for="organization_id"><strong>Organization</strong></label></td>
    <td>{html_options id="organization_id" name='organization_id' options=$organizations selected=$user.organization_id}</td>
    <td class="error">{$errors.organization_id}</td>
  </tr>
  <tr>
    <td><label for="department"><strong>Department</strong></label></td>
    <td><input type="text" id="department" name="department" value="{$user.department}" /></td>
    <td class="error">{$errors.department}</td>
  </tr>
  <tr>
    <td><label for="address">Address</label></td>
    <td><input type="text" id="address" name="address" value="{$user.address}" /></td>
    <td class="error">{$errors.address}</td>
  </tr>
  <tr>
    <td><label for="city">City</label></td>
    <td><input type="text" id="city" name="city" value="{$user.city}" /></td>
    <td class="error">{$errors.city}</td>
  </tr>
  <tr>
    <td><label for="province">Province/State:</label></td>
    <td><input type="text" id="province" name="province" value="{$user.province}" /></td>
    <td class="error">{$errors.province}</td>
  </tr>
  <tr>
    <td><label for="postcode">Postcode</label></td>
    <td><input type="text" id="postcode" name="postcode" value="{$user.postcode}" /></td>
    <td class="error">{$errors.postcode}</td>
  </tr>
  <tr>
    <td>Topics of interest: </td>
    <td>{include file="ui/topic_selector.tpl"}</td>
  </tr>
  {if !$edit}
  <tr>
    <td><label for="email"><strong>Email</strong></label></td>
    <td><input type="text" id="email" name="email" value="{$user.email}" /></td>
    <td class="error">{$errors.email}</td>
  </tr>
  <tr>
    <td><label for="email_confirm"><strong>Email (Re-type)</strong></label></td>
    <td><input type="text" id="email_confirm" name="email_confirm" value="{$user.email_confirm}" /></td>
    <td class="error">{$errors.email_confirm}</td>
  </tr>
  <tr>
    <td><label for="password"><strong>Password</strong></label></td>
    <td><input type="password" id="password" name="password" value="" /></td>
    <td class="error">{$errors.password}</td>
  </tr>
  <tr>
    <td><label for="password"><strong>Password (Re-type)</strong></label></td>
    <td><input type="password" id="password_confirm" name="password_confirm" value="" /></td>
    <td class="error">{$errors.password_confirm}</td>
  </tr>
  {/if}
  <tr><td colspan="3"><input type="submit" value="Submit" /></td></tr>
</table>
</form>
