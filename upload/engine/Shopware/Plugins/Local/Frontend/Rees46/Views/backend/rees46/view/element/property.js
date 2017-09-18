//{namespace name="backend/rees46/main"}

//{block name="backend/rees46/view/element/property"}
Ext.define('Shopware.apps.Rees46.view.element.Property', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.element-property',
    plugins: [{
        ptype: 'cellediting',
        pluginId: 'cellediting',
        clicksToEdit: 1
    }],
    margin: '0 0 0 0',
    sortableColumns: false,
    border: false,
    viewConfig: {
        emptyText: '{s name="blocks_empty"}No blocks{/s}'
    },
    initComponent: function() {
        var me = this;

        Ext.applyIf(me, {
            dockedItems: [
                me.createToolbar()
            ],
            columns: []
        });

        me.columns = Ext.Array.merge(me.columns, me.getColumns());

        me.callParent(arguments);
    },
    getColumns: function() {
        var me = this;

        return [
            me.getActionColumn()
        ];
    },
    getActionColumn: function() {
        var me = this;

        return {
            xtype: 'actioncolumn',
            width: 25,
            items: [{
              iconCls: 'sprite-minus-circle-frame',
              action: 'delete',
              tooltip: '{s name="blocks_delete"}Delete block{/s}',
              handler: function (view, rowIndex, colIndex, item, opts, record) {
                  me.fireEvent('delete', view, record, rowIndex);
              },
            }]
        };
    },
    createToolbar: function () {
        var me = this;

        me.toolbar = Ext.create('Ext.toolbar.Toolbar', {
            dock: 'bottom',
            border: false,
            items: me.createToolbarItems()
        });

        return me.toolbar;
    },
    createToolbarItems: function () {
        var me = this;

        return [
            me.createAddButton(),
            '->',
            me.createSaveButton(),
        ];
    },
    createAddButton: function () {
        var me = this;

        me.addButton = Ext.create('Ext.button.Button', {
            text: '{s name="blocks_add"}Add block{/s}',
            iconCls: 'sprite-plus-circle-frame',
            cls: 'secondary small',
            action: 'add',
        });

        return me.addButton;
    },
    createSaveButton: function () {
        var me = this;

        me.saveButton = Ext.create('Ext.button.Button', {
            text: '{s name="blocks_save"}Save blocks{/s}',
            cls: 'primary small',
            handler: function () {
                me.fireEvent('blocks-save', me);
            }
        });

        return me.saveButton;
    },
});
//{/block}
