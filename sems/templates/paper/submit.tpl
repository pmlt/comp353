{extends file="event/master.tpl"}

{block name='content_title' append=true} - Submit a new paper{/block}
{block name='content'}
<script type="text/javascript">
function updateField() {
  var emails = [];
  $('#authors_list li').each(function() {
    var i = $(this).find('input');
    if (i.length > 0) emails.push(i.val());
  });
  $('input[name=authors]').val(emails.join('|'));
}

$(function() {
  $('#authors_list').on('click', '.js-remove', function(e) {
    e.preventDefault();
    $(e.currentTarget).parent().remove();
  });
  $('#add').on('click', function(e) {
    e.preventDefault();
    $('#add').parent().before('<li><input type="text" value="" /> <button class="js-remove">Remove</button></li>');
  });
  $('form').bind('submit', function(e) {
    updateField();
  });

  updateField();
});
</script>

<form method="post" enctype="multipart/form-data">
<table>
  <tr>
    <td><label for="title">Title</label></td>
    <td><input type="text" id="title" name="title" value="{$smarty.post.title}" />
    <td><span class="error">{$errors.title}</span></td>
  </tr>
  <tr>
    <td><label for="abstract">Abstract</label></td>
    <td><input type="text" id="abstract" name="abstract" value="{$smarty.post.abstract}" />
    <td><span class="error">{$errors.abstract}</span></td>
  </tr>
  <tr>
    <td><label for="keywords">Keywords</label></td>
    <td><input type="text" id="keywords" name="keywords" value="{$smarty.post.keywords}" />
    <td><span class="error">{$errors.keywords}</span></td>
  </tr>
  <tr>
    <td><label for="file">File (PDF only!)</label></td>
    <td><input type="file" name="file" id="file" /></td>
    <td><span class="error">{$errors.file}</span>
  </tr>
  <tr>
    <td>Authors</td>
    <td>
      <ul id="authors_list">
        {if $smarty.post.authors}
        {foreach explode('|', $smarty.post.authors) as $email}
        <li><input type="text" value="{$email}" /> <button class="js-remove">Remove</button></li>
        {/foreach}
        {/if}
        <li><button id="add">Add a co-author</button></li>
      </ul>
      <input type="hidden" name="authors" value="" />
    </td>
    <td>{foreach $errors.authors as $e}<p class="error">{$e}</p>{/foreach}</td>
  </tr>
  <tr>
    <td>Topics</td>
    <td>{include file="ui/topic_selector.tpl"}</td>
    <td><span class="error">{$errors.topics}</span></td>
  </tr>
  <tr>
    <td colspan="3"><input id="submit" type="submit" value="Submit" /></td>
  </tr>
</table>
</form>
{/block}
