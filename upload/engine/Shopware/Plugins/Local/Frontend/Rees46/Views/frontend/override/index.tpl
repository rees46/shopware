{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_header_javascript_tracking"}
    {$smarty.block.parent}
    <script>
    {literal}(function(r){window.r46=window.r46||function(){(r46.q=r46.q||[]).push(arguments)};var s=document.getElementsByTagName(r)[0],rs=document.createElement(r);rs.async=1;rs.src='//cdn.rees46.com/v3.js';s.parentNode.insertBefore(rs,s);})('script');{/literal}
    {$REES46_DATA}
    {$REES46_CSS}
    </script>
{/block}

{block name="frontend_index_content_wrapper"}
    {$smarty.block.parent}

	{foreach from=$REES46_MODULES item=module}
		{if $module.params}
		<script>
		r46('recommend', '{$module.type}', {$module.params|json_encode nofilter}, function(results) {
			if (results.length > 0) {
				$('#rees46-recommended-{$module.id}').load('rees46&module_id={$module.id}&product_ids=' + results);
			}
		});
		</script>
		<div id="rees46-recommended-{$module.id}"></div>
		{/if}
	{/foreach}
{/block}
