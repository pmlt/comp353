<script type="text/javascript">
$(function() {
  $('.js-datetime').datepicker({
    dateFormat: 'yy-mm-dd',
    constrainInput: false
  });
});
</script>


<form method="post">
<table>
  <tr>
    <td><label for="title">Title</label></td>
    <td><input type="text" id="title" name="title" value="{$data.title}" /></td>
    <td><span class="error">{$errors.title}</span></td>
  </tr>
  <tr>
    <td><label for="publish_date">Publication date</label></td>
    <td><input class="js-datetime" type="text" id="publish_date" name="publish_date" value="{$data.publish_date}" /></td>
    <td><span class="error">{$errors.publish_date}</span></td>
  </tr>
  <tr>
    <td><label for="is_public">This is a public message</label></td>
    <td><input type="checkbox" id="is_public" name="is_public" value="1" {if $data.is_public}checked="checked"{/if} /></td>
    <td><span class="error">{$errors.is_public}</span></td>
  <tr>
    <td><label for="excerpt">Excerpt</label></td>
    <td><textarea id="excerpt" name="excerpt">{$data.excerpt}</textarea></td>
    <td><span class="error">{$errors.excerpt}</span></td>
  </tr>
  <tr>
    <td><label for="body">Body</label></td>
    <td><textarea id="body" name="body">{$data.body}</textarea></td>
    <td><span class="error">{$errors.body}</span></td>
  </tr>
  <tr>
    <td colspan="3"><input type="submit" value="Submit" /></td>
  </tr>
</table>
