<?php /* Smarty version Smarty-3.1.19, created on 2017-11-21 11:27:42
         compiled from "C:\wamp\www\RogerMaireLocal\mails\en\order_conf_product_list.txt" */ ?>
<?php /*%%SmartyHeaderCode:29845a13ff9e791e78-78926818%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c7de52b24439e20c091995a9aa091d878f4caaa9' => 
    array (
      0 => 'C:\\wamp\\www\\RogerMaireLocal\\mails\\en\\order_conf_product_list.txt',
      1 => 1509574316,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '29845a13ff9e791e78-78926818',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'list' => 0,
    'product' => 0,
    'customization' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5a13ff9e8c2976_56671990',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a13ff9e8c2976_56671990')) {function content_5a13ff9e8c2976_56671990($_smarty_tpl) {?><?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
						<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['reference'], ENT_QUOTES, 'UTF-8');?>


						<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['name'], ENT_QUOTES, 'UTF-8');?>


						<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['price'], ENT_QUOTES, 'UTF-8');?>

						<?php $_smarty_tpl->_capture_stack[0][] = array("productPriceBlock", null, null); ob_start(); ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>'displayProductPriceBlock','product'=>$_smarty_tpl->tpl_vars['product']->value,'type'=>"unit_price"),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?><?php echo htmlspecialchars(trim(preg_replace('!<[^>]*?>!', ' ', Smarty::$_smarty_vars['capture']['productPriceBlock'])), ENT_QUOTES, 'UTF-8');?>


						<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['quantity'], ENT_QUOTES, 'UTF-8');?>


						<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['price'], ENT_QUOTES, 'UTF-8');?>


	<?php  $_smarty_tpl->tpl_vars['customization'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['customization']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product']->value['customization']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['customization']->key => $_smarty_tpl->tpl_vars['customization']->value) {
$_smarty_tpl->tpl_vars['customization']->_loop = true;
?>
							<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['customization']->value['customization_text'], ENT_QUOTES, 'UTF-8');?>


							<?php if (count($_smarty_tpl->tpl_vars['product']->value['customization'])>1) {?>
								<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['customization_quantity'], ENT_QUOTES, 'UTF-8');?>

							<?php }?>
	<?php } ?>
<?php } ?>
<?php }} ?>
