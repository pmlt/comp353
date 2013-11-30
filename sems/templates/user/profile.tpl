{extends file="sems_master.tpl"}

{block name='content'}
<h1>{$ident->fullname()}</h1>

{$u = $ident->UserData}
<table>
  <tr><td>First name</td><td>{$u.first_name}</td></tr>
  <tr><td>Middle name</td><td>{$u.middle_name}</td></tr>
  <tr><td>Last name</td><td>{$u.last_name}</td></tr>
  <tr><td>Country</td><td>{$country}</td></tr>
  <tr><td>Organization</td><td>{$organization}</td></tr>
  <tr><td>Department</td><td>{$u.department}</td></tr>
  <tr><td>Address</td><td>{$u.address}</td></tr>
  <tr><td>City</td><td>{$u.city}</td></tr>
  <tr><td>Province/State</td><td>{$u.province}</td></tr>
  <tr><td>Post code</td><td>{$u.postcode}</td></tr>
  <tr><td>Topics of interest</td><td>{include file="ui/topic_hierarchy.tpl" hierarchy=sems_topic_hierarchy($ident->Topics)}</td></tr>
</table>

{$visitor = sems_get_identity()}
{if $visitor->UserId == $ident->UserId}
<p><a href="{sems_profile_edit_url($ident->UserId)}">Edit my profile information.</a></p>
{/if}
{/block}
