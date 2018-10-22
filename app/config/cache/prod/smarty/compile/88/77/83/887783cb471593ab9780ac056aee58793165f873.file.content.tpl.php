<?php /* Smarty version Smarty-3.1.19, created on 2017-11-21 13:11:43
         compiled from "C:\wamp\www\RogerMaireLocal\admin258korw9q\themes\default\template\content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:207315a1417ffc0ba73-75737440%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '887783cb471593ab9780ac056aee58793165f873' => 
    array (
      0 => 'C:\\wamp\\www\\RogerMaireLocal\\admin258korw9q\\themes\\default\\template\\content.tpl',
      1 => 1509574300,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '207315a1417ffc0ba73-75737440',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5a1417ffc0ba79_73729253',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a1417ffc0ba79_73729253')) {function content_5a1417ffc0ba79_73729253($_smarty_tpl) {?>
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
