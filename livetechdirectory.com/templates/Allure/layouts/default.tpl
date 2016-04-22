﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        {* Document/Browser title *}
        <title>{$PAGE_TITLE}</title>
        {* Document character set *}
        <meta http-equiv="Content-Type" content="text/html; charset={$smarty.const.CHARSET}" />
        <script type="text/javascript">
            var DOC_ROOT = '{$smarty.const.FRONT_DOC_ROOT}';
        </script>
        {* CSS Style file *}
        
	<link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/style/column.css" />
	<link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/style/form.css" />
	<link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/style/main_min.css" />
        <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/style/colors/{$color}-theme.css" />
         <!--[if IE 7]>
   	<link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/style/ie7.css" />
	  <![endif]-->

        {*<link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.USE_TEMPLATE}/chosen/chosen.css" media="screen"  />*}
        <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/Core/DefaultFrontend/style/select2.css"  />
        <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/Core/DefaultFrontend/style/fg.menu.css" />
        <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/Core/DefaultFrontend/style/theme/jquery-ui-1.8.23.custom.css"  />


        {* Custom META tags *}
        {if $smarty.const.ENABLE_META_TAGS}
            {if !empty($MetaKeywords)}
                <meta name="keywords" content="{$MetaKeywords|strip_tags:false|escape|trim}" />
            {/if}
            {if !empty($MetaDescription)}
               <meta name="description" content="{$MetaDescription|strip_tags:false|trim}" />
            {/if}
            {if !empty($MetaAuthor)}
                <meta name="author" content="{$MetaAuthor}" />
            {/if}
            {if !empty($MetaCopyright)}
                <meta name="copyright" content="{$MetaCopyright|strip|escape|trim}" />
            {/if}
            {if !empty($MetaRobots)}
                <meta name="robots" content="{$MetaRobots|strip|escape|trim}" />
            {/if}
        {/if}

        {* Please keep this line for better version tracking *}
        <meta name="generator" content="PHP Link Directory {$smarty.const.CURRENT_VERSION}" />

        {* Live Bookmarks *}
         {$RSS}

        {if $smarty.const.USE_CDN}
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
        {else}
        <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery.js"></script>
        <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery-ui.js"></script>
	    {/if}

        {literal}
            <script type="text/javascript">
             var $ = jQuery.noConflict();
            </script>
        {/literal}

        <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery.select2.js"></script>
        <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery.fg.menu.js"></script>

        {$phpldThemeStyles}
        {$phpldJavascripts}
        {$phpldStylesheets}
    </head>
    <body>
        <div class="phpld-wbox">
            {$USER_PANEL}
            <div class="phpld-clearfix"></div>

            <div class="header">
                <div class="headerLogo">
                    {$HEADER_LOGO}
                </div>
                {$HEADER_SEARCH_FORM}
            </div>
        </div>
        {include file="views/_shared/messages.tpl"}
        <div class="phpld-clearfix"></div>

        <div id="nav">
            <div class="phpld-wbox">
                <div class="phpld-hlist">
                    {$MAIN_MENU}
               </div>
            </div>
        </div>

        <div class="phpld-wbox">
        <div class="phpld-clearfix"></div>
        {strip}

        <div class="content-wrapper">
            <div class="path">
                {$BREADCRUMBS}
            </div>
            {$FLASH_MESSENGER}
	    <div class="phpld-column linearize-level-1">
		{if $layout_type eq "custom"}
		{include file="views/_customlayout/left_sidebar.tpl" widgets=$widget_list.LEFT_COLUMN}
		{include file="views/_customlayout/right_sidebar.tpl" widgets=$widget_list.RIGHT_COLUMN}
		{include file="views/_customlayout/main.tpl" widgets=$widget_list}
		
		{else}
		    {$content}
		{/if}
	    </div>
        </div>
        <div class="footer">
            Powered By: <a href="http://www.phplinkdirectory.com" title="powered by PHP Link Directory"> PHP Link Directory </a>
        </div>
    {$smarty.const.GOOGLE_ANALYTICS}
    {$LINK_CLICK_TRACKER_CODE}

</body>
</html>
{/strip}