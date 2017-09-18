//{namespace name="backend/rees46/main"}

//{block name="backend/rees46/view/settings/general"}
Ext.define('Shopware.apps.Rees46.view.settings.General', {
    extend: 'Ext.form.FieldSet',
    alias: 'widget.general',
    stateId: 'general',
    collapsible: true,
    collapsed: false,
    hidden: false,
    width: '100%',
    margin: 5,
    padding: 10,
    border: true,
    defaults: {
        labelWidth: 150,
        anchor: '100%'
    },
    initComponent: function () {
        var me = this;

        me.title = '{s name="general_form"}General{/s}';
        me.items = me.createForm();

        me.callParent(arguments);
    },
    createForm: function () {
        var me = this,
            data = me.record[0].data,
            store = Ext.create('Shopware.apps.Base.store.OrderStatus').load();

        return [
            Ext.create('Ext.form.field.Text', {
                name: 'REES46_SETTING_STORE_KEY',
                fieldLabel: '{s name="store_key"}Store Key{/s}',
                blankText: '{s name="required_field"}This field is required{/s}',
                labelWidth: me.defaults.labelWidth,
                allowBlank: false,
                required: true,
                value: data.REES46_SETTING_STORE_KEY,
            }),
            Ext.create('Ext.form.field.Text', {
                name: 'REES46_SETTING_SECRET_KEY',
                fieldLabel: '{s name="secret_key"}Secret Key{/s}',
                blankText: '{s name="required_field"}This field is required{/s}',
                labelWidth: me.defaults.labelWidth,
                allowBlank: false,
                required: true,
                value: data.REES46_SETTING_SECRET_KEY,
            }),
            Ext.create('Ext.form.field.ComboBox', {
                name: 'REES46_SETTING_ORDER_CREATED',
                fieldLabel: '{s name="order_created"}Created Order Status{/s}',
                blankText: '{s name="required_field"}This field is required{/s}',
                labelWidth: me.defaults.labelWidth,
                allowBlank: false,
                required: true,
                typeAhead: false,
                transform: 'stateSelect',
                editable: false,
                multiSelect: true,
                store: store,
                triggerAction: 'all',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                value: data.REES46_SETTING_ORDER_CREATED !== '' ? data.REES46_SETTING_ORDER_CREATED.split(',').map(x=>+x) : '',
            }),
            Ext.create('Ext.form.field.ComboBox', {
                name: 'REES46_SETTING_ORDER_COMPLETED',
                fieldLabel: '{s name="order_completed"}Completed Order Status{/s}',
                blankText: '{s name="required_field"}This field is required{/s}',
                labelWidth: me.defaults.labelWidth,
                allowBlank: false,
                required: true,
                typeAhead: false,
                transform: 'stateSelect',
                editable: false,
                multiSelect: true,
                store: store,
                triggerAction: 'all',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                value: data.REES46_SETTING_ORDER_COMPLETED !== '' ? data.REES46_SETTING_ORDER_COMPLETED.split(',').map(x=>+x) : '',
            }),
            Ext.create('Ext.form.field.ComboBox', {
                name: 'REES46_SETTING_ORDER_CANCELLED',
                fieldLabel: '{s name="order_cancelled"}Cancelled Order Status{/s}',
                blankText: '{s name="required_field"}This field is required{/s}',
                labelWidth: me.defaults.labelWidth,
                allowBlank: false,
                required: true,
                typeAhead: false,
                transform: 'stateSelect',
                editable: false,
                multiSelect: true,
                store: store,
                triggerAction: 'all',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                value: data.REES46_SETTING_ORDER_CANCELLED !== '' ? data.REES46_SETTING_ORDER_CANCELLED.split(',').map(x=>+x) : '',
            }),
            Ext.create('Ext.form.field.ComboBox', {
                name: 'REES46_SETTING_PRODUCT_CURRENCY',
                fieldLabel: '{s name="product_currency"}Product Currency{/s}',
                blankText: '{s name="required_field"}This field is required{/s}',
                labelWidth: me.defaults.labelWidth,
                allowBlank: false,
                required: true,
                typeAhead: false,
                transform: 'stateSelect',
                editable: false,
                store: Ext.create('Shopware.store.Currency').load(),
                triggerAction: 'all',
                queryMode: 'local',
                displayField: 'name',
                valueField: 'currency',
                value: data.REES46_SETTING_PRODUCT_CURRENCY,
            }),
            Ext.create('Ext.form.field.Checkbox', {
                name: 'REES46_SETTING_PRODUCT_TAX',
                fieldLabel: '{s name="product_tax"}Product Tax Included{/s}',
                blankText: '{s name="required_field"}This field is required{/s}',
                labelWidth: me.defaults.labelWidth,
                allowBlank: false,
                required: true,
                uncheckedValue: false,
                inputValue: true,
                checked: data.REES46_SETTING_PRODUCT_TAX ? true : false,
            }),
            Ext.create('Ext.button.Button', {
                cls: 'primary small',
                text: '{s name="save"}Save settings{/s}',
                handler: function () {
                    me.fireEvent('general-send', me);
                }
            })
        ];
    }
});
//{/block}
