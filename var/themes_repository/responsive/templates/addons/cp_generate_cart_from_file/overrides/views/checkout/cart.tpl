{script src="js/tygh/exceptions.js"}
{script src="js/tygh/checkout.js"}
{script src="js/tygh/cart_content.js"}

<div id="cart_main">
    {if !$cart|fn_cart_is_empty}
        {include file="views/checkout/components/cart_content.tpl"}
    {else}
        <p class="ty-no-items">{__("text_cart_empty")}</p>

        <div class="buttons-container wrap">
            {include file="buttons/continue_shopping.tpl" but_href=$continue_url|fn_url but_role="submit"}
            {include file="buttons/button.tpl" but_text=__("load_template_file") but_meta="ty-btn__primary cm-process-items cp-generate-cart-page-link"}
        </div>
    {/if}
    <!--cart_main--></div>