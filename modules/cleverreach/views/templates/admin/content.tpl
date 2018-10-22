{**
* 2017 CleverReach
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    CleverReach <partner@cleverreach.com>
* @copyright 2017 CleverReach GmbH
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<div class="panel clever-reach">
    <input type="hidden" id="admin_url" value="{$cleverreach_adminurl|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="import_url" value="{$cleverreach_importurl|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="base_url" value="{$cleverreach_baseUrl|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="authorize_url" value="{$cleverreach_authorize_url|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="batch_size_message" value="{l s='Batch size must be between 50 and 250' mod='cleverreach'}">
    <input type="hidden" id="empty_fields_message" value="{l s='All fields must be filled' mod='cleverreach'}">
    <input type="hidden" id="none_label" value="{l s='None' mod='cleverreach'}">
    <input type="hidden" id="next_label" value="{l s='Next' mod='cleverreach'}">
    <input type="hidden" id="reset_label" value="{l s='Are you sure you want to reset configuration?' mod='cleverreach'}">
    <input type="hidden" id="start_import_label" value="{l s='Start import' mod='cleverreach'}">
    <input type="hidden" id="customers_imported_message" value="{l s='Customers imported successfully' mod='cleverreach'}">

    <img class="cr-logo" src="{$cleverreach_logoUrl|escape:'htmlall':'UTF-8'}"/>

    <div class="cr-loader-big">
        <span class="cr-loader"></span>
    </div>

    <div class="cr-content form-horizontal" style="display: none;">
        <ul class="nav nav-tabs">
            <li class="list-links" id="configuration_tab">
                <a href="#configuration">
                    <div class="tab-links" data-tab="configurations">
                        <i class="icon-gear"></i>
                        {l s='Configuration' mod='cleverreach'}
                        {if $cleverreach_isConnected === false}
                            <span class="badge">1</span>
                        {/if}
                    </div>
                </a>
            </li>
            <li class="list-links" id="mappings_tab">
                <a href="#mappings" class="{if $cleverreach_isConnected === false}disabled{/if}">
                    <div class="tab-links" data-tab="mappings">
                        <i class="icon-group"></i>
                        {l s='Customer Groups' mod='cleverreach'}
                        {if $cleverreach_isConnected === false}
                            <span class="badge">2</span>
                        {/if}
                    </div>
                </a>
            </li>
            <li class="list-links" id="import_tab">
                <a href="#import" class="{if $cleverreach_isConnected === false}disabled{/if}">
                    <div class="tab-links" data-tab="import">
                        <i class="icon-cloud-upload"></i>
                        {l s='Import' mod='cleverreach'}
                        {if $cleverreach_isConnected === false}
                            <span class="badge">3</span>
                        {/if}
                    </div>
                </a>
            </li>
        </ul>

        <div class="tab-content panel">
            <div class="alert alert-danger" id="error-message-block">
                <p><strong>{l s='Error' mod='cleverreach'}</strong></p>
                <p class="message-item-text" id="error-message"></p>
            </div>
            <div class="alert alert-success" id="success-message-block">
                <p><strong>{l s='Success' mod='cleverreach'}</strong></p>
                <p class="message-item-text" id="success-message"></p>
            </div>

            <div class="tab-pane active" id="configurations">
                <div class="panel">
                    <div class="panel-heading">
                        {l s='Connect' mod='cleverreach'}
                    </div>
                    <div class="form-wrapper">
                        <a href="{$cleverreach_userManualUrl|escape:'htmlall':'UTF-8'}" target="_blank"
                            class="btn btn-default pull-right" id="user-manual">{l s='User Manual' mod='cleverreach'}</a>
                        <p>{l s='In order to connect with CleverReach, please click on Connect button. For more information please follow instructions in' mod='cleverreach'}
                            <a href="{$cleverreach_userManualUrl|escape:'htmlall':'UTF-8'}" target="_blank">{l s='user manual' mod='cleverreach'}</a>.
                        </p>

                        <button type="button" class="btn btn-primary" id="cr-connect">
                            <span id="cr-connect-loader" class="cr-loader"></span> {l s='Connect' mod='cleverreach'}
                        </button>

                        <span class="cr-status">{l s='Status' mod='cleverreach'}:</span>
                        <span class="cr-connected">{l s='Connected' mod='cleverreach'}</span>
                        <span class="cr-connecting">
                            {l s='Connecting' mod='cleverreach'}
                            <span id="import-loader"
                                  class="cr-loader pull-right" style="margin-left:10px;display: block;"></span>
                        </span>
                        <span class="cr-disconnected">{l s='Disconnected' mod='cleverreach'}</span>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        {l s='Global' mod='cleverreach'}
                    </div>
                    <div class="form-wrapper">
                        <div class="form-group">
                            <label class="control-label col-md-2">
                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title
                                      data-original-title="{l s='If product search is enabled, shop products can be searched from CleverReach.' mod='cleverreach'}">
                                    {l s='Product search' mod='cleverreach'}
                                </span>
                            </label>
                            <div class="col-md-3">
                                <select id="search" size="1" title="">
                                    <option value="1">{l s='Enabled' mod='cleverreach'}</option>
                                    <option value="0">{l s='Disabled' mod='cleverreach'}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">
                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title
                                      data-original-title="{l s='If debug mode is enabled, system logs user actions.' mod='cleverreach'}">
                                    {l s='Debug mode' mod='cleverreach'}
                                </span>
                            </label>
                            <div class="col-md-3">
                                <select id="cr-debug-mode-options" size="1" title="">
                                    <option value="1">{l s='Enabled' mod='cleverreach'}</option>
                                    <option value="0">{l s='Disabled' mod='cleverreach'}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="mappings">
                <p>{l s='Map your shop customer groups to CleverReach subscriber lists and choose a opt-in form' mod='cleverreach'}.</p>
                {if !empty($cleverreach_mapping)}
                <table class="table">
                    <thead>
                    <tr>
                        <th class="col-md-4"><span class="title_box">{l s='PrestaShop Customer Group' mod='cleverreach'}</span></th>
                        <th class="col-md-4"><span class="title_box">{l s='CleverReach Group' mod='cleverreach'}</span></th>
                        <th class="col-md-4">
                            <label class="control-label">
                                <span class="label-tooltip title_box" data-toggle="tooltip" data-html="true" title=""
                                      data-original-title="{l s='If you select opt-in form, default system opt-in option will be disabled.' mod='cleverreach'}">
                                    {l s='Opt-in Form' mod='cleverreach'}
                                </span>
                            </label>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$cleverreach_mapping key=k item=mapping}
                    <tr>
                        <td>
                            <input type="hidden" name="group_ids" value="{$k|escape:'htmlall':'UTF-8'}">
                            <label name="mappings">{$mapping|escape:'htmlall':'UTF-8'}</label>
                        </td>
                        <td>
                            <select name="groups" id="cr_{$k|escape:'htmlall':'UTF-8'}"></select>
                        </td>
                        <td>
                            <select name="forms" id="form_{$k|escape:'htmlall':'UTF-8'}"> </select>
                        </td>
                    </tr>
                    {/foreach}
                    </tbody>
                </table>
                {else}
                <h4>{l s='Currently there are no mappings available' mod='cleverreach'}</h4>
                {/if}
            </div>

            <div class="tab-pane" id="import">
                <div class="form-wrapper">
                    <div class="form-group">
                        <label class="control-label col-md-1">
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                  data-original-title="{l s='Number of customers imported to CleverReach per one request. Must be between 50 and 250. If you don\'t know what is this leave default value.' mod='cleverreach'}">
                                {l s='Batch size' mod='cleverreach'}
                            </span>
                        </label>
                        <div class="col-md-2">
                            <input type="text" id="cr-import-batch-size" name="batch" value="100"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-1">{l s='Import progress' mod='cleverreach'}</label>
                        <div class="col-md-6">
                            <div id="cr-progress" class="cr-graph">
                                <div id="cr-bar"><p id="cr-bar-text">0%</p></div>
                            </div>
                        </div>
                    </div>
                    {if $cleverreach_isConnected !== false}
                        <button type="button" class="btn btn-default col-md-offset-1" id="cr-start-import">
                            <i class="icon-cloud-upload"></i>
                            <span id="import-loader" class="cr-loader"></span>
                            {l s='Start import' mod='cleverreach'}
                        </button>
                    {/if}
                </div>
            </div>

            <div class="panel-footer">
                {if $cleverreach_isConnected === false}
                <button type="button" class="btn btn-default pull-left" id="cr-go-to-previous-page">
                    <i class="process-icon-back"></i>
                    {l s='Previous' mod='cleverreach'}
                </button>
                <button type="button" class="btn btn-default pull-right" id="cr-go-to-next-page">
                </button>
                {else}
                <button class="btn btn-default pull-left" id="cr-reset-configuration">
                    <i class="process-icon-reset"></i>
                    {l s='Reset to default' mod='cleverreach'}
                </button>
                <button type="button" class="btn btn-default pull-right" id="cr-save-configuration">
                    <i class="process-icon-save"></i>
                    <span id="cr-save-loader" class="cr-loader"></span>
                    {l s='Save' mod='cleverreach'}
                </button>
                {/if}
            </div>
        </div>
    </div>
</div>