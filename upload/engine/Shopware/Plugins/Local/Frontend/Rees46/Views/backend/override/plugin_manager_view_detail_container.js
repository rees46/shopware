//{block name="backend/plugin_manager/view/detail/container"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.Override.PluginManagerViewDetailContainer', {
    override: 'Shopware.apps.PluginManager.view.detail.Container',

    updateConfiguration: function(plugin) {
        var me = this,
            result = me.callParent(arguments);

        if (plugin.get('name') === 'Rees46') {
            me.informationTab.navigationClick(me.tabIndex.localDescription);
            me.informationTab.hideTab(me.tabIndex.configuration);
            me.configurationContainer.hide();
        }

        return result;
    },
});
//{/block}
