//{namespace name="backend/rees46/main"}

//{block name="backend/rees46/view/settings/blocks"}
Ext.define('Shopware.apps.Rees46.view.settings.Blocks', {
    extend: 'Ext.form.FieldSet',
    alias: 'widget.blocks',
    stateId: 'blocks',
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

        me.title = '{s name="blocks_form"}Product Recommendations Blocks{/s}';
        me.items = me.createGrid();

        me.callParent(arguments);
    },
    createGrid: function() {
        var me = this;

        return {
            xtype: 'element-property',
            name: 'blocks',
            border: false,
            plugins: [{
                ptype: 'cellediting',
                pluginId: 'cellediting',
                clicksToEdit: 1
            }, {
                ptype: 'gridtranslation',
                pluginId: 'translation',
                translationType: 'rees46_blocks',
                translationMerge: true
            }],
            columns: me.createColumns(),
            store: Ext.create('Shopware.apps.Rees46.store.Blocks'),
        }
    },
    createColumns: function() {
        var me = this;

        me.pageStore = Ext.create('Ext.data.Store', {
            fields: [ 'id', 'name' ],
            data: [
                { id: 'index', name: '{s name="blocks_page_index"}Home Page{/s}' },
                { id: 'listing', name: '{s name="blocks_page_category"}Category Page{/s}' },
                { id: 'detail', name: '{s name="blocks_page_product"}Product Detail Page{/s}' },
                { id: 'checkout', name: '{s name="blocks_page_cart"}Shopping Cart Page{/s}' },
                { id: 'search', name: '{s name="blocks_page_search"}Search Results Page{/s}' },
            ]
        });

        me.typeStore = Ext.create('Ext.data.Store', {
            fields: [ 'id', 'name' ],
            data: [
                { id: 'interesting', name: '{s name="blocks_type_interesting"}You May Also Like{/s}' },
                { id: 'also_bought', name: '{s name="blocks_type_also_bought"}Frequently Bought Together{/s}' },
                { id: 'similar', name: '{s name="blocks_type_similar"}Similar Products{/s}' },
                { id: 'popular', name: '{s name="blocks_type_popular"}Popular Products{/s}' },
                { id: 'see_also', name: '{s name="blocks_type_see_also"}Recommended For You{/s}' },
                { id: 'recently_viewed', name: '{s name="blocks_type_recently_viewed"}You Recently Viewed{/s}' },
                { id: 'buying_now', name: '{s name="blocks_type_buying_now"}Trending Products{/s}' },
                { id: 'search', name: '{s name="blocks_type_search"}Customers Who Looked For This Item Also Bought{/s}' },
                { id: 'supply', name: '{s name="blocks_type_supply"}Regular Purchase{/s}' },
            ]
        });

        me.templateStore = Ext.create('Ext.data.Store', {
            fields: [ 'id', 'name' ],
            data: [
                { id: 'rees46', name: '{s name="blocks_template_rees46"}REES46{/s}' },
                { id: 'minimal', name: '{s name="blocks_template_minimal"}Minimal{/s}' },
                { id: 'image', name: '{s name="blocks_template_image"}Grid{/s}' },
                { id: 'list', name: '{s name="blocks_template_list"}List{/s}' },
                { id: 'basic', name: '{s name="blocks_template_basic"}Basic{/s}' },
            ]
        });

        return [
            {
                dataIndex: 'page',
                text: '{s name="blocks_page"}Page{/s}',
                flex: 1,
                editor: {
                    xtype: 'element-select',
                    store: me.pageStore,
                    displayField: 'name',
                    valueField: 'id',
                },
                renderer: me.onSelectRenderer,
            },
            {
                dataIndex: 'type',
                text: '{s name="blocks_type"}Block Type{/s}',
                flex: 1,
                editor: {
                    xtype: 'element-select',
                    store: me.typeStore,
                    displayField: 'name',
                    valueField: 'id',
                },
                renderer: me.onSelectRenderer,
            },
            {
                dataIndex: 'title',
                text: '{s name="blocks_title"}Block Title{/s}',
                flex: 1,
                editor: {
                    xtype: 'element-text',
                },
            },
            {
                dataIndex: 'limit',
                text: '{s name="blocks_limit"}Product Limit{/s}',
                flex: 1,
                editor: {
                    xtype: 'element-number',
                    minValue: 0,
                    decimalPrecision: 0,
                },
            },
            {
                dataIndex: 'template',
                text: '{s name="blocks_template"}Block Template{/s}',
                flex: 1,
                editor: {
                    xtype: 'element-select',
                    store: me.templateStore,
                    displayField: 'name',
                    valueField: 'id',
                },
                renderer: me.onSelectRenderer,
            },
            {
                dataIndex: 'position',
                text: '{s name="blocks_position"}Block Position{/s}',
                flex: 1,
                editor: {
                    xtype: 'element-number',
                    decimalPrecision: 0,
                },
            },
            {
                dataIndex: 'status',
                text: '{s name="blocks_status"}Block Status{/s}',
                flex: 1,
                editor: {
                    xtype: 'element-boolean',
                },
            },
        ];
    },
    onSelectRenderer: function(value, metadata, record, rowIndex, colIndex) {
        var me = this,
            column = me.columns[colIndex];

        if(!column.editor && !column._editor) {
            return value;
        }
        if(!column._editor) {
            column._editor = column.editor;
        }
        var editor = column._editor,
            store = column._editor.store,
            index,
            record;

        if(!value) {
            return editor.emptyText;
        }
        index = store.find(editor.valueField, value);
        record = store.getAt(index);
        if(!record) {
            return editor.emptyText;
        }

        return record.get(editor.displayField);
    },
});
//{/block}
