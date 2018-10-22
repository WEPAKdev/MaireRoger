<?php /* Smarty version Smarty-3.1.19, created on 2017-11-11 22:52:17
         compiled from "C:\wamp64\www\RogerMaireLocal\themes\RogerMaire\templates\checkout\_partials\cart-summary-items-subtotal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:68405a077111cff659-45724371%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '780e115c052c6fba16b66619fdba5f5dafc4fbd7' => 
    array (
      0 => 'C:\\wamp64\\www\\RogerMaireLocal\\themes\\RogerMaire\\templates\\checkout\\_partials\\cart-summary-items-subtotal.tpl',
      1 => 1510191507,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '68405a077111cff659-45724371',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'cart' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5a077111d286b6_19171506',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a077111d286b6_19171506')) {function content_5a077111d286b6_19171506($_smarty_tpl) {?>

  <div class="card-block cart-summary-line cart-summary-items-subtotal clearfix" id="items-subtotal">
    <span class="label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cart']->value['summary_string'], ENT_QUOTES, 'UTF-8');?>
</span>
    <span class="value"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cart']->value['subtotals']['products']['amount'], ENT_QUOTES, 'UTF-8');?>
</span>
  </div>

<?php }} ?>
