{assign var="inline" value=true}
{if $items}
  <div class="et-top-block {if $block.user_class} {$block.user_class}{/if}">
    <ul class="ty-text-links{if $inline && !$vs_submenu} ty-text-links_show_inline{/if}">
      {foreach from=$items item="menu" name="vs_menu"}{strip}
        {$et_menu=$menu.et_menu}
        <li class="ty-text-links__item ty-level-{$menu.level|default:0} level-{$menu.level|default:0}{if $menu.active} ty-text-links__active{/if} {if $et_menu.color.enabled == "Y" && $et_menu.color.color_type|default:"S"!="C"}et_top_style_color{/if} ">
          {capture name="et_icon" assign="et_icon"}{strip}
            {if $et_menu.icon.enabled == "Y" && $et_menu.icon.value}
              <i class="{$et_menu.icon.value} et_menu_icon et_menu_icon_{$et_menu.et_menu_id}"></i>
            {/if}
            {if $et_menu.icon.color_type|default:"S"=="C"} 
            <style>
              .et-top-block .ty-text-links__item a.ty-text-links__a .et_menu_icon_{$et_menu.et_menu_id}{
                color: {$et_menu.icon.color};
              }
            </style>
            {/if}
          {/strip}{/capture}
          {if $et_menu.color.enabled == "Y" && $et_menu.color.color_type|default:"S"=="C"}
            <style>
              .et-top-block .ty-text-links__item a{
                padding: 5px 0 5px;
                margin-right: 15px;
              }
              .et-top-block .ty-text-links__item:last-child a{
                margin-right: 0;
              }
              .et-top-block .ty-text-links__item a.et_menu_color_{$et_menu.et_menu_id}{
                color: {$et_menu.color.color};
                background: {$et_menu.color.bkg};
                padding: 5px 10px;
                margin-right: 0;
              }
              .et-top-block .ty-text-links__item{
                margin:0;
              }
            </style>
          {else}
            <style>
              .et-top-block .ty-text-links__item{
                {if $language_direction == 'rtl'}
                  margin-left: 15px;
                {else}
                  margin-right: 15px;
                {/if}
              }
              .et-top-block .ty-text-links__item:last-child{
                {if $language_direction == 'rtl'}
                  margin-left: 0px;
                {else}
                  margin-right: 0px;
                {/if}
              }
            </style>
          {/if}

          <a {if $menu.href}href="{$menu.href|fn_url}"{/if} {if $menu.new_window}target="_blank"{/if} class="item-{$smarty.foreach.vs_menu.index} {if $menu.class}{$menu.class}{/if} ty-text-links__a et_menu_color_{$et_menu.et_menu_id}">
            {if $et_icon|trim}{$et_icon nofilter}{/if}<span>{$menu.item}</span>
          </a> 
          {if $menu.subitems}
            {include file="blocks/menu/text_links.tpl" items=$menu.subitems vs_submenu=true}
          {/if}
        </li>{/strip}{/foreach}
    </ul>
  </div>
{/if}