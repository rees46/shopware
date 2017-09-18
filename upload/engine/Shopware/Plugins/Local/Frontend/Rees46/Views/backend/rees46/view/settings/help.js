//{namespace name="backend/rees46/main"}

//{block name="backend/rees46/view/settings/help"}
Ext.define('Shopware.apps.Rees46.view.settings.Help', {
    extend: 'Ext.form.FieldSet',
    alias: 'widget.help',
    stateId: 'help',
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

        me.title = '{s name="help_form"}Help{/s}';
        me.items = me.createForm();

        me.callParent(arguments);

        me.on('afterrender', function () {
            var button = me.getEl().down('#rees46GotoDashboard');

            button.on('click', function () {
                me.fireEvent('help-send', me);
            });
        });
    },
    createForm: function () {
        var me = this;

        return [
			Ext.create('Ext.container.Container', {
                html: '{s name="help"}Go to your REES46 store dashboard to get the access to:'
                + '<br><ul>'
                + '<li>Triggered emails</li>'
                + '<li>Email marketing tool</li>'
                + '<li>Personalized search</li>'
                + '<li>Web push triggered notifications</li>'
                + '<li>Instant web push notifications</li>'
                + '<li>Audience segmentation</li>'
                + '<li>Abandoned cart remarketing tool</li>'
                + '</ul>'
                + '<button class="plugin-manager-action-button primary" id="rees46GotoDashboard">REES46 dashboard</button><br><br>'
                + 'Documentation: <a href="https://docs.rees46.com/display/en/Shopware+Plugin" target="_blank">https://docs.rees46.com/display/en/Shopware+Plugin</a><br><br>'
                + 'Support: <a href="mailto:support@rees46.com?subject=Support for REES46 Shopware plugin">support@rees46.com</a>{/s}',
                styleHtmlContent: true,
            }),
            me.getEventsContainer(),
        ];
    },
    getEventsContainer: function () {
        var me = this,
            data = me.record[0].data,
            store = Ext.create('Shopware.apps.Rees46.store.Events').load();

	    if (!data.REES46_ACTION_AUTH) {
	        return;
	    }

        return  Ext.create('Ext.grid.Panel', {
            title: 'Event Tracking',
            store: store,
            margin: '20 0 0 0',
            sortableColumns: false,
            border: false,
            columns: [
                {
                    header: '{s name="event_name"}Name{/s}',
                    dataIndex: 'name',
                    flex: 1,
                    renderer: me.columnNameRenderer,
                }, {
                    header: '{s name="event_status"}Status{/s}',
                    dataIndex: 'status',
                    flex: 1,
                    renderer: me.columnStatusRenderer,
                }, {
                    header: '{s name="event_date"}Last Action{/s}',
                    dataIndex: 'date',
                    flex: 1,
                },
            ],
        });
    },
    columnNameRenderer: function (value, meta, record) {
        id = record.getId();

        if (id === 'view') {
            icon = 'sprite-eye--plus';
        } else if (id === 'cart') {
            icon = 'sprite-shopping-basket--plus';
        } else if (id === 'remove_from_cart') {
            icon = 'sprite-shopping-basket--minus';
        } else if (id === 'purchase') {
            icon = 'sprite-credit-card-green';
        } else {
            icon = 'rees46-menu-icon';
        }

        return '<div style="height: 16px; width: 20px; float: left;" class="' + icon + '"></div>&nbsp;<span style="float: left; height: 16px; line-height: 16px;">' + value + '</span>';
    },
    columnStatusRenderer: function (value, meta, record) {
        value = value || 0;

        if (value > 0) {
            return '<div style="height: 16px; width: 16px; float: left;" class="sprite-tick-small"></div>&nbsp;<span style="float: left; height: 16px; line-height: 16px; color:green;">{s name="event_active"}Active{/s}</span>';
        } else {
            return '<div style="height: 16px; width: 16px; float: left;" class="sprite-cross-small"></div>&nbsp;<span style="float: left; height: 16px; line-height: 16px; color:red;">{s name="event_expired"}Expired{/s}</span>';
        }
    },
});
//{/block}
