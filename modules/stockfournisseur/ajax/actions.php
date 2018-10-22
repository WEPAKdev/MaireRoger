<?php
require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../init.php');
require_once(dirname(__FILE__).'/../stockfournisseur.php');

switch (Tools::getValue('action'))
{
    case 'downloadPdf':
        require_once _PS_MODULE_DIR_ . 'stockfournisseur/HTMLTemplateCommandFournisseurPdf.php';
        $templateName = 'CommandFournisseurPdfCore';
        $module = new Stockfournisseur();
        $module->assignObjAttributes();
        $module->assignSmartyAttributes();
        //assigner le module a la session, il sera récupéré pour l'acion validCommandFourn
        session_start();
        $_SESSION['stockfournisseur'] = $module;
        
        //for 1 product by page
        //$pdf = new PDF($module->arrResult, $templateName, Context::getContext()->smarty);
        
        //for all product in 1 page
        $pdf = new PDF(array($module->arrResult), $templateName, Context::getContext()->smarty);
        
        $pdf->render();
        break;
    case 'validCommandFourn':
        //retrn html new panel commande fourn : smarty->render
        require_once _PS_MODULE_DIR_ . 'stockfournisseur/HTMLTemplateCommandFournisseurPdf.php';
        session_start();
        $module = $_SESSION['stockfournisseur'];
        $result = $module->updateValues($module->arrResult);
        if($result){
            $params = array();
            $params['loadOnlyHtml'] = true;
            echo $module->hookDisplayStockfournisseur($params);
        }
        
}

