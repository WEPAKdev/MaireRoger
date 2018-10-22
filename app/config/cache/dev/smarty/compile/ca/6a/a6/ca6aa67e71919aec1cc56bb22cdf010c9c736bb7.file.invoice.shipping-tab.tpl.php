<?php /* Smarty version Smarty-3.1.19, created on 2017-11-22 20:51:11
         compiled from "C:\wamp\www\RogerMaireLocal\pdf\\invoice.shipping-tab.tpl" */ ?>
<?php /*%%SmartyHeaderCode:159085a15d52fb1a9d7-10439145%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ca6aa67e71919aec1cc56bb22cdf010c9c736bb7' => 
    array (
      0 => 'C:\\wamp\\www\\RogerMaireLocal\\pdf\\\\invoice.shipping-tab.tpl',
      1 => 1509574322,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '159085a15d52fb1a9d7-10439145',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'carrier' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5a15d52fdd9bd8_74338023',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a15d52fdd9bd8_74338023')) {function content_5a15d52fdd9bd8_74338023($_smarty_tpl) {?>
<table id="shipping-tab" width="100%">
	<tr>
		<td class="shipping center small grey bold" width="44%"><?php echo smartyTranslate(array('s'=>'Carrier','d'=>'Shop.Pdf','pdf'=>'true'),$_smarty_tpl);?>
</td>
		<td class="shipping center small white" width="56%"><?php echo $_smarty_tpl->tpl_vars['carrier']->value->name;?>
</td>
	</tr>
</table>
<?php }} ?>
