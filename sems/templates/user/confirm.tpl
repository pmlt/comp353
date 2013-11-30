{extends file='master.tpl'}

{block name='body'}
{if $confirm_success}
<script type="text/javascript">window.close();</script>
{/if}
<form method="post">
<p>Click the submit button to "confirm" {sems_identity_data('email')} as your email address. <input type='submit' name='submit' value='Submit' />
</form>
{/block}