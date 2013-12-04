{extends file="sems_master.tpl"}

{block name='content_title'}Profile of {$ident->fullname()}{/block}
{block name='content'}

{$u = $ident->UserData}
<table>
  <tr><td><strong>First name</strong></td><td>{$u.first_name}</td></tr>
  <tr><td><strong>Middle name</strong></td><td>{$u.middle_name}</td></tr>
  <tr><td><strong>Last name</strong></td><td>{$u.last_name}</td></tr>
  <tr><td><strong>Country</strong></td><td>{$country}</td></tr>
  <tr><td><strong>Organization</strong></td><td>{$organization}</td></tr>
  <tr><td><strong>Department</strong></td><td>{$u.department}</td></tr>
  <tr><td><strong>Address</strong></td><td>{$u.address}</td></tr>
  <tr><td><strong>City</strong></td><td>{$u.city}</td></tr>
  <tr><td><strong>Province/State</strong></td><td>{$u.province}</td></tr>
  <tr><td><strong>Post code</strong></td><td>{$u.postcode}</td></tr>
  <tr><td><strong>Topics of interest</strong></td><td>{include file="ui/topic_hierarchy.tpl"}</td></tr>
</table>

{$visitor = sems_get_identity()}
{if $visitor->UserId == $ident->UserId}
<p><a href="{sems_profile_edit_url($ident->UserId)}">Edit my profile information.</a></p>
{/if}
{/block}
