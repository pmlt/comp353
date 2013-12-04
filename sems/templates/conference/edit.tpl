{extends file="conference/master.tpl"}

{block name='content_title'}Modify details for {$conf.name}{/block}
{block name='content'}

{include file="conference/form.tpl" data=array_merge($conf, $smarty.post)}

{/block}
