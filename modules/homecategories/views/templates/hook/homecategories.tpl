<!--<div class="special-message">
    <p>Pour chaque commande 1euro sera reversé au Téléthon</p>
</div>-->
<div id="categories-container">
    <div class="row">
        {foreach from=$categories item=categorie name=cats}
            {assign var='categoryLink' value=$link->getcategoryLink($categorie.id, $categorie.link_cat)}
            <div class="categorie">
                {if $smarty.foreach.cats.iteration is even}
                    {if $categorie@iteration > 1 }
                        <div class="categorie-top-separator">
                        </div>
                    {/if}
                    <div class="text-categorie container col-md-6 col-xs-12">
                        <div class="title-content">
                            <p><span>{$categorie.titre|upper}</span> - <span> {$marque}</span></p>
                        </div>
                        <div class="desc-content">
                            <p>{$categorie.description|strip_tags}</p>
                        </div>
                        {if $categorie.id == 15 }
                            <div class="promo-content">
                                <h2>OFFRE SPECIALE</h2>
                                <p>Série limité brodée pour les 100 premiers !</p>
                            </div>
                        {/if}
                        {if $categorie.id == 18 }
                            <div class="promo-content">
                                <h2>OFFRE SPECIALE</h2>
                                <p>4 produits achetés le 3ème offert !</p>
                            </div>
                        {/if}
                    </div>
                    <div class="pic-categorie container col-md-6 col-xs-12">
                        <div class="img-container container-fluid">
                            <a href="{$categoryLink}">
                                <div class="img-content">
                                    <img src="{$cat_img_dir}img/c/{$categorie.id}.jpg" alt="{$categorie.titre}" title="{$categorie.titre}" class="img-responsive"/>
                                </div>
                                <div class="goto">
                                    <p>Decouvrez le</p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="categorie-bottom-separator container-fluid">
                    </div>
                {else}
                    {if $categorie@iteration > 1 }
                        <div class="categorie-top-separator">
                        </div>
                    {/if}
                    <div class="pic-categorie container col-md-6 col-xs-12">
                        <div class="img-container container-fluid">
                            <a href="{$categoryLink}">
                                <div class="img-content">
                                    <img src="{$cat_img_dir}img/c/{$categorie.id}.jpg" alt="{$categorie.titre}" title="{$categorie.titre}" class="img-responsive"/>
                                </div>
                                <div class="goto">
                                    <p>Decouvrez le</p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="text-categorie container col-md-6 col-xs-12">
                        <div class="title-content">
                            <p><span>{$categorie.titre|upper}</span> - <span> {$marque}</span></p>
                        </div>
                        <div class="desc-content">
                            <p>{$categorie.description|strip_tags}</p>
                        </div>
                        {if $categorie.id == 15 }
                            <div class="promo-content">
                                <h2>OFFRE SPECIALE</h2>
                                <p>Série limité brodée pour les 100 premiers !</p>
                            </div>
                        {/if}
                        {if $categorie.id == 18 }
                            <div class="promo-content">
                                <h2>OFFRE SPECIALE</h2>
                                <p>4 produits achetés le 3ème offert !</p>
                            </div>
                        {/if}
                    </div>
                    <div class="categorie-bottom-separator container-fluid">
                    </div>
                {/if}
            </div>
        {/foreach}
    </div>
</div>