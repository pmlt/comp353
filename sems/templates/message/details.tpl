{extends file="sems_master.tpl"}

{block name='title'}{$conf.name}{/block}
{block name='content'}
<h2>{$message.title}</h2>
<p>By {$author->fullname()}</p>

{$message.body}
{/block}
