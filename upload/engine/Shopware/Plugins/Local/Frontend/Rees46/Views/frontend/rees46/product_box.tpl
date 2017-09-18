{extends file="frontend/listing/product-box/box-basic.tpl"}

{block name="frontend_listing_box_article"}
	<div class="recommended-item">
		<div class="recommended-item-photo">
            {if isset($sArticle.image.thumbnails)}
                <a href="{$sArticle.linkDetails}"><img srcset="{$sArticle.image.thumbnails[0].sourceSet}" alt="{$sArticle.articleName|escape}" title="{$sArticle.articleName|escape}" /></a>
            {else}
                <a href="{$sArticle.linkDetails}"><img src="{link file='frontend/_public/src/img/no-picture.jpg'}" alt="{$sArticle.articleName|escape}" title="{$sArticle.articleName|escape}" /></a>
            {/if}
		</div>
		<div class="recommended-item-title">
			<a href="{$sArticle.linkDetails}">{$sArticle.articleName|escape}</a>
		</div>
        <div class="recommended-item-price">
            {if $sArticle.has_pseudoprice}
            	{$sArticle.pseudoprice|currency}
            {else}
            	{$sArticle.price|currency}
            {/if}
        </div>
		<div class="recommended-item-action">
			<a href="{$sArticle.linkDetails}">{s name="rees46_more"}More{/s}</a>
		</div>
	</div>
{/block}
