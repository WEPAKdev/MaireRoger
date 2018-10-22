<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class HTMLTemplateCommandFournisseurPdfCore extends HTMLTemplate
{
	public $custom_model;
 
	public function __construct($custom_object){
            $this->custom_model = $custom_object;
            $this->smarty = Context::getContext()->smarty;

            // header informations
            $id_lang = Context::getContext()->language->id;
            $this->title = $this->getTitle();
            // footer informations
            $this->shop = new Shop(Context::getContext()->shop->id);
            //get shop address
            $shop_address_obj = $this->shop->getAddress();
            if (isset($shop_address_obj) && $shop_address_obj instanceof Address) {
                //$this->shop_address = AddressFormat::generateAddress($shop_address_obj, array(), ' - ', ' ');
                $this->shop_address = OrderInvoice::getCurrentFormattedShopAddress((int)$this->shop->id);
            }
            
	}
        
        /**
        * recupère les infos du produit lié a 
        * Returns le name du prodiot associé
        * @return string
        */
       public function addingSmartyValues(){
           
       }       

       /**
        * @override
	* Returns the template's HTML header
	* @return string HTML content
	*/
	public function getHeader(){
            $this->assignCommonHeaderData();
            $this->smarty->assign(array(
                    'shop_address' => $this->shop_address
            ));
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'stockfournisseur/pdf/CommandFournisseur_header.tpl');
	}
 
	/**
         * @override
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent(){
            $this->smarty->assign(array(
                    'style' => $this->smarty->fetch(_PS_MODULE_DIR_ . 'stockfournisseur/pdf/CommandFournisseur_style.tpl'),
                    'custom_model' => $this->custom_model
            ));

            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'stockfournisseur/pdf/CommandFournisseur_body.tpl');
	}
   
	/**
         * @override
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getFooter(){
            $id_shop = (int)$this->shop->id;

            $this->smarty->assign(array(
                'available_in_your_account' => $this->available_in_your_account,
                'shop_address' => $this->shop_address,
                'shop_fax' => Configuration::get('PS_SHOP_FAX', null, null, $id_shop),
                'shop_phone' => Configuration::get('PS_SHOP_PHONE', null, null, $id_shop),
                'shop_email' => Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop),
                'free_text' => Configuration::get('PS_INVOICE_FREE_TEXT', (int)Context::getContext()->language->id, null, $id_shop)
            ));
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'stockfournisseur/pdf/CommandFournisseur_footer.tpl');
	}
        
        private function getTitle(){
            $date = date("m/d/y"); 
            return 'Commande du : ' . $date;
        }
 
	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename(){
            $date = date("m.d.y_H\hm"); 
            return 'commande_fournisseur_du_'. $date .'.pdf';
	}
 
	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename(){
            return $this->getFilename();
	}
}
