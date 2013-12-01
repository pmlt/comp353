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
    <td><label for="description">Description</label></td>
    <td><textarea id="description" name="description">{$data.description}</textarea></td>
    <td><span class="error">{$errors.description}</span></td>
  </tr>
  <tr>
    <td><label for="chair_email">Chair Email</label></td>
    <td><input type="text" id="chair_email" name="chair_email" value="{$data.chair_email}" /></td>
    <td><span class="error">{$errors.chair_email}</span></td>
  </tr>
  <tr>
    <td><label for="term_start_date">Start of term</label></td>
    <td><input class="js-datetime" type="text" id="term_start_date" name="term_start_date" value="{$data.term_start_date}" /></td>
    <td><span class="error">{$errors.term_start_date}</span></td>
  </tr>
  <tr>
    <td><label for="term_end_date">End of term</label></td>
    <td><input class="js-datetime" type="text" id="term_end_date" name="term_end_date" value="{$data.term_end_date}" /></td>
    <td><span class="error">{$errors.term_end_date}</span></td>
  </tr>
  <tr>
    <td><label for="submit_start_date">Start of submission</label></td>
    <td><input class="js-datetime" type="text" id="submit_start_date" name="submit_start_date" value="{$data.submit_start_date}" /></td>
    <td><span class="error">{$errors.submit_start_date}</span></td>
  </tr>
  <tr>
    <td><label for="submit_end_date">End of submission</label></td>
    <td><input class="js-datetime" type="text" id="submit_end_date" name="submit_end_date" value="{$data.submit_end_date}" /></td>
    <td><span class="error">{$errors.submit_end_date}</span></td>
  </tr>
  <tr>
    <td><label for="auction_start_date">Start of auction</label></td>
    <td><input class="js-datetime" type="text" id="auction_start_date" name="auction_start_date" value="{$data.auction_start_date}" /></td>
    <td><span class="error">{$errors.auction_start_date}</span></td>
  </tr>
  <tr>
    <td><label for="auction_end_date">End of auction</label></td>
    <td><input class="js-datetime" type="text" id="auction_end_date" name="auction_end_date" value="{$data.auction_end_date}" /></td>
    <td><span class="error">{$errors.auction_end_date}</span></td>
  </tr>
  <tr>
    <td><label for="review_start_date">Start of review</label></td>
    <td><input class="js-datetime" type="text" id="review_start_date" name="review_start_date" value="{$data.review_start_date}" /></td>
    <td><span class="error">{$errors.review_start_date}</span></td>
  </tr>
  <tr>
    <td><label for="review_end_date">End of review</label></td>
    <td><input class="js-datetime" type="text" id="review_end_date" name="review_end_date" value="{$data.review_end_date}" /></td>
    <td><span class="error">{$errors.review_end_date}</span></td>
  </tr>
  <tr>
    <td><label for="decision_date">Decision date</label></td>
    <td><input class="js-datetime" type="text" id="decision_date" name="decision_date" value="{$data.decision_date}" /></td>
    <td><span class="error">{$errors.decision_date}</span></td>
  </tr>
  <tr>
    <td><label for="start_date">Start of meeting</label></td>
    <td><input class="js-datetime" type="text" id="start_date" name="start_date" value="{$data.start_date}" /></td>
    <td><span class="error">{$errors.start_date}</span></td>
  </tr>
  <tr>
    <td><label for="end_date">End of meeting</label></td>
    <td><input class="js-datetime" type="text" id="end_date" name="end_date" value="{$data.end_date}" /></td>
    <td><span class="error">{$errors.end_date}</span></td>
  </tr>
  <tr>
    <td>Topics</td>
    <td>{include file="ui/topic_selector.tpl"}</td>
    <td><span class="error">{$errors.topics}</span></td>
  </tr>
  <tr>
    <td colspan="3"><input type="submit" value="Submit" /></td>
  </tr>
</table>
</form>
