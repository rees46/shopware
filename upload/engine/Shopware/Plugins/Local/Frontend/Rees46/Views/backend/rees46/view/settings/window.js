//{namespace name="backend/rees46/main"}

//{block name="backend/rees46/view/settings/window"}
Ext.define('Shopware.apps.Rees46.view.settings.Window', {
    extend: 'Ext.container.Container',
    alias: 'widget.settings',
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    autoScroll: true,
    initComponent: function () {
        var me = this;

        me.title = '{s name="title"}REES46{/s}';
        me.items = me.getItems();

        me.callParent(arguments);
    },
    getItems: function () {
        var me = this,
            data = me.record[0].data;

        if (data.REES46_ACTION_AUTH) {
            return [
                {
                    xtype: 'help',
                    record: me.record
                },
                {
                    xtype: 'general',
                    record: me.record
                },
                {
                    xtype: 'blocks',
                    record: me.record
                },
            ];
        } else {
            return [
                {
                    xtype: 'authorize',
                    record: me.record
                },
                {
                    xtype: 'register',
                    record: me.record
                },
                {
                    xtype: 'help',
                    record: me.record
                },
            ];
        }
    }
});
//{/block}
