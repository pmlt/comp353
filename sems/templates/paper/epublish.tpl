{extends file="event/master.tpl"}

{block name='content_title' append=true} - ePublish papers{/block}
{block name='content'}

<script type="text/javascript">
$(function() {
  $('.js-datetime').datepicker({
    dateFormat: 'yy-mm-dd',
    constrainInput: false
  });
});
</script>


<form method="post">
{foreach $papers as $paper}
{include file="paper/file.tpl" paper=$paper revisions=$paper.revisions authors=$paper.authors with_decision=true}

<p>Publish date: <input type="text" class="js-datetime" name="paper_{$paper.paper_id}" value="{$paper.publish_date}"}</p>
<hr />
{/foreach}

<p><input type="submit" value="Save dates" /></p>
{if $saved}<p class="success">Publication dates saved.</p>{/if}
</form>


{/block}
