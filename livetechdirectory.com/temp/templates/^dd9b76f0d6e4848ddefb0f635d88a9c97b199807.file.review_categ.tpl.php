<?php /* Smarty version Smarty-3.1.12, created on 2014-05-14 11:09:10
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/submit/review_categ.tpl" */ ?>
<?php /*%%SmartyHeaderCode:205236662953734ed62209b3-96221610%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dd9b76f0d6e4848ddefb0f635d88a9c97b199807' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/submit/review_categ.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '205236662953734ed62209b3-96221610',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'selected' => 0,
    'selected_parent' => 0,
    'CATEGORY_ID' => 0,
    'PARENT_ID' => 0,
    'categs_tree' => 0,
    'k' => 0,
    'categ_id' => 0,
    'v' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53734ed62cbdd8_59288884',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53734ed62cbdd8_59288884')) {function content_53734ed62cbdd8_59288884($_smarty_tpl) {?><?php if (!is_callable('smarty_function_math')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.math.php';
?>


<?php if ($_smarty_tpl->tpl_vars['selected']->value){?>
	<?php if (isset($_smarty_tpl->tpl_vars['selected_cat'])) {$_smarty_tpl->tpl_vars['selected_cat'] = clone $_smarty_tpl->tpl_vars['selected_cat'];
$_smarty_tpl->tpl_vars['selected_cat']->value = $_smarty_tpl->tpl_vars['selected']->value; $_smarty_tpl->tpl_vars['selected_cat']->nocache = null; $_smarty_tpl->tpl_vars['selected_cat']->scope = 0;
} else $_smarty_tpl->tpl_vars['selected_cat'] = new Smarty_variable($_smarty_tpl->tpl_vars['selected']->value, null, 0);?>
	<?php if (isset($_smarty_tpl->tpl_vars['selected_parent'])) {$_smarty_tpl->tpl_vars['selected_parent'] = clone $_smarty_tpl->tpl_vars['selected_parent'];
$_smarty_tpl->tpl_vars['selected_parent']->value = $_smarty_tpl->tpl_vars['selected_parent']->value; $_smarty_tpl->tpl_vars['selected_parent']->nocache = null; $_smarty_tpl->tpl_vars['selected_parent']->scope = 0;
} else $_smarty_tpl->tpl_vars['selected_parent'] = new Smarty_variable($_smarty_tpl->tpl_vars['selected_parent']->value, null, 0);?>
<?php }else{ ?>
	<?php if (isset($_smarty_tpl->tpl_vars['selected_cat'])) {$_smarty_tpl->tpl_vars['selected_cat'] = clone $_smarty_tpl->tpl_vars['selected_cat'];
$_smarty_tpl->tpl_vars['selected_cat']->value = $_smarty_tpl->tpl_vars['CATEGORY_ID']->value; $_smarty_tpl->tpl_vars['selected_cat']->nocache = null; $_smarty_tpl->tpl_vars['selected_cat']->scope = 0;
} else $_smarty_tpl->tpl_vars['selected_cat'] = new Smarty_variable($_smarty_tpl->tpl_vars['CATEGORY_ID']->value, null, 0);?>
	<?php if (isset($_smarty_tpl->tpl_vars['selected_parent'])) {$_smarty_tpl->tpl_vars['selected_parent'] = clone $_smarty_tpl->tpl_vars['selected_parent'];
$_smarty_tpl->tpl_vars['selected_parent']->value = $_smarty_tpl->tpl_vars['PARENT_ID']->value; $_smarty_tpl->tpl_vars['selected_parent']->nocache = null; $_smarty_tpl->tpl_vars['selected_parent']->scope = 0;
} else $_smarty_tpl->tpl_vars['selected_parent'] = new Smarty_variable($_smarty_tpl->tpl_vars['PARENT_ID']->value, null, 0);?>
<?php }?>
<select name="CATEGORY_ID" id="<?php echo smarty_function_math(array('equation'=>'rand(10,100)'),$_smarty_tpl);?>
"><?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['categs_tree']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" <?php if ((($_smarty_tpl->tpl_vars['categ_id']->value==$_smarty_tpl->tpl_vars['k']->value)&&($_smarty_tpl->tpl_vars['categ_id']->value!=0))){?> selected="selected" <?php }?> <?php if ($_smarty_tpl->tpl_vars['v']->value['closed']==1){?>disabled = "disabled" <?php }?>><?php echo $_smarty_tpl->tpl_vars['v']->value['val'];?>
</option><?php } ?></select><?php }} ?>