<?php
/**
 * Module d'ajout des categories de premier niveau sur la home page.
 * User: PAK
 * Date: 13/11/2017
 * Time: 00:54
 */
if (!defined('_PS_VERSION_'))
{
    exit;
}

class Stockfournisseur extends Module
{

    public function __construct()
    {
        $this->name = 'stockfournisseur';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Krstic Pierre-Alexis';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Stockfournisseur');
        $this->description = $this->l('Affiche les produits en rupture de stock dans un tableau');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('MYMODULE_NAME'))
            $this->warning = $this->l('No name provided');
    }

    public function install()
    {
        if (!parent::install())
            return false;
        $this->registerHook('displayStockfournisseur');
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall())
            return false;
        return true;
    }

    public function hookDisplayStockfournisseur($params)
    {
        //objects
        $classCategorie = new CategoryCore();
        $classpProduct  = new ProductCore();
        
        //create array of uniq matrixs to display
        $categories = $classCategorie->getCategories();
        //var_dump($categories);
        //exit;
        foreach($categoriesNiveau1 as $categorieNiveau1){
            $baseArray["'". $categorie ."'"] = array();
        }
        foreach($baseArray as $baseElemCat)
        {
            /*$level2cat =*/ 
        }
        //get categories (id/title/desc/link)
        $req =  "SELECT c.id_category as id, cl.name titre, cl.description, cl.link_rewrite as link_cat "
                ."FROM "._DB_PREFIX_."category c JOIN "._DB_PREFIX_."category_lang cl ON c.id_category = cl.id_category "
                ."WHERE active = 1 AND id_parent = (SELECT id_category FROM ps_category WHERE is_root_category = 1) "
                ."ORDER BY position ASC";

        $result = Db::getInstance()->ExecuteS($req);

        //regroupement des resultats

        $this->context->smarty->assign(
            array(
                'my_module_name' => Configuration::get('MYMODULE_NAME'),
                'my_module_link' => $this->context->link->getModuleLink('mymodule', 'display'),
                'cat_img_dir'    => __PS_BASE_URI__,
                'server_dir'     => __PS_BASE_URI__,
                'categories'     => $result,
                'marque'         => 'MAIRE ROGER'
            )
        );

        return $this->display(__FILE__, 'stockfournisseur.tpl');
    }
}