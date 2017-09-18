Ext.define('Shopware.apps.Rees46.view.element.Boolean', {
    extend: 'Ext.form.field.Checkbox',
    alias: [
        'widget.element-boolean',
        'widget.element-checkbox'
    ],
    inputValue: 1,
    uncheckedValue: 0,

    initComponent: function () {
        var me = this;

        if(me.value) {
            me.setValue(!!me.value);
        }

        // Move support text to box label
        if(me.supportText) {
            me.boxLabel = me.supportText;
            delete me.supportText;
        } else if(me.helpText) {
            me.boxLabel = me.helpText;
            delete me.helpText;
        }

        me.callParent(arguments);
    }
});
