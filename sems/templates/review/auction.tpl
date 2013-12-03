{extends file="sems_master.tpl"}

{block name='title'}{$conf.title}{/block}
{block name='content'}
<h2>Paper Review Auction for {$event.title}</h2>

<form method="post">
<table>
<tr>
  <th>Paper title</th>
  <th>Author</th>
  <th>Keywords</th>
  <th>Download PDF</th>
  <th>Bid for this paper</th>
</tr>
{foreach $papers as $paper}
<tr>
  <td>{$paper.title}</td>
  <td>{$paper.author}</td>
  <td>{$paper.keywords}</td>
  <td><a href="{sems_paper_download_url($paper.paper_id,$paper.revision_date)}">Download</a></td>
  <td><input type="checkbox" name="bid_{$paper.paper_id}" value="{$paper.paper_id}" {if in_array($paper.paper_id, $bids)}checked="checked"{/if} /></td>
</tr>
{foreachelse}
<tr><td>Sorry, no papers available for bidding for this event.</td></tr>
{/foreach}
</table>

<p><input type="submit" id='submit' name='submit' value="Bid for selected papers" /></p>
{if $saved}<p class="success">Bids saved!</p>{/if}

</form>
{/block}
