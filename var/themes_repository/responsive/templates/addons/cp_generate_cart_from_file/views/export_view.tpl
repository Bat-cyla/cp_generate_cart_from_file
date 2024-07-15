<div id="cp_export_cart_modal">
    <form action="{""|fn_url}" method="post" name="cp_unload_file_form" class="form-horizontal " enctype="multipart/form-data">
            <div class="cp-row">
                <div class="cp-row-item-main">
                    {if $export_format == 'pdf_csv'}
                    <input type="radio" name="format" id="formatChoice1" value="csv_table" />
                    <label for="formatChoice1">{__("cp_csv_table")}</label>
                    <input type="radio" name="format" id="formatChoice2" value="pdf_cp" />
                    <label for="formatChoice2">{__("cp_pdf_commercial_proposal")}</label>
                    {else}
                        <input type="hidden" name="format" value="{$export_format}">
                    {/if}
                    <div class="cp-message">
                        {__("cp_export_view_message")}
                    </div>

                    <div class="buttons-container">
                        {include file="buttons/button.tpl" but_role="submit" but_text=__("download") but_name="dispatch[cp_generate_cart_export_file.generate]" but_meta="ty-btn__primary cm-process-items cm-dialog-closer" but_target_form="cp_unload_file_form"}
                        <form action="{""|fn_url}" method="post" name="email" class="form-horizontal" enctype="multipart/form-data">
                            <input type="email" id="emailField" name="email" class="cp-email-field">
                            <label for="emailField"></label>
                        </form>
                        {include file="buttons/button.tpl" but_role="submit" but_text=__("send") but_name="dispatch[cp_generate_cart_export_file.send_mail]" but_meta="ty-btn__primary cm-process-items cm-dialog-closer" but_target_form="email_address"}
                    </div>
                </div>
            </div>
    </form>
    <script>
        $('#cp_export_cart_open_modal_link').click();
    </script>

    <!--cp_export_cart_modal--></div>