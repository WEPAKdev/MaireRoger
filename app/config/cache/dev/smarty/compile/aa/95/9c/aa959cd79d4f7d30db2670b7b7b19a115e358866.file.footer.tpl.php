<?php /* Smarty version Smarty-3.1.19, created on 2017-11-22 20:51:09
         compiled from "C:\wamp\www\RogerMaireLocal\pdf\\footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:278705a15d52d5d3038-70069916%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aa959cd79d4f7d30db2670b7b7b19a115e358866' => 
    array (
      0 => 'C:\\wamp\\www\\RogerMaireLocal\\pdf\\\\footer.tpl',
      1 => 1509574322,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '278705a15d52d5d3038-70069916',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'available_in_your_account' => 0,
    'shop_address' => 0,
    'shop_phone' => 0,
    'shop_fax' => 0,
    'shop_details' => 0,
    'free_text' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5a15d52dcfef47_42958204',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a15d52dcfef47_42958204')) {function content_5a15d52dcfef47_42958204($_smarty_tpl) {?>
<table style="width: 100%;">
	<tr>
		<td style="text-align: center; font-size: 6pt; color: #444;  width:100%;">
			<?php if ($_smarty_tpl->tpl_vars['available_in_your_account']->value) {?>
				<?php echo smartyTranslate(array('s'=>'An electronic version of this invoice is available in your account. To access it, log in to our website using your e-mail address and password (which you created when placing your first order).','d'=>'Shop.Pdf','pdf'=>'true'),$_smarty_tpl);?>

				<br />
			<?php }?>
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['escape'][0][0]->smartyEscape($_smarty_tpl->tpl_vars['shop_address']->value,'html','UTF-8');?>
<br />

			<?php if (!empty($_smarty_tpl->tpl_vars['shop_phone']->value)||!empty($_smarty_tpl->tpl_vars['shop_fax']->value)) {?>
				<?php echo smartyTranslate(array('s'=>'For more assistance, contact Support:','d'=>'Shop.Pdf','pdf'=>'true'),$_smarty_tpl);?>
<br />
				<?php if (!empty($_smarty_tpl->tpl_vars['shop_phone']->value)) {?>
					<?php echo smartyTranslate(array('s'=>'Tel: %s','sprintf'=>array($_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['escape'][0][0]->smartyEscape($_smarty_tpl->tpl_vars['shop_phone']->value,'html','UTF-8')),'d'=>'Shop.Pdf','pdf'=>'true'),$_smarty_tpl);?>

				<?php }?>

				<?php if (!empty($_smarty_tpl->tpl_vars['shop_fax']->value)) {?>
					<?php echo smartyTranslate(array('s'=>'Fax: %s','sprintf'=>array($_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['escape'][0][0]->smartyEscape($_smarty_tpl->tpl_vars['shop_fax']->value,'html','UTF-8')),'d'=>'Shop.Pdf','pdf'=>'true'),$_smarty_tpl);?>

				<?php }?>
				<br />
			<?php }?>

			<?php if (isset($_smarty_tpl->tpl_vars['shop_details']->value)) {?>
				<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['escape'][0][0]->smartyEscape($_smarty_tpl->tpl_vars['shop_details']->value,'html','UTF-8');?>
<br />
			<?php }?>

			<?php if (isset($_smarty_tpl->tpl_vars['free_text']->value)) {?>
				<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['escape'][0][0]->smartyEscape($_smarty_tpl->tpl_vars['free_text']->value,'html','UTF-8');?>
<br />
			<?php }?>
		</td>
	</tr>
</table>

<?php }} ?>
