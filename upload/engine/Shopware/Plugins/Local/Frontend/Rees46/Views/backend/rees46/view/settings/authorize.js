//{namespace name="backend/rees46/main"}

//{block name="backend/rees46/view/settings/authorize"}
Ext.define('Shopware.apps.Rees46.view.settings.Authorize', {
    extend: 'Ext.form.FieldSet',
    alias: 'widget.authorize',
    stateId: 'authorize',
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

        me.title = '{s name="authorize_form"}Authorization Form{/s}';
        me.items = me.createForm();

        me.callParent(arguments);
    },
    createForm: function () {
        var me = this;

        return [
            Ext.create('Ext.form.field.Display', {
                fieldStyle: 'font-style: italic; color: #999999;',
                value: '{s name="authorize_help"}To authorize, please log in on rees46.com and copy & paste Store Key and Secret Key from Store Settings (Dashboard > Settings > Store Settings) to the fields below.{/s}'
            }),
            Ext.create('Ext.form.field.Text', {
                name: 'auth_store_key',
                fieldLabel: '{s name="store_key"}Store Key{/s}',
                allowBlank: false,
                blankText: '{s name="required_field"}This field is required{/s}',
                required: true,
            }),
            Ext.create('Ext.form.field.Text', {
                name: 'auth_secret_key',
                fieldLabel: '{s name="secret_key"}Secret Key{/s}',
                allowBlank: false,
                blankText: '{s name="required_field"}This field is required{/s}',
                required: true,
            }),
            Ext.create('Ext.button.Button', {
                cls: 'primary small',
                text: '{s name="authorize"}Authorize{/s}',
                handler: function () {
                    me.fireEvent('authorize-send', me);
                }
            })
        ];
    }
});
//{/block}
