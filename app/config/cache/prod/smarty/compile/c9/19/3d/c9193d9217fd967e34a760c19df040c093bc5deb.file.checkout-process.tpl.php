<?php /* Smarty version Smarty-3.1.19, created on 2017-11-21 11:26:51
         compiled from "C:\wamp\www\RogerMaireLocal\themes\RogerMaire\templates\checkout\checkout-process.tpl" */ ?>
<?php /*%%SmartyHeaderCode:52405a13ff6b9952d3-83952879%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c9193d9217fd967e34a760c19df040c093bc5deb' => 
    array (
      0 => 'C:\\wamp\\www\\RogerMaireLocal\\themes\\RogerMaire\\templates\\checkout\\checkout-process.tpl',
      1 => 1510191507,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '52405a13ff6b9952d3-83952879',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'steps' => 0,
    'step' => 0,
    'index' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5a13ff6b9d21d3_54719133',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a13ff6b9d21d3_54719133')) {function content_5a13ff6b9d21d3_54719133($_smarty_tpl) {?>
<?php  $_smarty_tpl->tpl_vars["step"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["step"]->_loop = false;
 $_smarty_tpl->tpl_vars["index"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['steps']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["step"]->key => $_smarty_tpl->tpl_vars["step"]->value) {
$_smarty_tpl->tpl_vars["step"]->_loop = true;
 $_smarty_tpl->tpl_vars["index"]->value = $_smarty_tpl->tpl_vars["step"]->key;
?>
  <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['render'][0][0]->smartyRender(array('identifier'=>$_smarty_tpl->tpl_vars['step']->value['identifier'],'position'=>($_smarty_tpl->tpl_vars['index']->value+1),'ui'=>$_smarty_tpl->tpl_vars['step']->value['ui']),$_smarty_tpl);?>

<?php } ?>
<?php }} ?>
