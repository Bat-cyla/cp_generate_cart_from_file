

{include file="buttons/button.tpl" but_text=__("load_template_file") but_meta="ty-btn__primary cm-process-items cp-generate-cart-page-link"}

{if $addons.cp_generate_cart_from_file.PDF_export=='Y' || $addons.cp_generate_cart_from_file.CSV_export=='Y'}
{include file="buttons/button.tpl" but_text=__("unload_template_file") but_meta="ty-btn__primary cm-process-items cp-export-cart-page-link"}
{/if}