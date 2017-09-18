//{block name="backend/rees46/store/blocks"}
Ext.define('Shopware.apps.Rees46.store.Blocks', {
    extend: 'Ext.data.Store',
    autoLoad: true,
    remoteSort: true,
    remoteFilter: true,
    batch: true,
    model: 'Shopware.apps.Rees46.model.Blocks'
});
//{/block}
