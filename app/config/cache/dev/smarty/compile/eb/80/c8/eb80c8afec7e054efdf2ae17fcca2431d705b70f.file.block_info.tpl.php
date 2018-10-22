<?php /* Smarty version Smarty-3.1.19, created on 2017-11-22 19:49:52
         compiled from "C:\wamp\www\RogerMaireLocal\modules\paypal\views\templates\admin\block_info.tpl" */ ?>
<?php /*%%SmartyHeaderCode:289015a15c6d0983d73-88664505%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eb80c8afec7e054efdf2ae17fcca2431d705b70f' => 
    array (
      0 => 'C:\\wamp\\www\\RogerMaireLocal\\modules\\paypal\\views\\templates\\admin\\block_info.tpl',
      1 => 1511375684,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '289015a15c6d0983d73-88664505',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'preference' => 0,
    'return_url' => 0,
    'need_rounding' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5a15c6d0ab4876_03460201',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a15c6d0ab4876_03460201')) {function content_5a15c6d0ab4876_03460201($_smarty_tpl) {?>
<div class="container-fluid">
    <div class="col-sm-12">
        <div class="paypal_block_info panel">
            <p><?php echo smartyTranslate(array('s'=>'If you have just created your PayPal account, check the email sent by PayPal to confirm your email address.','mod'=>'paypal'),$_smarty_tpl);?>
</p>
            <p><?php echo smartyTranslate(array('s'=>'If you encounter rounding issues with your orders, please change PrestaShop round mode in:','mod'=>'paypal'),$_smarty_tpl);?>
 <a target="_blank" href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['escape'][0][0]->smartyEscape($_smarty_tpl->tpl_vars['preference']->value,'javascript','UTF-8');?>
}"><?php echo smartyTranslate(array('s'=>'Preferences > General','mod'=>'paypal'),$_smarty_tpl);?>
</a> <?php echo smartyTranslate(array('s'=>'then change for:','mod'=>'paypal'),$_smarty_tpl);?>
</p>
            <p><b><?php echo smartyTranslate(array('s'=>'Round mode: "Round up away from zero, when it is half way there (recommended) "','mod'=>'paypal'),$_smarty_tpl);?>
</b></p>
            <p><b><?php echo smartyTranslate(array('s'=>'Round type: "Round on each item"','mod'=>'paypal'),$_smarty_tpl);?>
</b></p>
        </div>
    </div>
</div>

<div style="display: none;">
    <div id="content-rounding-settings">
        <form action="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['escape'][0][0]->smartyEscape($_smarty_tpl->tpl_vars['return_url']->value,'javascript','UTF-8');?>
" method="post" id="credential-configuration" class="bootstrap">

            <h4><?php echo smartyTranslate(array('s'=>'Warning','mod'=>'paypal'),$_smarty_tpl);?>
</h4>

            <p><?php echo smartyTranslate(array('s'=>'Your product rounding settings are not compliant with PayPal module.','mod'=>'paypal'),$_smarty_tpl);?>
</p>
            <p style='margin-bottom: 30px;'><?php echo smartyTranslate(array('s'=>'Without modification of your PrastaShop configuration, PayPal will round items from cart to your customers.','mod'=>'paypal'),$_smarty_tpl);?>
</p>

            <p>
                <button class="btn btn-default"  onclick="$.fancybox.close();return false;"><?php echo smartyTranslate(array('s'=>'I understand','mod'=>'paypal'),$_smarty_tpl);?>
</button>
                <button class="btn btn-info" name="save_rounding_settings"><?php echo smartyTranslate(array('s'=>'Change rounding settings','mod'=>'paypal'),$_smarty_tpl);?>
</button>
            </p>
        </form>
    </div>

</div>

<script type="text/javascript">

    function display_rounding()
    {
        $.fancybox.open([
            {
                type: 'inline',
                autoScale: true,
                minHeight: 30,
                content: $('#content-rounding-settings').html(),
            }
        ]);
    }

    $(document).ready(function(){

        var need_rounding = <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['escape'][0][0]->smartyEscape($_smarty_tpl->tpl_vars['need_rounding']->value,'html','UTF-8');?>
;

        $('#configuration_form input[name=paypal_sandbox]').change(function(event) {
            sandbox = $('#configuration_form input[name=paypal_sandbox]:checked').val();
            if (need_rounding && sandbox == 0) {
                display_rounding();
            }
        });

    });

</script><?php }} ?>
