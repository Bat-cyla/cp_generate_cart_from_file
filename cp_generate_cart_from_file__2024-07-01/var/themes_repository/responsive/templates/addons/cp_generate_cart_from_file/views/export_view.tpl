<div id="cp_export_cart_modal">
    <form action="{""|fn_url}" method="post" name="cp_unload_file_form" class="form-horizontal cm-ajax" enctype="multipart/form-data">
            <div class="cp-row">
                <div class="cp-row-item-main">
                    <input type="radio" name="format" id="formatChoice1" value="csv_table" />
                    <label for="formatChoice1">{__("cp_csv_table")}</label>
                    <input type="radio" name="format" id="formatChoice2" value="pdf_cp" />
                    <label for="formatChoice2">{__("cp_pdf_commercial_proposal")}</label>

                    <div class="cp-message">
                        {__("cp_export_view_message")}
                    </div>

                    <div class="buttons-container">
                        {include file="buttons/button.tpl" but_role="submit" but_text=__("download") but_name="dispatch[cp_generate_cart_export_file.generate]" but_meta="ty-btn__primary cm-process-items cm-dialog-closer" but_target_form="cp_unload_file_form"}
                        <span class="ty-close-text"><a class="cm-dialog-closer ty-btn ty-float-right">{__("send")}</a></span>
                    </div>
                </div>
            </div>
    </form>
    <script>
        $('#cp_export_cart_open_modal_link').click();
    </script>

    <!--cp_export_cart_modal--></div>