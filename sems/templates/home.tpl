{extends file='sems_master.tpl'}

{block name='content'}
<h2>Welcome to SEMS!</h2>

{if sems_identity_data('email_sent_flag') == "0"}
<script type="text/javascript">
window.open("{sems_confirm_url()}", "", "width=300,height=200,menubar=0,status=0,toolbar=0,titlebar=0");
</script>
{/if}

{/block}