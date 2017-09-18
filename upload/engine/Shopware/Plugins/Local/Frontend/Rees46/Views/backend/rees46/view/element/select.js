Ext.define('Shopware.apps.Rees46.view.element.Select', {
    extend:'Shopware.apps.Base.view.element.Select',
    alias:[
        'widget.element-select',
        'widget.element-combo',
        'widget.element-combobox',
        'widget.element-comboremote'
    ],
    allowBlank: false,
    typeAhead: false,
    transform: 'stateSelect',
    editable: false,
    queryMode: 'remote',
    initComponent:function () {
        var me = this;

        me.callParent(arguments);
        me.queryCaching = (me.store.$className == 'Ext.data.ArrayStore');
    }
});
