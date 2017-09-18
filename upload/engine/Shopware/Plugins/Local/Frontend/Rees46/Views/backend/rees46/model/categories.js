//{block name="backend/rees46/model/categories"}
Ext.define('Shopware.apps.Rees46.model.Categories', {
    extend: 'Ext.data.Model',
    autoLoad: true,
    fields: [
        { name: 'id', type: 'int' },
        { name: 'name', type: 'string' },
    ],
    proxy: {
        type: 'ajax',
        api: {
            read: '{url controller="Rees46" action="apiGetShopCategories"}'
        },
        reader: {
            type: 'json',
            root: 'data',
        }
    }
});
//{/block}
