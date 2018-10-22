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

    public $arrResult = array();
    public $totQty = 0;
    
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

    public function install(){
        if (!parent::install())
            return false;
        $this->registerHook('displayStockfournisseur');
        return true;
    }

    public function uninstall(){
        if (!parent::uninstall())
            return false;
        return true;
    }
    
    /* optionnel */
    public function sendEmailToFournWithPdf($pdf){
        
    }
    
    /*
     * Reset qty to 0 of each qty of product attribute commanded
     * @arrValues (array) : $this->arrResult format
     * @return (bool)
     */
    public function updateValues($arrValues){
        /*
         *  $arrResult[$productName]['infosSup']['productName'] = $productName;
            $arrResult[$productName]['infosSup']['qty'] = $qtyCurrentProduct;
         *  $arrResult[$productName][$size][$color][$productAttr] = $qty;
         */
        //maj qty de l'attribute id de la table ps_stock_available
        foreach($arrValues as $product){
            foreach($product as $key => $size){
                if($key != 'infosSup'){
                    foreach($size as $color){
                        foreach($color as $idAttr => $qty){
                            $req =  "UPDATE ps_stock_available "
                                    ."SET quantity = quantity + ". intval($qty)." "
                                    ."WHERE id_product_attribute = ". $idAttr;
                            $result = Db::getInstance()->execute($req);
                            if(!$result){return false;}
                        }
                    }
                }
            }
        }
        return true;
    }
    
    /*
     * Cette fonction cree les atributs de classes totQty et arrResult
     */
    public function assignObjAttributes(){
        $this->totQty = 0;
        $this->arrResult = array();
        /*METHODE 2*/
        //recuperere les produits en rupture de stock avec group By id_produits
            //recuperer les attributs de ses produits associé le nb de produit a commander
            //incrementer la valeur globale de produits a commander
            //recuperer la catégorie de ses produits pour l'affichage
            //creer le tableau : [produit name][nom decli][qté to command]

        //get les infos produits en rupture de stock
        $req =  "SELECT ps_stock_available.id_product, ps_stock_available.quantity as qty, id_product_attribute as attr "
                ."FROM ps_stock_available JOIN ps_product ON ps_stock_available.id_product = ps_product.id_product "
                ."WHERE ps_stock_available.quantity < 0 AND id_product_attribute != 0 "
                .'ORDER BY id_product ASC';
        $result = Db::getInstance()->ExecuteS($req);
        $totQty = 0;
        $arrResult = array();
        if(!empty($result))
        {
            $currentid_product = $result[0]['id_product'];
            $qtyCurrentProduct = 0;
            foreach($result as $entry){
                //vars
                $idproduct      = $entry['id_product'];
                $productAttr    = $entry['attr'];
                $qty            = abs(intval($entry['qty']));
                $decliString    = '';
                $tailleArr      = ['XS','S','M','L','XL'];

                //get quantity to command by product id
                if($currentid_product != $idproduct){
                    $currentid_product = $idproduct;
                    $qtyCurrentProduct = $qty;
                }
                else{
                    $qtyCurrentProduct += $qty;
                }

                //get product name
                $req =  "SELECT name FROM ps_product_lang WHERE id_product = ".$idproduct;
                $name = Db::getInstance()->ExecuteS($req);
                $productName = $name[0]['name'];

                //increment total qty
                $totQty += $qty;

                //get attribute combinaisons name
                $req =  "SELECT name "
                        ."FROM ps_product_attribute_combination JOIN ps_attribute_lang ON ps_product_attribute_combination.id_attribute = ps_attribute_lang.id_attribute "
                        ."WHERE ps_product_attribute_combination.id_product_attribute = ".$productAttr;
                $result = Db::getInstance()->ExecuteS($req);

                //create array keys $size & $color values 
                foreach($result as $key => $nameAttr){
                     if (in_array($nameAttr['name'], $tailleArr))
                    {
                        $size = $nameAttr['name'];
                    }
                    else{
                        $color = $nameAttr['name'];
                    }               
                }

                //create result array to display
                $arrResult[$productName]['infosSup']['productName'] = $productName;
                $arrResult[$productName]['infosSup']['qty'] = $qtyCurrentProduct;
                $arrResult[$productName][$size][$color][$productAttr] = $qty;
                $this->totQty = $totQty;
                $this->arrResult = $arrResult;            
            }
        }
    }
    
    /*
     * Cette fonction assign les attributs de la classe a Smarty
     */
    public function assignSmartyAttributes(){
        $this->context->smarty->assign(
            array(
                'my_module_name' => Configuration::get('MYMODULE_NAME'),
                'my_module_link' => $this->context->link->getModuleLink('mymodule', 'display'),
                'cat_img_dir'    => __PS_BASE_URI__,
                'server_dir'     => __PS_BASE_URI__,
                'toOrderArray'   => $this->arrResult,
                'totToOrder'     => $this->totQty
            )
        );
    }

    public function hookDisplayStockfournisseur($params){
        $this->assignObjAttributes();
        $this->assignSmartyAttributes();
        if(empty($params['loadOnlyHtml'])){
            $this->context->controller->addJS(($this->_path).'js/stockfourn.js');
            $this->context->controller->addCSS(($this->_path).'css/stockfourn.css'); 
        }
        return $this->display(__FILE__, 'stockfournisseur.tpl');
    }
}