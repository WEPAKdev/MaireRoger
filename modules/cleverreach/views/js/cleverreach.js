/**
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
 */

(function () {
    var isConnected = false;
    var initialAdjustment = true;

    /**
     * Configurations and constants
     *
     * @type {{get}}
     */
    var CONFIG = (function () {
        var constants = {
            SAVE_CONFIGURATION_URL: '&ajax=true&action=saveConfig',
            GET_CONFIGURATION_URL: '&ajax=true&action=getConfig',
            VALIDATION_URL: '&ajax=true&action=checkConfig',
            IMPORT_START_URL: '&ajax=true&action=importStart',
            CHECK_PROGRESS_URL: '&ajax=true&action=importProgress',
            RESET_CONFIGURATION_URL: '&ajax=true&action=resetConfig',
            IMPORT_LOCKED: 0,
            NOTHING_TO_IMPORT: 1,
            IMPORT_STARTED: 2,
            INCORRECT_BATCH: 3,
            CONFIGURATION_SET: 4,
            CONFIGURATION_RESET: 5,
            SUCCESSFUL_CONNECTION: 6,
            UNSUCCESSFUL_CONNECTION: 7
        };
        return {
            get: function (name) {
                return constants[name];
            }
        };
    })();

    var handlers = {
        load: function (selector, time) {
            if (document.querySelector(selector)) {
                ajax.get(handlers.getAdminUrl() + CONFIG.get('GET_CONFIGURATION_URL'), null,
                    handlers.pageLoadResponseHandler, 'json');
                tabs.initTabs();
                handlers.initHandlers();
                return true;
            }

            setTimeout(function () {
                handlers.load(selector, time);
            }, time);
        },
        /**
         * Initializes handlers for various buttons and attaches event listeners for clicks to them
         */
        initHandlers: function () {
            var connect = document.getElementById('cr-connect');
            var reset = document.getElementById('cr-reset-configuration');
            var save = document.getElementById('cr-save-configuration');
            var start = document.getElementById('cr-start-import');
            var saveLoader = document.getElementById('cr-save-loader');

            if (connect) {
                connect.addEventListener('click', function () {
                    handlers.checkConnectionStatus();
                });
            }

            if (save) {
                save.addEventListener('click', function () {
                    if (handlers.store()) {
                        saveLoader.style.display = 'block';
                    }
                });
            }

            if (reset) {
                reset.addEventListener('click', function () {
                    var resetLabel = document.getElementById('reset_label').value;
                    if (confirm(resetLabel)) {
                        ajax.get(handlers.getAdminUrl() + CONFIG.get('RESET_CONFIGURATION_URL'), null,
                            handlers.configurationResetHandler, 'json');
                    }
                });
            }

            if (start) {
                start.addEventListener('click', function () {
                    handlers.startImport(this);
                });
            }
        },

        /**
         * A function for storing all values on all the tabs in the database
         */
        store: function () {
            var i;
            var batch = document.getElementById('cr-import-batch-size');
            var mappings = document.getElementsByName('group_ids');
            var groupMappings = {};
            var params = [];

            if (batch.value === '' || isNaN(batch.value) || batch.value < 50 || batch.value > 250) {
                handlers.showMessage(document.getElementById('batch_size_message').value);

                return false;
            }

            params.debugMode = document.getElementById('cr-debug-mode-options').value;
            params.productSearch = document.getElementById('search').value;

            for (i = 0; i < mappings.length; i++) {
                groupMappings[mappings[i].value] = {
                    systemGroup: mappings[i].value,
                    crGroup: document.getElementById('cr_' + mappings[i].value).value,
                    optInForm: document.getElementById('form_' + mappings[i].value).value
                };
            }

            params.groupMappings = JSON.stringify(groupMappings);
            params.batchSize = batch.value;

            ajax.post(handlers.getAdminUrl() + CONFIG.get('SAVE_CONFIGURATION_URL'), params,
                handlers.configurationResponseHandler, 'json', true);

            return true;
        },

        /**
         * Handles response on page load
         *
         * @param response
         */
        pageLoadResponseHandler: function (response) {
            // hide initial loader ans show content
            document.querySelector('.cr-loader-big').style.display = 'none';
            document.querySelector('.cr-content').style.display = 'block';
            var next = document.getElementById('cr-go-to-next-page');

            if (!response.connected) {
                document.querySelector('.cr-disconnected').style.display = 'inline-block';
                next.disabled = true;
                return;
            }

            document.getElementById('cr-connect').style.display = 'none';
            document.querySelector('.cr-connected').style.display = 'inline-block';

            var configurations = response.configurations;
            var productSearch = document.getElementById('search');
            var debugMode = document.getElementById('cr-debug-mode-options');
            var batchSize = document.getElementById('cr-import-batch-size');
            var mapping;
            var selectCrGroup;
            var selectOptInForm;

            handlers.populateGroups(configurations.groups, document.getElementsByName('groups'));

            for (var key in configurations.mappings) {
                if (configurations.mappings.hasOwnProperty(key)) {
                    mapping = configurations.mappings[key];
                    selectCrGroup = document.getElementById('cr_' + mapping.systemGroup);
                    selectOptInForm = document.getElementById('form_' + mapping.systemGroup);

                    selectCrGroup.addEventListener('change', function () {
                        handlers.populateFormSelect(this, configurations.groups);
                    });

                    selectCrGroup.value = mapping.crGroup;
                    selectCrGroup.dispatchEvent(new Event('change'));
                    selectOptInForm.value = mapping.optInForm;
                }
            }

            initialAdjustment = false;
            if (response.running) {
                progressBar.move(false, response.width);
            }

            productSearch.value = configurations.productSearch ? 1 : 0;
            debugMode.value = configurations.debugMode ? 1 : 0;
            batchSize.value = configurations.batchSize ? configurations.batchSize : 100;
        },

        /**
         * Handles response from server regarding setting configuration and importing customers
         *
         * @param response
         */
        configurationResponseHandler: function (response) {
            var next = document.getElementById('cr-go-to-next-page');
            var errorMsgBlock = document.getElementById('error-message-block');
            var saveLoader = document.getElementById('cr-save-loader');

            if (response.status === CONFIG.get('INCORRECT_BATCH')) {
                handlers.showMessage(response.message, false);
            } else {
                errorMsgBlock.style.display = 'none';

                if (initialAdjustment) {
                    handlers.startImport(document.getElementById('cr-go-to-next-page'));
                } else {
                    handlers.showMessage(response.message, true);
                    saveLoader.style.display = 'none';
                }
            }
        },

        /**
         * Handles response from server regarding resetting configuration
         *
         * @param response
         */
        configurationResetHandler: function (response) {
            if (response.status) {
                location.reload();
            }
        },

        /**
         * handles import response
         *
         * @param response
         */
        importResponseHandler: function (response) {
            var start = document.getElementById('cr-start-import');
            var loader = initialAdjustment ?
                document.getElementById('wizard-import-loader') : document.getElementById('import-loader');
            var next = document.getElementById('cr-go-to-next-page');

            loader.style.display = 'none';

            if (response.status === CONFIG.get('IMPORT_LOCKED')) {
                handlers.showMessage(response.message, false);
            } else if (response.status === CONFIG.get('NOTHING_TO_IMPORT')) {
                handlers.showMessage(response.message, true);
            } else {
                handlers.showMessage(response.message, true);
                progressBar.move(true, 0);
            }

            start ? start.disabled = false : next.disabled = false;
        },

        /**
         * Handles response from server regarding connection
         *
         * @param response
         */
        connectionResponseHandler: function (response) {
            document.getElementById('cr-connect-loader').style.display = 'none';

            handlers.showMessage(response.message, response.status === CONFIG.get('SUCCESSFUL_CONNECTION'));
        },

        /**
         * Shows message, success or error, depend on second param
         *
         * @param message
         * @param success
         */
        showMessage: function (message, success) {
            var selector = success ? 'success-message' : 'error-message';

            document.getElementById(selector).innerHTML = message;
            document.getElementById('error-message-block').style.display = success ? 'none' : 'block';
            document.getElementById('success-message-block').style.display = success ? 'block' : 'none';
        },
        /**
         * Hides all messages
         */
        hideMessage: function () {
            document.getElementById('error-message-block').style.display = 'none';
            document.getElementById('success-message-block').style.display = 'none';
        },
        /**
         * Gets first child
         *
         * @param el
         * @returns {*}
         */
        getFirstChild: function (el) {
            var firstChild = el.firstChild;
            while (firstChild && firstChild.nodeType === 3) { // skip TextNodes
                firstChild = firstChild.nextSibling;
            }

            return firstChild;
        },

        /**
         * Starts import of customers to CleverReach
         */
        startImport: function (button) {
            var importLoader = handlers.getFirstChild(button);

            importLoader.style.display = 'inline-block';
            button.disabled = true;

            ajax.post(handlers.getImportUrl() + CONFIG.get('IMPORT_START_URL'), null,
                handlers.importResponseHandler, 'json', true);
        },

        /**
         * Checks connection status
         */
        checkConnectionStatus: function () {
            var connected = document.querySelector('.cr-connected');
            var disconnected = document.querySelector('.cr-disconnected');
            var connecting = document.querySelector('.cr-connecting');
            var authUrl = document.getElementById('authorize_url');
            var authWin = window.open(authUrl.value, 'authWindow',
                'toolbar=0,location=0,menubar=0,width=600');

            var winClosed = setInterval(function () {
                if (authWin.closed) {
                    connected.style.display = 'none';
                    disconnected.style.display = 'none';
                    connecting.style.display = 'inline-block';
                    document.getElementById('cr-connect').disabled = true;
                    clearInterval(winClosed);
                    status();
                }
            }, 250);

            function status() {
                ajax.post(handlers.getAdminUrl() + CONFIG.get('VALIDATION_URL'), null, handlers.auth,
                    'json', true);
            }
        },

        /**
         * Callback function for AJAX request that does authentication
         *
         * @param response
         * @param status
         */
        auth: function (response, status) {
            var next = document.getElementById('cr-go-to-next-page');
            var connected = document.querySelector('.cr-connected');
            var disconnected = document.querySelector('.cr-disconnected');
            var connecting = document.querySelector('.cr-connecting');
            var connect = document.getElementById('cr-connect');

            connecting.style.display = 'none';

            if (response.status === CONFIG.get('SUCCESSFUL_CONNECTION')) {
                handlers.connectionResponseHandler(response, status);
                connected.style.display = 'inline-block';
                disconnected.style.display = 'none';
                connect.style.display = 'none';
                handlers.populateGroups(response.groups, document.getElementsByName('groups'));

                isConnected = true;

                if (next) {
                    next.classList.remove('disabled');
                    next.disabled = false;
                    next.onclick = function () {
                        handlers.navigate(1);
                    };
                }
            } else {
                disconnected.style.display = 'inline-block';
                connect.disabled = false;
            }
        },

        /**
         * Populates mapping table with CleverReach groups
         *
         * @param groups
         * @param groupSelects
         */
        populateGroups: function (groups, groupSelects) {

            [].forEach.call(groupSelects, function (select) {
                // remove old children
                while (select.hasChildNodes()) {
                    select.removeChild(select.lastChild);
                }

                //adding "none" option
                select.appendChild(handlers.createOption({
                    id: 0,
                    name: document.getElementById('none_label').value
                }));

                groups.forEach(function (group) {
                    select.appendChild(handlers.createOption(group));
                });

                handlers.populateFormSelect(select, groups);

                select.addEventListener('change', function () {
                    handlers.populateFormSelect(select, groups);
                });
            });
        },

        /**
         * Populates mapping table with groups
         *
         * @param select
         * @param groups
         */
        populateFormSelect: function (select, groups) {
            var selectIdParts = select.id.split('_');
            var groupId = selectIdParts[selectIdParts.length - 1];
            var formSelect = document.getElementById('form_' + groupId);
            var selectedOptionValue = select.options[select.selectedIndex].value;
            var selectedGroup;
            var i;

            while (formSelect.lastChild) {
                formSelect.removeChild(formSelect.lastChild);
            }

            for (i = 0; i < groups.length; i++) {
                if (groups[i].id == selectedOptionValue) {
                    selectedGroup = groups[i];
                    break;
                }
            }

            handlers.appendOptionsToFormSelect(selectedGroup ? selectedGroup.forms : false, formSelect);
        },

        /**
         * Appends forms as options to the given form select element
         *
         * @param forms
         * @param select
         */
        appendOptionsToFormSelect: function (forms, select) {
            // adding "none" option
            select.appendChild(handlers.createOption({
                id: 0,
                name: document.getElementById('none_label').value
            }));

            if (forms) {
                forms.forEach(function (form) {
                    select.appendChild(handlers.createOption(form));
                });
            }
        },

        /**
         * Function for navigating through the tabs
         *
         * @param page
         */
        navigate: function (page) {
            handlers.hideMessage();
            tabs.openTab(page);
            handlers.setWizardButtons(page);
        },

        /**
         * Sets actions for previous and next navigation buttons in configuration wizard
         *
         * @param page
         */
        setWizardButtons: function (page) {
            var createSpan = function () {
                var span = document.createElement('span');

                span.className = 'cr-loader';
                span.id = 'wizard-import-loader';
                span.style.display = 'none';

                return span;
            };

            var next = document.getElementById('cr-go-to-next-page');
            var prev = document.getElementById('cr-go-to-previous-page');
            var tabLinks = document.querySelectorAll('.clever-reach .list-links a');

            if (next && prev) {
                next.style.display = 'block';

                switch (page) {
                    // Configurations
                    case 0:
                        prev.style.display = 'none';

                        if (document.getElementById('wizard-import-loader')) {
                            next.removeChild(createSpan());
                        }

                        isConnected ? next.classList.remove('disabled') : next.classList.add('disabled');

                        next.innerHTML = '<i class="process-icon-next"></i>' + document.getElementById('next_label').value;
                        next.onclick = function () {
                            handlers.navigate(1);
                        };

                        break;

                    // Mappings
                    case 1:
                        prev.style.display = 'block';
                        prev.classList.remove('disabled');
                        prev.onclick = function () {
                            handlers.navigate(0);
                        };

                        next.innerHTML = '<i class="process-icon-next"></i>' + document.getElementById('next_label').value;
                        next.classList.remove('disabled');
                        next.onclick = function () {
                            handlers.navigate(2);
                        };

                        if (document.getElementById('wizard-import-loader')) {
                            next.removeChild(createSpan());
                        }

                        break;

                    // Import
                    case 2:
                        prev.style.display = 'block';
                        prev.onclick = function () {
                            handlers.navigate(1);
                        };

                        next.innerHTML = '<i class="process-icon-cloud-upload icon-cloud-upload"></i>'
                            + document.getElementById('start_import_label').value;
                        next.insertBefore(createSpan(), next.firstChild);

                        next.onclick = function () {
                            if (handlers.store()) {
                                document.getElementById('wizard-import-loader').style.display = 'block';
                            }
                        };

                        break;

                }
            }

            tabLinks[page].classList.remove('disabled');
        },

        /**
         * Gets shop admin url
         *
         * @returns {*}
         */
        getAdminUrl: function () {
            return document.getElementById('admin_url').value;
        },

        /**
         * Gets shop import url
         *
         * @returns {*}
         */
        getImportUrl: function () {
            return document.getElementById('import_url').value;
        },

        /**
         * Creates drop down option
         *
         * @param form
         * @returns {Element}
         */
        createOption: function (form) {
            var option = document.createElement('option');

            option.value = form.id;
            option.innerHTML = form.name;

            return option;
        }
    };

    var tabs = {
        /**
         * Initializes tabs and attaches corresponding event listeners for clicks
         */
        initTabs: function () {
            var i,
                tabLinks = document.querySelectorAll('.clever-reach .nav-tabs li a');

            handlers.navigate(0);
            for (i = 0; i < tabLinks.length; i++) {
                tabs.handleElement(tabLinks[i], i);
            }
        },

        /**
         * Function for setting event handlers for a specific element. We need this function
         * because these events are assigned in a for loop and it's not possible to pass certain
         * parameters, such as iterator variable directly in the function for initializing tabs, so
         * we need this helper function
         *
         * @param element
         * @param i
         */
        handleElement: function (element, i) {
            element.addEventListener('click', function () {
                if (!this.classList.contains('disabled')) {
                    handlers.navigate(i);
                }
            });
        },

        /**
         * Function for opening a given tab, and closing all the others, so that only the selected
         * tab remains in focus
         *
         * @param page
         */
        openTab: function (page) {
            // Declare all variables
            var i, tabContainers, tabLinks, listElement;

            // Get all elements with class="tab-content" and hide them
            tabContainers = document.querySelectorAll('.clever-reach .tab-pane');
            for (i = 0; i < tabContainers.length; i++) {
                tabContainers[i].classList.remove('active');
            }

            // Get all elements with class="tab-links" and remove the class "active"
            tabLinks = document.querySelectorAll('.clever-reach .tab-links');
            for (i = 0; i < tabLinks.length; i++) {
                tabLinks[i].classList.remove('active');
            }

            // Get all elements with class="list-links" and remove the class "active"
            listElement = document.querySelectorAll('.clever-reach .list-links');
            for (i = 0; i < tabLinks.length; i++) {
                listElement[i].classList.remove('active');
            }

            // Show the current tab, and add an "active" class to the link that opened the tab
            tabContainers[page].classList.add('active');
            tabLinks[page].classList.add('active');
            listElement[page].classList.add('active');
        }
    };

    var progressBar = {
        /**
         * Moves progress bar slider from 0 to 100%
         */
        move: function (first, width) {

            var bar = document.getElementById('cr-bar');
            var text = document.getElementById('cr-bar-text');
            var startButton = document.getElementById('cr-start-import');
            var id = setInterval(frame, 3000);

            text.innerHTML = width + '%';
            bar.style.width = width + '%';

            function frame() {
                if (width >= 100) {
                    clearInterval(id);
                    handlers.showMessage(document.getElementById('customers_imported_message').value, true);

                    if (startButton) {
                        startButton.style.display = 'block';
                    }
                } else {
                    ajax.post(handlers.getImportUrl() + CONFIG.get('CHECK_PROGRESS_URL'), null,
                        function (response) {
                            width = response.status;
                            text.innerHTML = width + '%';
                            bar.style.width = width + '%';
                        }, 'json', true);
                }
            }
        }
    };

    var ajax = {
        x: function () {
            var versions = [
                'MSXML2.XmlHttp.6.0',
                'MSXML2.XmlHttp.5.0',
                'MSXML2.XmlHttp.4.0',
                'MSXML2.XmlHttp.3.0',
                'MSXML2.XmlHttp.2.0',
                'Microsoft.XmlHttp'
            ];
            var xhr;
            var i;

            if (typeof XMLHttpRequest !== 'undefined') {
                return new XMLHttpRequest();
            }

            for (i = 0; i < versions.length; i++) {
                try {
                    xhr = new ActiveXObject(versions[i]);
                    break;
                } catch (e) {
                }
            }

            return xhr;

        },

        send: function (url, callback, method, data, format, async) {
            var x = ajax.x();

            if (async === undefined) {
                async = true;
            }

            x.open(method, url, async);
            x.onreadystatechange = function () {
                if (x.readyState === 4) {
                    var response = x.responseText;
                    var status = x.status;

                    if (format === 'json') {
                        response = JSON.parse(response);
                    }

                    callback(response, status);
                }
            };

            if (method === 'POST') {
                x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            }

            x.send(data);

        },

        post: function (url, data, callback, format, async) {
            var query = [];
            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
                }
            }

            ajax.send(url, callback, 'POST', query.join('&'), format, async);
        },

        get: function (url, data, callback, format, async) {
            var query = [];
            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
                }
            }

            ajax.send(url + (query.length ? '?' + query.join('&') : ''), callback, 'GET',
                null, format, async);
        }
    };

    handlers.load('#base_url', 100);
})();
