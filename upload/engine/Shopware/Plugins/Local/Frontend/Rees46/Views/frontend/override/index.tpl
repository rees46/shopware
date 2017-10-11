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
                    $('#rees46-recommended-{$module.id} .rees46-product').load('{$Shop->getBasePath()}/rees46?module_id={$module.id}&product_ids=' + results, function(results) {
                        $('#rees46-recommended-{$module.id}').css('display', 'block');
                    });
                }
            });
            </script>
            {if $module.template == 'rees46'}
            <div id="rees46-recommended-{$module.id}" style="margin: 0rem 0rem 0.625rem 0rem; display: none;">
                <div class="rees46 rees46-recommend">
                    <div class="recommender-block-title">{$module.title}</div>
                    <div class="recommended-items rees46-product"></div>
                </div>
            </div>
            {else}
            <div id="rees46-recommended-{$module.id}" class="panel has--border" style="margin: 0rem 0rem 0.625rem 0rem; display: none;">
                <div class="panel--title is--underline product-slider--title">{$module.title}</div>
                <div class="product-slider product-slider--content" style="padding: 0rem 1.25rem 1.25rem 0rem;">
                    <div class="product-slider" data-product-slider="false" data-itemMinWidth="25%">
                        <div class="product-slider--container is--horizontal rees46-product" data-ajax-wishlist="true" data-compare-ajax="true"></div>
                    </div>
                </div>
            </div>
            {/if}
        {/if}
    {/foreach}
{/block}
