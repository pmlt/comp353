{extends file="sems_master.tpl"}

{block name='title'}{$conf.name}{/block}
{block name='content'}
<h2>Review of {$paper.title}</h2>

<h3>Paper details</h3>
{include file="paper/file.tpl"}

<h3>Review</h3>
<form method="post">
{$data = array_merge($review,$smarty.post)}
{$scale = [1,2,3,4,5,6,7,8,9,10]}
<table>
  <tr>
    <td><label for="score">Score</label></td>
    <td>{html_options id="score" name="score" values=$scale output=$scale selected=$data.score}</td>
    <td><span class="error">{$errors.score}</span></td>
  </tr>
  <tr>
    <td><label for="confidence">Confidence</label></td>
    <td>{html_options id="confidence" name="confidence" values=$scale output=$scale selected=$data.confidence}</td>
    <td><span class="error">{$errors.confidence}</span></td>
  </tr>
  <tr>
    <td><label for="originality">Confidence</label></td>
    <td>{html_options id="originality" name="originality" options=[
      "good" => "Good",
      "mediocre" => "Mediocre",
      "bad" => "Bad"] selected=$data.originality}</td>
    <td><span class="error">{$errors.originality}</span></td>
  </tr>
  <tr>
    <td><label for="strong_point">Strongest point</label></td>
    <td><input type="text" id="strong_point" name="strong_point" value="{$data.strong_point}" /></td>
    <td><span class="error">{$errors.strong_point}</span></td>
  </tr>
  <tr>
    <td><label for="review_comments">Comments about the review</label></td>
    <td><textarea id="review_comments" name="review_comments">{$data.review_comments}</textarea></td>
    <td><span class="error">{$errors.review_comments}</span></td>
  </tr>
  <tr>
    <td><label for="author_comments">Comments for the author</label></td>
    <td><textarea id="author_comments" name="author_comments">{$data.author_comments}</textarea></td>
    <td><span class="error">{$errors.author_comments}</span></td>
  </tr>
  <tr>
    <td><label for="chair_comments">Comments for the Program Chair</label></td>
    <td><textarea id="chair_comments" name="chair_comments">{$data.chair_comments}</textarea></td>
    <td><span class="error">{$errors.chair_comments}</span></td>
  </tr>
  <tr>
    <td><label for="external_reviewer_email">External Reviewer Email</label></td>
    <td><input type="text" id="external_reviewer_email" name="external_reviewer_email" value="{$data.external_reviewer_email}" /></td>
    <td><span class="error">{$errors.external_reviewer_email}</span></td>
  </tr>
  <tr>
    <td colspan="3"><input type="submit" value="Save" /></td>
  </tr>
</table>

{if $saved}<p class="success">Review saved! You can keep changing the review until {sems_datetime($event.review_end_date)}.</p>{/if}
</form>
{/block}
