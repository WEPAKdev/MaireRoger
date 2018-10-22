<?php /* Smarty version Smarty-3.1.19, created on 2017-11-22 21:46:36
         compiled from "modules\homecategories\views\templates\hook\homecategories.tpl" */ ?>
<?php /*%%SmartyHeaderCode:139205a15e22c833f72-68012869%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '32f40fa07f7060d4a9eb951d0149e4584856d7d0' => 
    array (
      0 => 'modules\\homecategories\\views\\templates\\hook\\homecategories.tpl',
      1 => 1510742954,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '139205a15e22c833f72-68012869',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'categories' => 0,
    'categorie' => 0,
    'link' => 0,
    'marque' => 0,
    'categoryLink' => 0,
    'cat_img_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5a15e22c9a1972_44944040',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a15e22c9a1972_44944040')) {function content_5a15e22c9a1972_44944040($_smarty_tpl) {?><div id="categories-container">
    <div class="row">
        <?php  $_smarty_tpl->tpl_vars['categorie'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['categorie']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['categorie']->iteration=0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['cats']['iteration']=0;
foreach ($_from as $_smarty_tpl->tpl_vars['categorie']->key => $_smarty_tpl->tpl_vars['categorie']->value) {
$_smarty_tpl->tpl_vars['categorie']->_loop = true;
 $_smarty_tpl->tpl_vars['categorie']->iteration++;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['cats']['iteration']++;
?>
            <?php $_smarty_tpl->tpl_vars['categoryLink'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getcategoryLink($_smarty_tpl->tpl_vars['categorie']->value['id'],$_smarty_tpl->tpl_vars['categorie']->value['link_cat']), null, 0);?>
            <div class="categorie">
                <?php if (!(1 & $_smarty_tpl->getVariable('smarty')->value['foreach']['cats']['iteration'])) {?>
                    <?php if ($_smarty_tpl->tpl_vars['categorie']->iteration>1) {?>
                        <div class="categorie-top-separator">
                        </div>
                    <?php }?>
                    <div class="text-categorie container col-md-6 col-xs-12">
                        <div class="title-content">
                            <p><span><?php echo htmlspecialchars(mb_strtoupper($_smarty_tpl->tpl_vars['categorie']->value['titre'], 'UTF-8'), ENT_QUOTES, 'UTF-8');?>
</span> - <span> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['marque']->value, ENT_QUOTES, 'UTF-8');?>
</span></p>
                        </div>
                        <div class="desc-content">
                            <p><?php echo htmlspecialchars(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['categorie']->value['description']), ENT_QUOTES, 'UTF-8');?>
</p>
                        </div>
                        <?php if ($_smarty_tpl->tpl_vars['categorie']->iteration==1) {?>
                            <div class="promo-content">
                                <h2>OFFRE SPECIALE</h2>
                                <p>Les 500 premiers numérotés !</p>
                                <p>50 euro au lieu de 60 !</p>
                            </div>
                        <?php }?>
                    </div>
                    <div class="pic-categorie container col-md-6 col-xs-12">
                        <div class="img-container container-fluid">
                            <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryLink']->value, ENT_QUOTES, 'UTF-8');?>
">
                                <div class="img-content">
                                    <img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cat_img_dir']->value, ENT_QUOTES, 'UTF-8');?>
/img/c/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categorie']->value['id'], ENT_QUOTES, 'UTF-8');?>
.jpg" alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categorie']->value['titre'], ENT_QUOTES, 'UTF-8');?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categorie']->value['titre'], ENT_QUOTES, 'UTF-8');?>
" class="img-responsive"/>
                                </div>
                                <div class="goto">
                                    <p>Decouvrez le</p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="categorie-bottom-separator container-fluid">
                    </div>
                <?php } else { ?>
                    <?php if ($_smarty_tpl->tpl_vars['categorie']->iteration>1) {?>
                        <div class="categorie-top-separator">
                        </div>
                    <?php }?>
                    <div class="pic-categorie container col-md-6 col-xs-12">
                        <div class="img-container container-fluid">
                            <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryLink']->value, ENT_QUOTES, 'UTF-8');?>
">
                                <div class="img-content">
                                    <img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cat_img_dir']->value, ENT_QUOTES, 'UTF-8');?>
/img/c/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categorie']->value['id'], ENT_QUOTES, 'UTF-8');?>
.jpg" alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categorie']->value['titre'], ENT_QUOTES, 'UTF-8');?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categorie']->value['titre'], ENT_QUOTES, 'UTF-8');?>
" class="img-responsive"/>
                                </div>
                                <div class="goto">
                                    <p>Decouvrez le</p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="text-categorie container col-md-6 col-xs-12">
                        <div class="title-content">
                            <p><span><?php echo htmlspecialchars(mb_strtoupper($_smarty_tpl->tpl_vars['categorie']->value['titre'], 'UTF-8'), ENT_QUOTES, 'UTF-8');?>
</span> - <span> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['marque']->value, ENT_QUOTES, 'UTF-8');?>
</span></p>
                        </div>
                        <div class="desc-content">
                            <p><?php echo htmlspecialchars(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['categorie']->value['description']), ENT_QUOTES, 'UTF-8');?>
</p>
                        </div>
                        <?php if ($_smarty_tpl->tpl_vars['categorie']->iteration==1) {?>
                            <div class="promo-content">
                                <h2>OFFRE SPECIALE</h2>
                                <p>Les 500 premiers numérotés !</p>
                                <p>50 euro au lieu de 60 !</p>
                            </div>
                        <?php }?>
                    </div>
                    <div class="categorie-bottom-separator container-fluid">
                    </div>
                <?php }?>
            </div>
        <?php } ?>
    </div>
</div><?php }} ?>
