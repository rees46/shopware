//{namespace name="backend/rees46/main"}

//{block name="backend/rees46/view/main/window"}
Ext.define('Shopware.apps.Rees46.view.main.Window', {
    extend: 'Enlight.app.Window',
    alias: 'widget.main',
    layout: 'fit',
    width: 900,
    height: '100%',
    padding: 0,
    autoScroll: true,
    stateful: true,
    border: false,
    initComponent: function() {
        var me = this;

        me.addEvents(
            'authorize-send',
        );

        me.title = '{s name="title"}REES46{/s}';
        me.items = me.getItems();

        me.callParent(arguments);
    },
    getItems: function () {
        var me = this;

        return Ext.create('Ext.form.Panel', {
            collapsible: false,
            region: 'center',
            autoScroll: true,
            border: false,
            items: [
                {
                    xtype: 'settings',
                    record: me.record
                }
            ]
        });
    }
});
//{/block}
