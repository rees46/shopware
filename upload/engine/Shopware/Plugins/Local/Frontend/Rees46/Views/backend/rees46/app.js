//{namespace name="backend/rees46/main"}

//{block name="backend/rees46/application"}
Ext.define('Shopware.apps.Rees46', {
    extend: 'Enlight.app.SubApplication',
    name: 'Shopware.apps.Rees46',
    bulkLoad: true,
    loadPath: '{url action=load}',
    controllers: [
        'Main',
    ],
    models: [
        'Settings',
        'Categories',
        'Events',
    ],
    stores: [
        'Settings',
        'Categories',
        'Events',
    ],
    views: [
        'main.Window',
        'settings.Window',
        'settings.Authorize',
        'settings.Register',
        'settings.Help',
        'settings.General',
        'settings.Blocks',
        'element.Property',
        'element.Boolean',
        'element.Select',
        'element.Text',
        'element.Number',
    ],
    launch: function () {
        var me = this;
        return me.getController('Main').mainWindow;
    }
});
//{/block}
