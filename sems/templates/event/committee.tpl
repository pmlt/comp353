{extends file="event/master.tpl"}

{block name='content_title' append=true} - Manage committee{/block}
{block name='content'}

<script type="text/javascript">
function updateField() {
  var emails = [];
  $('#committee_list li').each(function() {
    var i = $(this).find('input');
    if (i.length > 0) emails.push(i.val());
  });
  console.log(emails);
  $('input[name=committee]').val(emails.join('|'));
}

$(function() {
  $('#committee_list').on('click', '.js-remove', function(e) {
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

<form method="post">
<ul id="committee_list">
  {foreach $committee as $member}
  <li><input type="text" value="{$member.email}" /> <button class="js-remove">Remove</button></li>
  {/foreach}
  <li><button id="add">Add another committee member</button></li>
</ul>
<p><input type="hidden" name="committee" value="" /><input id='submit' type="submit" value="Save this committee"</p>
{if $errors}
  {foreach $errors as $error}<p class="error">{$error}</p>{/foreach}
{elseif $success}
  <p class="success">Committee saved!</p>
{/if}
</form>
{/block}
