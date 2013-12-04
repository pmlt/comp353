{extends file="event/master.tpl"}

{block name='content_title'}{$message.title}{/block}
{block name='content'}
<p>Published on {sems_datetime($message.publish_date)}</p>
<p>By <a href="{sems_profile_url($author->UserId)}">{$author->fullname()}</a></p>

{$message.body}
{/block}
