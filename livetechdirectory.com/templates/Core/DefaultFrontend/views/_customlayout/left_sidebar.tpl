
<div class="phpld-col1"  style="width:{$sidebar1.width}{$sidebar1.type};">
    <div class="phpld-cbox">
        {foreach from=$widgets item=widget}
            {include file="views/_shared/widget.tpl" ID=$widget.ID  TITLE=$widget.SETTINGS.TITLE SHOW_TITLE=$widget.SETTINGS.SHOW_TITLE   CONTENT=$widget.CONTENT WIDGET_HEADING=$widgetheading}
            {/foreach}
    </div>
</div>