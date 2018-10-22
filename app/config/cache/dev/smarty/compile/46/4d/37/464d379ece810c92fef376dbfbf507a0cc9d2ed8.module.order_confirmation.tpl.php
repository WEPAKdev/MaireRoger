<?php /* Smarty version Smarty-3.1.19, created on 2017-11-22 21:45:55
         compiled from "module:paypal/views/templates/hook/order_confirmation.tpl" */ ?>
<?php /*%%SmartyHeaderCode:179655a15e203409463-23151378%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '464d379ece810c92fef376dbfbf507a0cc9d2ed8' => 
    array (
      0 => 'module:paypal/views/templates/hook/order_confirmation.tpl',
      1 => 1511375684,
      2 => 'module',
    ),
  ),
  'nocache_hash' => '179655a15e203409463-23151378',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'method' => 0,
    'transaction_id' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5a15e203483264_55255711',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a15e203483264_55255711')) {function content_5a15e203483264_55255711($_smarty_tpl) {?><!-- begin C:\wamp\www\RogerMaireLocal/modules/paypal/views/templates/hook/order_confirmation.tpl -->
<li id="paypal_transaction_id">
    <?php if ($_smarty_tpl->tpl_vars['method']->value=='BT') {?>
        <?php echo smartyTranslate(array('s'=>'Braintree transaction id :','mod'=>'paypal'),$_smarty_tpl);?>

    <?php } else { ?>
        <?php echo smartyTranslate(array('s'=>'Paypal transaction id :','mod'=>'paypal'),$_smarty_tpl);?>

    <?php }?>
    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['transaction_id']->value, ENT_QUOTES, 'UTF-8');?>

</li>
<!-- end C:\wamp\www\RogerMaireLocal/modules/paypal/views/templates/hook/order_confirmation.tpl --><?php }} ?>
