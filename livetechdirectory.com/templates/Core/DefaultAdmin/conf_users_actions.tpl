{* Error and confirmation messages *}
{include file="messages.tpl"}

{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="edit_article_form" validators=$validators}

{strip}
{if (isset($error) and $error gt 0) or !empty($sql_error)}
   <div class="error block">
      <h2>{l}Error{/l}</h2>
      <p>{l}An error occured while saving.{/l}</p>
      {if !empty($errorMsg)}
         <p>{$errorMsg|escape}</p>
      {/if}
      {if !empty($sql_error)}
         <p>{l}The database server returned the following message:{/l}</p>
         <p>{$sql_error|escape}</p>
      {/if}
   </div>
{/if}

{if $posted}
   <div class="success block">
      {l}User saved.{/l}
   </div>
{/if}

<div class="block">
   <form method="post" action="">
   <!--<table class="formPage">
   <tbody>
        {foreach from=$default item=def key=k}
    	 <tr>
    	 	 <td class="types" colspan="2">{$user_types[$k]}</td>
    	 </tr>
    	{section name=i loop=$def}
    	 <tr>
             <td class="label">
             	<label for="ACTION">{$def[i].NAME}:</label>
             </td>
             <td class="smallDesc">
             	{assign var='id' value=$def[i].ID}
                <input type="checkbox" id="{$def[i].ID}" name="{$def[i].ID}" value="1" 
            	{if $actual.$id neq ''}
            		{if $actual.$id eq 1}checked="checked"{/if}
            	{else}
            		{if $def[i].VALUE eq 1}checked="checked"{/if}
            	{/if}
            	/>
        	 </td>
    	 </tr>
		{/section}
		{/foreach}
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-user-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-user-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save user{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>-->
        
   <table class="formPage">
   <thead>
        <tr>
            <td class="listHeader">User type</td>
            {section name=i loop=$default[0]}
                <td class="listHeader">{$default[0][i].NAME}</td>
            {/section}
        </tr>
       </thead>
       
        {foreach from=$default item=def key=k}
           
           {if ($k eq 0) || ($k eq $level)}
         <tr>
             <td class="listHeader" style="color: #000;">{$user_types[$k]}</td>
     
        
        {section name=i loop=$def}
     
             <td class="smallDesc">
                {assign var='id' value=$def[i].ID}
                <input type="checkbox" id="{$def[i].ID}" name="{$def[i].ID}" value="1" 
                {if $actual.$id neq ''}
                    {if $actual.$id eq 1}checked="checked"{/if}
                {else}
                    {if $def[i].VALUE eq 1}checked="checked"{/if}
                {/if}
                />
             </td>
         
        {/section}
        
        </tr>
        {/if}
        {/foreach}
        
   </table>
   <table>
   <tr>
      <td><input type="reset" id="reset-user-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
      <td><input type="submit" id="send-user-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save user{/l}" class="button" /></td>
   </tr>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
   <input type="hidden" name="submit_session" value="{$submit_session}" />
   </form>
</div>
{if (isset($level) and $level eq 2)}
{if (isset($error) and $error gt 0) or !empty($sql_error)}
   <div class="error block">
      <h2>{l}Error{/l}</h2>
      <p>{l}An error occured while saving.{/l}</p>
      {if !empty($errorMsg)}
         <p>{$errorMsg|escape}</p>
      {/if}
      {if !empty($sql_error)}
         <p>{l}The database server returned the following message:{/l}</p>
         <p>{$sql_error|escape}</p>
      {/if}
   </div>
{/if}

{if $posted}
   <div class="success block">
      {$posted|escape}
   </div>
{/if}

{if $WARN}
<div class="block">
   <form method="post" action="{$smarty.const.DOC_ROOT}/conf_user_permissions.php{if !empty($u)}?u={$u}{/if}" name="delete">
   <input type="hidden" id="warn" name="warn" value="1" class="hidden" />
   <input type="hidden" name="CATEGORY_ID" value="{$CATEGORY_ID}" class="hidden" />
   <table class="formPage">
   <thead>
      <tr>
         <th colspan="2">{$permsTitleMsg|escape}</th>
      </tr>
   </thead>

   <tbody>
      <tr>
         <td class="notice" colspan="2">
            <p>Category {$CATEGORY} is parent to {$CHILD_CATEGORIES} {if $CHILD_CATEGORIES eq 0}category{else}categories{/if} that this user has permission to.</p>
            <p>Proceed to grant permission to category {$CATEGORY} and delete the existing permission to the {$CHILD_CATEGORIES} {if $CHILD_CATEGORIES eq 0}category{else}categories{/if}?</p>
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td>
            <input type="submit" name="cancel" value="{l}Cancel{/l}" title="{l}Cancel{/l}" class="button" />
         </td>
         <td>
            <input type="submit" name="proceed" value="{l}Proceed{/l}" title="{l}Grant permission to parent category including child categories{/l}" class="button" />
         </td>
      </tr>
   </tfoot>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
   <input type="hidden" name="submit_session" value="{$submit_session}" />
   </form>
</div>
{else}
<div class="block">
   <form method="post" action="{$smarty.const.DOC_ROOT}/conf_user_permissions.php?action=N{if !empty($u)}&amp;u={$u}{/if}" id="edit_user_actions_form">
   <table class="formPage">
   <thead>
      <tr>
         <th colspan="2">{$permsTitleMsg|escape}</th>
      </tr>
   </thead>
   <tbody>
      <tr>
         <td class="label"><label for="CATEGORY_ID">{l}Category{/l}:</label></td>
         <td class="smallDesc">
            {* Load category selection *}
            {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl"}
            {*validate form="conf_user_permissions" id="v_CATEGORY_ID" message=$smarty.capture.no_url_in_top*}
            {*validate form="conf_user_permissions" id="v_CATEGORY_ID_U" message=$smarty.capture.permission_not_unique*}
            {*validate form="conf_user_permissions" id="v_CATEGORY_ID_S" message=$smarty.capture.permission_is_sub_cat*}
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td>
            <input type="reset" id="reset-perms-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" />
         </td>
         <td>
            <input type="submit" id="send-perms-submit" name="add" value="{l}Add permission{/l}" alt="{l}Add permission{/l}" title="{l}Add permission to selected category{/l}" class="button" />
         </td>
      </tr>
   </tfoot>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
   <input type="hidden" name="submit_session" value="{$submit_session}" />
   </form>
</div>
{/if}

<div class="block">
   <table class="list">
   <thead>
      <tr>
         {foreach from=$columns key=col item=name}
            {if $ENABLE_REWRITE or $col ne 'TITLE_URL'}
               <th class="listHeader" id="{$col}"><a href="{$smarty.const.DOC_ROOT}/dir_links.php{if isset($columnURLs) and is_array($columnURLs)}?{$columnURLs.$col}{/if}" title="{l}Sort by{/l}: {$name|escape|trim}" class="sort{if $SORT_FIELD eq $col and $requestOrder eq 1} {$smarty.const.SORT_ORDER}{/if}">{$name|escape|trim}</a></th>
               {/if}
         {/foreach}
         <th class="last-child">{l}Action{/l}</th>
      </tr>
   </thead>

   <tbody>
      {foreach from=$list item=row key=idperm}
         <tr class="{cycle values="odd,even"}">
            {foreach from=$columns key=col item=name}
               {if $ENABLE_REWRITE or $col ne 'TITLE_URL'}
               <td>
                  {if $col eq 'CATEGORY_PATH'}
                     {foreach from=$row.$col item=category name=path}
                        {if $smarty.foreach.path.iteration gt 2} &gt; {/if}
                        {if not $smarty.foreach.path.first}
                           {$category.TITLE|escape|trim}
                        {/if}
                     {/foreach}
                  {else}
                     {$row.$col|escape}
                  {/if}
               </td>
               {/if}
            {/foreach}
            <td class="last-child"><a id="remove-userperms-{$idperm}" href="{$smarty.const.DOC_ROOT}/conf_user_permissions.php?action=D:{$idperm}&u={$u}" onclick="return link_rm_confirm('{escapejs}{l}Are you sure you want to remove this permission?{/l}{/escapejs}');" title="{l}Remove Permission{/l}" class="action delete"><span>{l}Delete{/l}</span></a></td>
         </tr>
      {foreachelse}
         <tr>
            <td colspan="{$col_count}" class="norec">{l}No records found.{/l}</td>
         </tr>
      {/foreach}
   </tbody>
   </table>

   {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/list_pager.tpl"}
</div>
{/if}
{/strip}