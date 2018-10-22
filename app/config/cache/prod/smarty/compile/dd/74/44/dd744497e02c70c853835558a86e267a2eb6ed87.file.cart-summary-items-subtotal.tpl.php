<?php /* Smarty version Smarty-3.1.19, created on 2017-11-17 17:10:58
         compiled from "C:\wamp\www\RogerMaireLocal\themes\RogerMaire\templates\checkout\_partials\cart-summary-items-subtotal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:152555a0f0a127d9986-21052999%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dd744497e02c70c853835558a86e267a2eb6ed87' => 
    array (
      0 => 'C:\\wamp\\www\\RogerMaireLocal\\themes\\RogerMaire\\templates\\checkout\\_partials\\cart-summary-items-subtotal.tpl',
      1 => 1510191507,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '152555a0f0a127d9986-21052999',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'cart' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5a0f0a12816881_07460508',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a0f0a12816881_07460508')) {function content_5a0f0a12816881_07460508($_smarty_tpl) {?>

  <div class="card-block cart-summary-line cart-summary-items-subtotal clearfix" id="items-subtotal">
    <span class="label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cart']->value['summary_string'], ENT_QUOTES, 'UTF-8');?>
</span>
    <span class="value"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cart']->value['subtotals']['products']['amount'], ENT_QUOTES, 'UTF-8');?>
</span>
  </div>

<?php }} ?>
