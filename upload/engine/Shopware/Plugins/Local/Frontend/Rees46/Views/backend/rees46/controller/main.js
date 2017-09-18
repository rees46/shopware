//{namespace name=backend/rees46/view/main}

//{block name="backend/rees46/controller/main"}
Ext.define('Shopware.apps.Rees46.controller.Main', {
    extend: 'Ext.app.Controller',
    mainWindow: null,
    init: function () {
        var me = this;

        me.control({
            'help': {
                'help-send': me.apiGotoDashboard,
            },
            'authorize': {
                'authorize-send': me.apiSetXml,
            },
            'register': {
                'register-send': me.apiCreateUser,
            },
            'general': {
                'general-send': me.saveConfig,
            },
            'blocks button[action=add]': {
                click: me.addBlock,
            },
            'element-property': {
                'blocks-save': me.saveBlocks,
                delete: function (view, record) {
                    var me = this,
                        store = view.getStore();

                    store.remove(record);
                },
            },
        });

        var store = me.subApplication.getStore('Settings');

        store.load({
            callback: function (data) {
                me.mainWindow = me.getView('main.Window').create({
                    record: data
                }).show();
            }
        });

        me.callParent(arguments);
    },
    apiCreateUser: function (win) {
        var me = this;

        win.up('window').setLoading(true);

        Ext.Ajax.request({
            url: '{url controller="rees46" action="apiCreateUser"}',
            method: 'POST',
            params: {
                auth_email: win.down('textfield[name=auth_email]').getValue(),
                auth_phone: win.down('textfield[name=auth_phone]').getValue(),
                auth_firstname: win.down('textfield[name=auth_firstname]').getValue(),
                auth_lastname: win.down('textfield[name=auth_lastname]').getValue(),
                auth_country: win.down('combobox[name=auth_country]').getValue(),
                auth_currency: win.down('combobox[name=auth_currency]').getValue(),
                auth_category: win.down('combobox[name=auth_category]').getValue(),
            },
            success: function (response) {
                var response = Ext.JSON.decode(response.responseText);

                me.log(response.data);

                win.up('window').setLoading(false);

                if (response.success) {
                    me.apiCreateShop(win);
                }
            },
        });
    },
    apiCreateShop: function (win) {
        var me = this;

        win.up('window').setLoading(true);

        Ext.Ajax.request({
            url: '{url controller="rees46" action="apiCreateShop"}',
            method: 'POST',
            success: function (response) {
                var response = Ext.JSON.decode(response.responseText);

                me.log(response.data);

                win.up('window').setLoading(false);

                if (response.success) {
                    me.apiSetXml(win);
                }
            },
        });
    },
    apiSetXml: function (win) {
        var me = this,
            store_key = win.down('textfield[name=auth_store_key]').getValue(),
            secret_key = win.down('textfield[name=auth_secret_key]').getValue();

        win.up('window').setLoading(true);

        Ext.Ajax.request({
            url: '{url controller="rees46" action="apiSetXml"}',
            method: 'POST',
            params: {
                store_key: store_key,
                secret_key: secret_key,
            },
            success: function (response) {
                var response = Ext.JSON.decode(response.responseText);

                me.log(response.data);

                win.up('window').setLoading(false);

                if (response.success) {
                    me.apiSetOrders(0, win);
                }
            },
        });
    },
    apiSetOrders: function (offset, win) {
        var me = this;

        win.up('window').setLoading(true);

        Ext.Ajax.request({
            url: '{url controller="rees46" action="apiSetOrders"}',
            method: 'POST',
            params: {
                offset: offset,
            },
            success: function (response) {
                var response = Ext.JSON.decode(response.responseText);

                me.log(response.data, response.total, response.count);

                win.up('window').setLoading(false);

                if (response.offset) {
                    me.apiSetOrders(response.offset, win);
                } else {
                    me.apiSetCustomers(0, win);
                }
            },
        });
    },
    apiSetCustomers: function (offset, win) {
        var me = this;

        win.up('window').setLoading(true);

        Ext.Ajax.request({
            url: '{url controller="rees46" action="apiSetCustomers"}',
            method: 'POST',
            params: {
                offset: offset,
            },
            success: function (response) {
                var response = Ext.JSON.decode(response.responseText);

                me.log(response.data, response.total, response.count);

                win.up('window').setLoading(false);

                if (response.offset) {
                    me.apiSetCustomers(response.offset, win);
                } else {
                    me.apiGetWebPushFiles(win);
                }
            },
        });
    },
    apiGetWebPushFiles: function (win) {
        var me = this;

        win.up('window').setLoading(true);

        Ext.Ajax.request({
            url: '{url controller="rees46" action="apiGetWebPushFiles"}',
            method: 'POST',
            success: function (response) {
                var response = Ext.JSON.decode(response.responseText);

                me.log(response.data);

                win.up('window').setLoading(false);

                if (response.success) {
                   me.apiFinishConfigure(win);
                }
            },
        });
    },
    apiFinishConfigure: function (win) {
        var me = this,
            store = me.subApplication.getStore('Settings');

        win.up('window').setLoading(true);

        Ext.Ajax.request({
            url: '{url controller="rees46" action="apiFinishConfigure"}',
            method: 'POST',
            success: function (response) {
                var response = Ext.JSON.decode(response.responseText);

                if (response.link) {
                    nw = window.open(response.link, '_blank');
                    nw.focus();
                } else {
                    var form = Ext.create('Ext.form.Panel', {
                        id: 'rees46FinishConfigure',
                        method: 'post',
                        standardSubmit: 'true',
                        url: response.url,
                        items: [
                            {
                                xtype: 'hiddenfield',
                                name: 'api_key',
                                value: response.api_key,
                            },
                            {
                                xtype: 'hiddenfield',
                                name: 'api_secret',
                                value: response.api_secret,
                            },
                        ],
                    });

                    win.add(form);

                    form.submit({
                        target: '_blank',
                    });
                }

                win.up('window').destroy();

                store.load({
                    callback: function (data) {
                        me.mainWindow = me.getView('main.Window').create({
                            record: data
                        }).show();
                    }
                });
            },
        });
    },
    apiGotoDashboard: function (win) {
        var me = this;

        Ext.Ajax.request({
            url: '{url controller="rees46" action="apiGotoDashboard"}',
            method: 'POST',
            success: function (response) {
                var response = Ext.JSON.decode(response.responseText);

                if (response.link) {
                    nw = window.open(response.link, '_blank');
                    nw.focus();
                } else {
                    var form = Ext.create('Ext.form.Panel', {
                        id: 'rees46GotoDashboard',
                        method: 'post',
                        standardSubmit: 'true',
                        url: response.url,
                        items: [
                            {
                                xtype: 'hiddenfield',
                                name: 'api_key',
                                value: response.api_key,
                            },
                            {
                                xtype: 'hiddenfield',
                                name: 'api_secret',
                                value: response.api_secret,
                            },
                        ],
                    });

                    win.add(form);

                    form.submit({
                        target: '_blank',
                    });
                }
            },
        });
    },
    saveConfig: function (win) {
        var me = this,
            body = win.up('window'),
            panel = body.down('form'),
            form = panel.getForm(),
            fields = form.getValues(),
            params = Ext.JSON.encode(fields);

        win.up('window').setLoading(true);

        Ext.Ajax.request({
            url: '{url controller="rees46" action="saveConfig"}',
            method: 'POST',
            params: {
                params
            },
            success: function (response) {
                var response = Ext.JSON.decode(response.responseText);

                me.log(response.data);

                win.up('window').setLoading(false);
            },
        });
    },
    addBlock: function (button) {
        var me = this,
            table = button.up('grid'),
            fields = table.query('[isFormField]'),
            data = { }, fieldData;

        if (!table) {
            return;
        }

        Ext.each(fields, function (field) {
            fieldData = field.getModelData();
            data = Ext.apply(data, fieldData);
        });

        var store = table.getStore(),
            record = store.add(data)[0],
            plugin = table.getPlugin('cellediting');

        plugin.startEdit(record, table.columns[0]);
    },
    saveBlocks: function (win) {
        var me = this,
            body = win.up('window'),
            grid = body.down('grid[name=blocks]'),
            store = grid.getStore(),
            records = store.data.items,
            arr = [],
            params,
            error;

        grid.setLoading(true);

        Ext.each(records, function (record) {
            recordData = record.getData();

            for (key in recordData) {
                if (recordData[key] === '' || recordData[key] === undefined) {
                    error = null;
                }
            }

            arr.push(recordData);
        });

        if (error === null) {
            me.log('error_blocks_save');

            grid.setLoading(false);

            return;
        }

        params = Ext.JSON.encode(arr);

        Ext.Ajax.request({
            url: '{url controller="rees46" action="saveBlocks"}',
            method: 'POST',
            params: {
                params
            },
            success: function (response) {
                var response = Ext.JSON.decode(response.responseText);

                me.log(response.data);

                grid.setLoading(false);

                store.load();
            },
        });
    },
    log: function (text, total, count) {
        var me = this;

        Shopware.Notification.createGrowlMessage(
            'REES46',
            me.getMessage(text, total, count),
            'REES46',
        );
    },
    getMessage: function (text, total, count) {
        var translations = [];
            // apiCreateUser
            translations['error_auth_email'] = '{s name="error_auth_email"}Incorrect value for Email field.{/s}';
            translations['error_auth_phone'] = '{s name="error_auth_phone"}Incorrect value for Phone Number field.{/s}';
            translations['error_auth_firstname'] = '{s name="error_auth_firstname"}Incorrect value for First Name field.{/s}';
            translations['error_auth_lastname'] = '{s name="error_auth_lastname"}Incorrect value for Last Name field.{/s}';
            translations['error_auth_country'] = '{s name="error_auth_country"}Incorrect value for Country field.{/s}';
            translations['error_auth_currency'] = '{s name="error_auth_currency"}Incorrect value for Currency field.{/s}';
            translations['error_auth_category'] = '{s name="error_auth_category"}Incorrect value for Product Category field.{/s}';
            translations['error_user_create'] = '{s name="error_user_create"}Could not register an account. Please, check the form was filled out correctly.{/s}';
            translations['error_user_duplicate'] = '{s name="error_user_duplicate"}Account already exists. Please, authorize.{/s}';
            translations['success_user_create'] = '{s name="success_user_create"}Account successfully registered.{/s}';
            // apiCreateShop
            translations['error_api_key'] = '{s name="error_api_key"}Incorrect value for API Key field.{/s}';
            translations['error_api_secret'] = '{s name="error_api_secret"}Incorrect value for API Secret field.{/s}';
            translations['error_api_category'] = '{s name="error_api_category"}Incorrect value for Product Category field.{/s}';
            translations['error_shop_create'] = '{s name="error_shop_create"}Could not create a store.{/s}';
            translations['error_shop_duplicate'] = '{s name="error_shop_duplicate"}Account already exists. Please, authorize.{/s}';
            translations['success_shop_create'] = '{s name="success_shop_create"}Store successfully created.{/s}';
            // apiSetXml
            translations['error_store_key'] = '{s name="error_store_key"}Incorrect value for Store Key field.{/s}';
            translations['error_secret_key'] = '{s name="error_secret_key"}Incorrect value for Secret Key field.{/s}';
            translations['error_xml_export'] = '{s name="error_xml_export"}Could not export product feed.{/s}';
            translations['success_xml_export'] = '{s name="success_xml_export"}Product feed successfully exported to REES46.{/s}';
            // apiSetOrders
            translations['error_orders_export'] = '{s name="error_orders_export"}Could not export orders.{/s}';
            translations['process_orders_export'] = Ext.String.format('{s name="process_orders_export"}[1] out of [0] orders successfully exported to REES46.{/s}', total, count);
            translations['success_orders_export'] = Ext.String.format('{s name="success_orders_export"}[0] orders successfully exported to REES46.{/s}', total);
            translations['empty_orders_export'] = '{s name="empty_orders_export"}No available orders for export.{/s}';
            // apiSetCustomers
            translations['error_customers_export'] = '{s name="error_customers_export"}Could not export customers.{/s}';
            translations['process_customers_export'] = Ext.String.format('{s name="process_customers_export"}[1] out of [0] customers successfully exported to REES46.{/s}', total, count);
            translations['success_customers_export'] = Ext.String.format('{s name="success_customers_export"}[0] customers successfully exported to REES46.{/s}', total);
            translations['empty_customers_export'] = '{s name="empty_customers_export"}No available customers for export.{/s}';
            // apiGetWebPushFiles
            translations['success_files_load'] = '{s name="success_files_load"}Files successfully loaded.{/s}';
            translations['error_files_load'] = '{s name="error_files_load"}Could not load files.{/s}';
            // saveConfig
            translations['success_config_save'] = '{s name="success_config_save"}Configuration saved.{/s}';
            // saveBlocks
            translations['success_blocks_save'] = '{s name="success_blocks_save"}Blocks saved.{/s}';
            translations['error_blocks_save'] = '{s name="error_blocks_save"}All fields of the block is required.{/s}';

        return translations[text];
    },
});
//{/block}
