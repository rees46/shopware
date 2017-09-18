//{block name="backend/rees46/model/settings"}
Ext.define('Shopware.apps.Rees46.model.Settings', {
    extend: 'Ext.data.Model',
    autoLoad: true,
    fields: [
        { name: 'auth_email', type: 'string' },
        { name: 'auth_country', type: 'string' },
        { name: 'auth_currency', type: 'string' },
        { name: 'auth_category', type: 'int', defaultValue: 0 },
        { name: 'REES46_ACTION_AUTH', type: 'boolean' },
        { name: 'REES46_SETTING_STORE_KEY', type: 'string' },
        { name: 'REES46_SETTING_SECRET_KEY', type: 'string' },
        { name: 'REES46_SETTING_ORDER_CREATED', type: 'string' },
        { name: 'REES46_SETTING_ORDER_COMPLETED', type: 'string' },
        { name: 'REES46_SETTING_ORDER_CANCELLED', type: 'string' },
        { name: 'REES46_SETTING_PRODUCT_CURRENCY', type: 'string' },
        { name: 'REES46_SETTING_PRODUCT_TAX', type: 'boolean' },
    ],
    proxy: {
        type: 'ajax',
        api: {
            read: '{url controller="Rees46" action="getFields"}'
        },
        reader: {
            type: 'json',
            root: 'data',
        }
    }

});
//{/block}
