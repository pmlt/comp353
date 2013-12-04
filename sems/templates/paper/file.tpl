
<table>
  <tr>
    <td>Title</td>
    <td>{$paper.title}</td>
  </tr>
  <tr>
    <td>Abstract</td>
    <td>{$paper.abstract}</td>
  </tr>
  <tr>
    <td>Keywords</td>
    <td>{$paper.keywords}</td>
  </tr>
  <tr>
    <td>Author(s)</td>
    <td>
      <ul>
      {foreach $authors as $author}
      <li><a href="{sems_profile_url($author.user_id)}">{$author.first_name} {$author.middle_name} {$author.last_name}</a></li>
      {/foreach}
      </ul>
    </td>
  </tr>
  <tr>
    <td>Revision(s)</td>
    <td>
      <ul>
      {foreach $revisions as $rev}
        <li><a href="{sems_paper_download_url($paper.paper_id,$rev)}">{sems_datetime($rev)}</a></li>
      {/foreach}
      </ul>
    </td>
  </tr>
  {if $with_decision}
    {$options = sems_paper_decision_options()}
  <tr>
    <td>Final decision: </td>
    <td>{$options[$paper.decision]}</td>
  </tr>
  {/if}
</table>
