<?php /* Smarty version Smarty-3.1.19, created on 2017-11-22 21:45:15
         compiled from "C:\wamp\www\RogerMaireLocal\admin258korw9q\themes\default\template\content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:196405a15e1db2087f3-92087916%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '887783cb471593ab9780ac056aee58793165f873' => 
    array (
      0 => 'C:\\wamp\\www\\RogerMaireLocal\\admin258korw9q\\themes\\default\\template\\content.tpl',
      1 => 1510857574,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '196405a15e1db2087f3-92087916',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5a15e1db2456f4_72213799',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a15e1db2456f4_72213799')) {function content_5a15e1db2456f4_72213799($_smarty_tpl) {?>
<div id="ajax_confirmation" class="alert alert-success hide"></div>

<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div>
<?php }} ?>
