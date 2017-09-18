//{block name="backend/rees46/store/settings"}
Ext.define('Shopware.apps.Rees46.store.Settings', {
    extend: 'Ext.data.Store',
    autoLoad: true,
    remoteSort: true,
    remoteFilter: true,
    batch: true,
    model: 'Shopware.apps.Rees46.model.Settings'
});
//{/block}
