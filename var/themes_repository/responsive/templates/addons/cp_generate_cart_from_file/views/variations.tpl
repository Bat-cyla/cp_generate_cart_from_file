<div id="cp_variations_modal">
    <form action="{""|fn_url}" method="POST" name="cp_variations_modal_form" class="form-horizontal cm-ajax" enctype="multipart/form-data">
    <input type="hidden" class="cp-variation-modal"/>
        <fieldset>
            <div class="table-responsive-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="4" class="center">
                                <h4>{__("cp_specific_products")}</h4>
                            </th>
                        </tr>
                        <tr class="variations-head">
                            <th class="variations-head__item">
                            </th>
                            <th class="variations-head__item">
                                {__("name")}
                            </th>
                            <th class="variations-head__item">
                                {__("cp_artiqle")}
                            </th>
                            <th class="variations-head__item">
                            </th>
                        </tr>
                    </thead>
                    {if $variations}
                        {foreach from=$variations item=variation key=artiqle}
                            {if $variation.reference_product}
                                <tr>
                                    <td></td>
                                    <td class="variations__item" data-th="{__("name")}">
                                        <b>{$variation.reference_product.name}</b>
                                    </td>
                                    <td class="cp-center variations__item " data-th="{__("cp_article")}" width="30%">
                                        <b>{$variation.reference_product.artiqle}</b>
                                    </td>
                                </tr>
                            {/if}
                            {if $variation.products}
                                {foreach from=$variation.products item=product}
                                    <tr class="variations__list">
                                        <td width="5%">
                                            <input type="checkbox" id="cp_checkbox_{$product.product_id}" name="chosen_products[{$product.product_id}]" value="{$variation.amount}"/>
                                        </td>
                                        <td class="variations__item" data-th="{__("name")}" width="50%">
                                            {$product.product}
                                        </td>
                                        <td class="cp-center variations__item " data-th="{__("cp_article")}" width="30%">
                                            {$product.product_code}
                                        </td>
                                    </tr>
                                {/foreach}
                            {/if}
                        {/foreach}
                    {/if}
                    {if $undefined_products}
                        {foreach from=$undefined_products item=undefined_product}
                            <tr class="variations__list">
                                <td width="5%"></td>
                                <td class="variations__item" data-th="{__("name")}" width="50%">
                                    <b>{$undefined_product.name}</b>
                                </td>
                                <td class="cp-center variations__item " data-th="{__("cp_article")}" width="30%">
                                    <b>{$undefined_product.artiqle}</b>
                                </td>
                                <td><b>{__("cp_undefined_product")}</b></td>
                            </tr>
                        {/foreach}
                    {/if}
                </table>
            </div>
        </fieldset>
        <div class="buttons-container">
		    {include file="buttons/button.tpl" but_role="submit" but_text=__("ok") but_name="dispatch[cp_generate_cart_from_file.finish]" but_meta="ty-btn__primary cm-process-items cm-dialog-closer ty-float-right" but_target_form="cp_variations_modal_form"}
        </div>
    </form>

    <script>
        $('#cp_variations_open_modal_link').click();
    </script>
<!--cp_variations_modal--></div>