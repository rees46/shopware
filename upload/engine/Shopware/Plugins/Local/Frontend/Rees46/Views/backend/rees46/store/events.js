//{block name="backend/rees46/store/events"}
Ext.define('Shopware.apps.Rees46.store.Events', {
    extend: 'Ext.data.Store',
    autoLoad: true,
    remoteSort: true,
    remoteFilter: true,
    batch: true,
    model: 'Shopware.apps.Rees46.model.Events'
});
//{/block}
