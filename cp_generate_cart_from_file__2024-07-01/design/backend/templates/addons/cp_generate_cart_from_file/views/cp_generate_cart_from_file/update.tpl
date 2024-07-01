{if $template_data}
    {assign var="id" value=$template_data.template_id}
{else}
    {assign var="id" value=0}
{/if}

{capture name="mainbox"}
    <form action="{""|fn_url}"
    method="post"
    class="form-horizontal form-edit"
    name="cp_template_form"
    enctype="multipart/form-data">

        <input type="hidden" class="cm-no-hide-input" name="fake" value="1" />
        <input type="hidden" class="cm-no-hide-input" name="template_data[template_id]" value="{$id}" />

        <div class="control-group">
            <label for="elm_template_name" class="control-label cm-required">{__("name")}</label>
            <div class="controls">
                <input type="text" 
                name="template_data[name]"
                id="elm_template_name"
                value="{$template_data.name}"
                size="25"/>
            </div>
        </div>
        
        <div class="control-group">
            <label for="upload-file" class="control-label">{__("upload_file")}:</label>
            <div class="controls">
                <input class="upload-file" type="file" name="cp_attachment[]" id="upload-file" accept="xls, xlsx" size="1"/>
            </div>
            {if $template_data.filename}
            <div class="controls">
                <a href="{$template_data.path_to_file}" target="_blank">{$template_data.filename}</a>&nbsp; (<a href="{"cp_generate_cart_from_file.get_file?attachment_id=`$template_data.attachment_id`"|fn_url}">{__("download")}</a>)
                &nbsp;
            </div>
            {/if}
        </div>
        
        {if !$id}
            <div class="control-group">
                <label class="control-label">{__("cp_template_default")}:</label>
                <div class="controls">
                    <input type="checkbox" checked="checked" name= "template_data[default_template]" value= "Y"/>
                </div>
            </div>
        {else}
            <div class="control-group">
                <label class="control-label">{__("cp_template_default")}:</label>
                <div class="controls">
                    {if $template_data.default_template === "Y"}
                        <input type="checkbox" checked="checked" name= "template_data[default_template]" value= "Y"/>
                    {else}
                        <input type="checkbox" name= "template_data[default_template]" value= "Y"/>
                    {/if}
                </div>
            </div>
        {/if}

        {capture name = "buttons"}
            {if $id}
                {capture name="tools_list"}
                    <li>{btn type="list"
                        text=__("delete")
                        class="cm-confirm"
                        href="cp_generate_cart_from_file.delete?template_id=`$id`"
                        method="POST"}
                    </li>
                {/capture}
            {/if}
            {dropdown content=$smarty.capture.tools_list}
        {/capture}

        {capture name="adv_buttons"}
            {if !$id}
                {include file="buttons/save_cancel.tpl"
                but_role="submit-link"
                but_target_form="cp_template_form"
                but_name="dispatch[cp_generate_cart_from_file.update]"}
            {else}
                {include file="buttons/save_cancel.tpl"
                but_name="dispatch[cp_generate_cart_from_file.update]"
                but_role="submit-link"
                but_target_form="cp_template_form"
                hide_first_button=$hide_first_button
                hide_second_button=$hide_second_button
                save=$id}
            {/if}
        {/capture}
    </form>
{/capture}


{include file="common/mainbox.tpl"
title=($id) ? $template_data.name : __("cp_add_template")
content=$smarty.capture.mainbox
adv_buttons = $smarty.capture.adv_buttons
buttons=$smarty.capture.buttons}