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
        'Blocks',
        'Categories',
        'Events',
        'Settings',
    ],
    stores: [
        'Blocks',
        'Categories',
        'Events',
        'Settings',
    ],
    views: [
        'element.Boolean',
        'element.Number',
        'element.Property',
        'element.Select',
        'element.Text',
        'main.Window',
        'settings.Window',
        'settings.Authorize',
        'settings.Register',
        'settings.Help',
        'settings.General',
        'settings.Blocks',
    ],
    launch: function () {
        var me = this;
        return me.getController('Main').mainWindow;
    },
});
//{/block}
