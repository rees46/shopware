//{block name="backend/rees46/model/events"}
Ext.define('Shopware.apps.Rees46.model.Events', {
    extend: 'Ext.data.Model',
    autoLoad: true,
    fields: [
        { name: 'id', type: 'string' },
        { name: 'name', type: 'string' },
        { name: 'status', type: 'string' },
        { name: 'date', type: 'string' },
    ],
    proxy: {
        type: 'ajax',
        api: {
            read: '{url controller="Rees46" action="apiGetShopEvents"}'
        },
        reader: {
            type: 'json',
            root: 'data',
        }
    }
});
//{/block}
