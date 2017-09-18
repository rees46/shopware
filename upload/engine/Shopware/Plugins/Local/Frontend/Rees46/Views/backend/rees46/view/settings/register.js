//{namespace name="backend/rees46/main"}

//{block name="backend/rees46/view/settings/register"}
Ext.define('Shopware.apps.Rees46.view.settings.Register', {
    extend: 'Ext.form.FieldSet',
    alias: 'widget.register',
    stateId: 'register',
    collapsible: true,
    collapsed: false,
    hidden: false,
    width: '100%',
    margin: 5,
    padding: 10,
    border: true,
    defaults: {
        labelWidth: 160,
        anchor: '100%'
    },
    initComponent: function () {
        var me = this;

        me.addEvents(
            'register-send',
        );

        me.title = '{s name="register_form"}Registration Form{/s}';
        me.items = me.createForm();

        me.callParent(arguments);
    },
    createForm: function () {
        var me = this,
            data = me.record[0].data;

        return [
            Ext.create('Ext.form.field.Display', {
                fieldStyle: 'font-style: italic; color: #999999;',
                value: '{s name="register_help"}To register, please fill out the form below. Authorization, in this case, is performed automatically.{/s}'
            }),
            Ext.create('Ext.form.field.Text', {
                name: 'auth_email',
                fieldLabel: '{s name="email"}Email{/s}',
                allowBlank: false,
                blankText: '{s name="required_field"}This field is required{/s}',
                required: true,
                value: data.auth_email
            }),
            Ext.create('Ext.form.field.Text', {
                name: 'auth_phone',
                fieldLabel: '{s name="phone"}Phone Number{/s}',
                allowBlank: false,
                blankText: '{s name="required_field"}This field is required{/s}',
                required: true,
            }),
            Ext.create('Ext.form.field.Text', {
                name: 'auth_firstname',
                fieldLabel: '{s name="firstname"}First Name{/s}',
                allowBlank: false,
                blankText: '{s name="required_field"}This field is required{/s}',
                required: true,
            }),
            Ext.create('Ext.form.field.Text', {
                name: 'auth_lastname',
                fieldLabel: '{s name="lastname"}Last Name{/s}',
                allowBlank: false,
                blankText: '{s name="required_field"}This field is required{/s}',
                required: true,
            }),
            Ext.create('Ext.form.field.ComboBox', {
                name: 'auth_country',
                fieldLabel: '{s name="country"}Country{/s}',
                blankText: '{s name="required_field"}This field is required{/s}',
                allowBlank: false,
                required: true,
                typeAhead: false,
                transform: 'stateSelect',
                editable: false,
                store: Ext.create('Shopware.store.Country').load(),
                triggerAction: 'all',
                queryMode: 'local',
                displayField: 'name',
                valueField: 'iso',
                value: data.auth_country,
            }),
            Ext.create('Ext.form.field.ComboBox', {
                name: 'auth_currency',
                fieldLabel: '{s name="currency"}Currency{/s}',
                blankText: '{s name="required_field"}This field is required{/s}',
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
                value: data.auth_currency,
            }),
            Ext.create('Ext.form.field.ComboBox', {
                name: 'auth_category',
                fieldLabel: '{s name="product_category"}Product Category{/s}',
                blankText: '{s name="required_field"}This field is required{/s}',
                allowBlank: false,
                required: true,
                typeAhead: false,
                transform: 'stateSelect',
                editable: false,
                store: Ext.create('Shopware.apps.Rees46.store.Categories').load(),
                triggerAction: 'all',
                queryMode: 'local',
                displayField: 'name',
                valueField: 'id',
                value: data.auth_category,
            }),
            Ext.create('Ext.button.Button', {
                cls: 'primary small',
                text: '{s name="register"}Register{/s}',
                handler: function () {
                    me.fireEvent('register-send', me);
                }
            })
        ];
    }
});
//{/block}
