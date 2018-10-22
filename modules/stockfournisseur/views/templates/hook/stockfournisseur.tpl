<div id="toorder-container" class="panel">
    <div class="panel-heading">
        Recapitulatif des produits en rupture de stock
    </div>
    <div class="body-stockfournisseur">
        {foreach from=$toOrderArray item=productName key=name name=toOrderProductName}
        <div class="toOrder-elem">
            <table class="table">
                <thead>
                    <h4><bold>{$name}</bold></h4>
                    <p>à commander <span style="color:red;">{$productName.infosSup.qty}</span></p>
                     <tr>
                        <th>Taille</th>
                        <th colspan="35" align="center">Couleur : quantité</th>
                     </tr>
                </thead>
                <tbody>                
                    {foreach from=$productName item=attrSize key=size name=toOrderAttrSize}
                        {if $size != 'infosSup'}
                            {if $smarty.foreach.toOrderAttrSize.iteration is even}
                            <tr class="odd">
                                <td>{$size}</td>
                                {foreach from=$attrSize item=attrColor key=color name=toOrderAttrColor}
                                    {foreach from=$attrColor item=qty key=idattr name=toOrderIdAttr}
                                        <td>{$color} :</td>
                                        <td align="center" width="45" data-idattr="{$idattr}">{$qty}</td>
                                    {/foreach}
                                {/foreach}
                            </tr>
                            {else}
                            <tr>
                                <td>{$size}</td>
                                {foreach from=$attrSize item=attrColor key=color name=toOrderAttrColor}
                                    {foreach from=$attrColor item=qty key=idattr name=toOrderIdAttr}
                                        <td>{$color} :</td>
                                        <td align="center" width="45" data-idattr="{$idattr}">{$qty}</td>
                                    {/foreach}
                                {/foreach}
                            </tr>    
                            {/if}
                        {/if}
                    {/foreach}
                </tbody>
            </table>       
        </div>    
        {/foreach}
    </div>
    <div class="footer-stockfournisseur">
        {if $totToOrder > 0 }
            <p id="totToOrder" class="label" style="color:red">Total de produit à commander : {$totToOrder}</p>
            <span id="bt-pdf" class="btn-group-action">
                <span class="btn-group">
                    <a id="createcommand" class="btn btn-default" target="_blanck" href="../modules/stockfournisseur/ajax/actions.php?action=downloadPdf">
                        <i class="icon-file-text"></i>
                    </a>

                </span>
            </span>
        <button disabled="disabled" id="validCommand" class="btn btn-default" onclick="return confirm('ATTENTION cette opération est irréversible, assurez vous d\'avoir bien sauvegardé le pdf de commande avant de cliquer sur OK. \n Si vous n\'avez pas sauvegardé le pdf de commande cliquez sur CANCEL. \n Avez vous sauvegardé le pdf de commande fournisseur ?')" title="Afficher">
            <i class="fa-check"></i> Valider la commande
        </button>
        {else}
            <p id="totToOrder" class="label" style="color:green">Total de produit à commander : {$totToOrder}</p>
        {/if}
    </div>
</div>