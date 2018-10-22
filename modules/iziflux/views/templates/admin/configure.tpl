{*
 * 2016 Profileo
 *
 * @author Profileo contact@profileo.com
 * @copyright 2016 Profileo
 * @license Profileo
 * @link http://www.profileo.com
 *}

<div class="panel">
    <div class="container">
        <div class="description">
        	<h3><i class="icon icon-credit-card"></i> {l s='izi Flux' mod='iziflux'}</h3>
        	<p>
        		<strong>{l s='Iziflux vous permet de diffuser votre catalogue produits sur :' mod='iziflux'}</strong><br />
        		{l s='- Plus de 500 comparateurs (CPC et CPA) compatibles en Europe. (Google Shopping, Leguide, Twenga , Kelkoo, Shopping, shopzilla, etc…)' mod='iziflux'}<br />
        		{l s='- Les places de marché ( Ebay, Amazon, Pixmania, Priceminister, Rueducommerce, Brandalley, Cdiscount, Fnac, 2xmoinscher. D\'autres places de marché sont en cours d\'intégration permanente)' mod='iziflux'}<br />
        		{l s='- Les Plateformes d\'affiliation et de cashback' mod='iziflux'}<br />
        		{l s='- Les  Solutions de retargeting' mod='iziflux'}
        	</p>
        
        	<p>
        		{l s='Vos commandes marketplaces sont automatiquement importées dans votre back office Prestashop.' mod='iziflux'}
        	</p>
        	
        	<br />
        	
        	<p>
        		<strong>{l s='Vous disposez ainsi d’une interface de pilotage permettant de :' mod='iziflux'}</strong><br />
        		{l s='- Segmenter son catalogue pour ne publier qu\'une partie de ses offres sur un support (ex : produits en stock seulement ou offre saisonnière).' mod='iziflux'}<br />
        		{l s='- Désactiver les produits non rentables, automatiquement ou manuellement.' mod='iziflux'}<br />
        		{l s='- Editer ses fichiers produits massivement.' mod='iziflux'}<br />
        		{l s='- Catégoriser automatiquement ou manuellement ses produits au sein de l\'arborescence des différents partenaires.' mod='iziflux'}
        		{l s='- Enrichir le contenu de son catalogue.' mod='iziflux'}
        		{l s='- Rajouter massivement des informations manquantes comme les codes EAN par exemple.' mod='iziflux'}
        	</p>
        	<br />
        	<p>
        		{l s='Ex : Il peut être intéressant de modifier les titres et descriptifs produits en fonction des recommandations et des algorithmes de chaque support, notamment en insérant une phrase d\'accroche pour Noël ou les soldes d\'hiver.' mod='iziflux'}
        	</p>
        	
        	<br />
        	<br />
        	<p>
        		{l s='Iziflux vous rembourse intégralement le coût du module (remise à valoir sur votre abonnement mensuel à notre solution).' mod='iziflux'}
        	</p>
    	</div>
    	
    	
    	<div class="formulaire">
    		<p>
        		{l s='Pour en Bénéficier et activer votre licence iziflux merci de compléter le formulaire ci-dessous' mod='iziflux'}
        	</p>
        	
        	<br />
			<form method="post" action="">
				<label>{l s='Nom du site' mod='iziflux'} : </label><div><input type="text" name="site" value="{$shop_name|escape:'htmlall':'UTF-8'}"></div>
				<label>{l s='Nom' mod='iziflux'} : </label><div><input type="text" name="nom" value="{$empolyee->lastname|escape:'htmlall':'UTF-8'}"></div>
				<label>{l s='Prenom' mod='iziflux'} : </label><div><input type="text" name="prenom" value="{$empolyee->firstname|escape:'htmlall':'UTF-8'}"></div>
				<label>{l s='E-mail' mod='iziflux'} : </label><div><input type="text" name="email" value="{$shop_email|escape:'htmlall':'UTF-8'}"></div>
				<label>{l s='Téléphone' mod='iziflux'} : </label><div><input type="text" name="telephone" value="{$shop_phone|escape:'htmlall':'UTF-8'}"></div>
				<p style="text-align:center" ><input type="submit" value="{l s='Envoyer la demande' mod='iziflux'}" name="envoi_mail" class="button"/></p>
    		</form>
    	</div>
    </div>
</div>

