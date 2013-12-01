<form method="post">
<table>
  <tr>
    <td><label for="name">Name</label></td>
    <td><input id="name" type="text" name="name" value="{$data.name}" /></td>
    <td class="error">{$errors.name}</td>
  </tr>
  <tr>
    <td><label for="type">Type</label></td>
    <td>{html_options name='type' options=[
      "C" => "Conference",
      "J" => "Journal"] selected=$data.type}</td>
    <td class="error">{$errors.type}</td>
  </tr>
  <tr>
    <td><label for="description">Description</label></td>
    <td><textarea id="description" name="description">{$data.description}</textarea></td>
    <td class="error">{$errors.description}</td>
  </tr>
  <tr>
    <td><label for="chair_email">Chair Email</label></td>
    <td><input id="chair_email" type="text" name="chair_email" value="{$data.chair_email}" /></td>
    <td class="error">{$errors.chair_email}</td>
  </tr>
  <tr>
    <td>Topics</td>
    <td>{include file="ui/topic_selector.tpl"}</td>
    <td class="error">{$errors.topics}</td>
  </tr>
  <tr><td colspan="3"><input type="submit" value="Submit" /></td></tr>
</table>
</form>
