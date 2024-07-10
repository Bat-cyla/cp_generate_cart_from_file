<div id="cp_generate_cart_modal">
    <form action="{""|fn_url}" method="post" name="cp_upload_file_form" class="form-horizontal cm-ajax" enctype="multipart/form-data">
    <input type="hidden" name="result_ids" value="cp_variations_modal" />
        <fieldset>
            <div class="cp-row">
                <div class="cp-row-item-main">
                    <div class="control-group">
                        <label for="cp-controls" class="control-label"><h3>{__("select_of_column_order")}:</h3></label>
                        <div id="cp-controls">
                            <div class="cp-row">
                                <div class="cp-row-item-fields">
                                    <div class="cp-controls">
                                        <div class="cp-row-field">
                                            <h4><label for="field_name" class="control-label">{__("cp_name")}</label></h4>
                                            <input type="text" maxlength="5" name="array_fields[name]" size="1" placeholder="A" class="input-micro cp-input" id="field_name">
                                        </div>
                                    </div>
                                    <div class="cp-controls">
                                        <div class="cp-row-field">
                                            <h4><label for="field_artiqle" class="control-label cp-required">{__("cp_artiqle")}</label></h4>
                                            <input type="text" maxlength="5" name="array_fields[artiqle]" size="1" placeholder="B" class="input-micro cp-input" id="field_artiqle">
                                        </div>
                                    </div>
                                    <div class="cp-controls">
                                        <div class="cp-row-field">
                                            <h4><label for="field_amount" class="control-label cp-required">{__("cp_qty")}</label></h4>
                                            <input type="text" maxlength="5" name="array_fields[amount]" size="1" placeholder="C" class="input-micro cp-input" id="field_amount">
                                        </div>
                                    </div>
                                </div>
                                <div class="cp-row-item-empty"></div>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <input class="cp-upload-file" type="file" name="cp_generate_attach[]" id="upload-file" accept=".xls, .xlsx, .csv" size="1"/>
                        </div>
                    </div>
                    {if $template_data.attachment_id}
                        <div class="cp-file-template">
                            {__("cp_load_file_instruction")}<br>
                            <a class="cp-upload-template-link" href="{"cp_generate_cart_from_file.get_file?attachment_id=`$template_data.attachment_id`"|fn_url}"><b>({__("cp_load_file")})</b></a>
                        </div>
                    {/if}
                </div>
                <div class="cp-row-item-side">
                    <div class="cp-img">
                        <h4>{__("cp_column_example")}:</h4><br>
                        <img src="design/themes/responsive/media/images/addons/cp_generate_cart_from_file/images/template.png" alt="Image is not founded">
                        <p class="cp-warning">{__("cp_warning")}</p>
                    </div>
                </div>
            </div>
        </fieldset>
        <div class="buttons-container">
		    {include file="buttons/button.tpl" but_role="submit" but_text=__("load") but_name="dispatch[cp_generate_cart_from_file.generate]" but_meta="cp-submit ty-btn__primary cm-process-items cm-dialog-closer" but_target_form="cp_upload_file_form"}
            <span class="ty-close-text"><a class="cm-dialog-closer ty-btn ty-float-right">{__("cancel")}</a></span>
        </div>
    </form>

    <script>
        $('#cp_generate_cart_open_modal_link').click();
    </script>
<!--cp_generate_cart_modal--></div>