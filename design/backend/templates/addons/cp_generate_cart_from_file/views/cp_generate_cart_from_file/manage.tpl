{capture name="mainbox"}
    <form action="{""|fn_url}" method="post" name="templates_form" class="cm-processed-form" enctype="multipart/form-data">
    <input type="hidden" name="fake" value="1" />
    
    {if $templates}
    <table class="table table-middle content_templates">
        <thead>
            <tr>
                <th>
                    {__("id")}
                </th>
                <th>
                    {__("name")}
                </th>
                <th>
                    {__("date")}
                </th>
                <th width="10%">
                    {__("default")}
                </th>
                <th width="6%" class="mobile-hide">&nbsp;</th>
            </tr>
        </thead>
    {foreach from=$templates item=template}
        <tr>
            <td id="template_id" class="{$no_hide_input}" data-th="{__("id")}">
                {$template.template_id}
            </td>
            <td class="{$no_hide_input}" data-th="{__("name")}">
                <a class="row-status"
                href="{"cp_generate_cart_from_file.update?template_id=`$template.template_id`"|fn_url}">
                    {$template.name}
                </a>
                {include file="views/companies/components/company_name.tpl" object=$template}
            </td>
            <td width="15%" data-th="{__("date")}">
                <div>{$template.timestamp|date_format:"`$settings.Appearance.date_format`"}</div>
            </td>
            <td class="{$no_hide_input}" data-th="{__("default")}">
                    {$template.default_template}
            </td>
            <td width="6%" class="mobile-hide">
                {capture name="tools_list"}
                    <li>
                        {btn type="list"
                            text=__("edit")
                            href="cp_generate_cart_from_file.update?template_id=`$template.template_id`"}
                    </li>
                    <li>
                        {btn type="list"
                            class="cm-confirm"
                            text=__("delete")
                            href="cp_generate_cart_from_file.delete?template_id=`$template.template_id`"
                            method="POST"}
                    </li>
                {/capture}
                    <div class="hidden-tools">
                        {dropdown content=$smarty.capture.tools_list}
                    </div>
            </td>
        </tr>
    {/foreach}
    </table>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}
    {capture name="adv_buttons"}
        {include file="common/tools.tpl" tool_href="cp_generate_cart_from_file.update" prefix="top" hide_tools="true" title=__("cp_import_template") icon="icon-plus"}
    {/capture}
    </form>
{/capture}
    
{include file="common/mainbox.tpl" title=__("cp_uploaded_templates") content=$smarty.capture.mainbox adv_buttons=$smarty.capture.adv_buttons}