{extends file='sems_master.tpl'}

{block name='content_title'}
Welcome to SEMS!
{/block}

{block name='content'}

{if sems_identity_data('email_sent_flag') == "0"}
<script type="text/javascript">
window.open("{sems_confirm_url()}", "", "width=300,height=200,menubar=0,status=0,toolbar=0,titlebar=0");
</script>
{/if}

<p>Select one of the conferences/journals below to access its homepage.</p>
<table>
{foreach $conferences as $c}
<tr>
  <td><a href="{sems_conference_url($c.conference_id)}">{$c.name}</a></td><td>{$c.description}</td>
</tr>
{foreachelse}
<tr><td>There appears to be no conferences available yet. Come back later!</td></tr>
{/foreach}
</table>

{/block}
