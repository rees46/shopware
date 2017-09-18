//{block name="backend/rees46/store/categories"}
Ext.define('Shopware.apps.Rees46.store.Categories', {
    extend: 'Ext.data.Store',
    autoLoad: true,
    remoteSort: true,
    remoteFilter: true,
    batch: true,
    model: 'Shopware.apps.Rees46.model.Categories',
});
//{/block}
