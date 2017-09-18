//{block name="backend/rees46/model/blocks"}
Ext.define('Shopware.apps.Rees46.model.Blocks', {
    extend: 'Ext.data.Model',
    autoLoad: true,
    fields: [
        { name: 'id', type: 'int' },
        { name: 'page', type: 'string' },
        { name: 'type', type: 'string' },
        { name: 'title', type: 'string' },
        { name: 'limit', type: 'int' },
        { name: 'template', type: 'string' },
        { name: 'position', type: 'int' },
        { name: 'status', type: 'boolean' },
    ],
    proxy: {
        type: 'ajax',
        api: {
            read: '{url controller="Rees46" action="getBlocks"}'
        },
        reader: {
            type: 'json',
            root: 'data',
        }
    }
});
//{/block}
