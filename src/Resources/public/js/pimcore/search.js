pimcore.registerNS("pimcore.plugin.DivanteClipboardBundle.Search");

pimcore.plugin.DivanteClipboardBundle.Search = Class.create(pimcore.object.search, {

    urlGetBatchJobs: "/admin/object-helper/get-clipboard-batch-jobs",

    batchPrepare: function (columnIndex, onlySelected) {
        // no batch for system properties
        if(this.systemColumns.indexOf(this.grid.getColumns()[columnIndex].dataIndex) > -1) {
            return;
        }

        var jobs = [];
        if(onlySelected) {
            var selectedRows = this.grid.getSelectionModel().getSelection();
            for (var i=0; i<selectedRows.length; i++) {
                jobs.push(selectedRows[i].get("id"));
            }
            this.batchOpen(columnIndex,jobs);

        } else {

            var filters = "";
            var condition = "";

            if(this.sqlButton.pressed) {
                condition = this.sqlEditor.getValue();
            } else {
                var filterData = this.store.getFilters().items;
                if(filterData.length > 0) {
                    filters = this.store.getProxy().encodeFilters(filterData);
                }
            }

            var params = {
                filter: filters,
                condition: condition,
                classId: this.classId,
                folderId: this.element.id,
                objecttype: this.objecttype,
                language: this.gridLanguage
            };


            Ext.Ajax.request({
                url: this.urlGetBatchJobs,
                params: params,
                success: function (columnIndex,response) {
                    var rdata = Ext.decode(response.responseText);
                    if (rdata.success && rdata.jobs) {
                        this.batchOpen(columnIndex, rdata.jobs);
                    }

                }.bind(this,columnIndex)
            });
        }

    },

    createGrid: function (fromConfig, response, settings, save) {
        var itemsPerPage = pimcore.helpers.grid.getDefaultPageSize(-1);

        var fields = [];
        if (response.responseText) {
            response = Ext.decode(response.responseText);

            if (response.pageSize) {
                itemsPerPage = response.pageSize;
            }

            fields = response.availableFields;
            this.gridLanguage = response.language;
            this.sortinfo = response.sortinfo;

            this.settings = response.settings || {};
            this.availableConfigs = response.availableConfigs;
            this.sharedConfigs = response.sharedConfigs;

            if (response.onlyDirectChildren) {
                this.onlyDirectChildren = response.onlyDirectChildren;
            }
        } else {
            fields = response;
            this.settings = settings;
            this.buildColumnConfigMenu();
        }

        this.fieldObject = {};
        for (var i = 0; i < fields.length; i++) {
            this.fieldObject[fields[i].key] = fields[i];
        }

        this.cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                clicksToEdit: 1,
                listeners: {
                    beforeedit: function (editor, context, eOpts) {
                        //need to clear cached editors of cell-editing editor in order to
                        //enable different editors per row
                        var editors = editor.editors;
                        editors.each(function (editor) {
                            if (typeof editor.column.config.getEditor !== "undefined") {
                                Ext.destroy(editor);
                                editors.remove(editor);
                            }
                        });
                    }
                }
            }
        );

        var plugins = [this.cellEditing, 'pimcore.gridfilters'];

        // get current class
        var classStore = pimcore.globalmanager.get("object_types_store");
        var klass = classStore.getById(this.classId);

        var gridHelper = new pimcore.object.helpers.grid(
            klass.data.text,
            fields,
            "/admin/object/grid-proxy?classId=" + this.classId + "&folderId=" + this.object.id,
            {
                language: this.gridLanguage,
                // limit: itemsPerPage
            },
            false
        );

        gridHelper.showSubtype = false;
        gridHelper.enableEditor = true;
        gridHelper.limit = itemsPerPage;


        var propertyVisibility = klass.get("propertyVisibility");

        this.store = gridHelper.getStore(this.noBatchColumns);
        if (this.sortinfo) {
            this.store.sort(this.sortinfo.field, this.sortinfo.direction);
        }
        this.store.getProxy().setExtraParam("only_direct_children", this.onlyDirectChildren);
        this.store.getProxy().setExtraParam("clipboard", true);
        this.store.setPageSize(itemsPerPage);

        var gridColumns = gridHelper.getGridColumns();

        // add filters
        this.gridfilters = gridHelper.getGridFilters();

        this.languageInfo = new Ext.Toolbar.TextItem({
            text: t("grid_current_language") + ": " + (this.gridLanguage == "default" ? t("default") : pimcore.available_languages[this.gridLanguage])
        });

        this.toolbarFilterInfo = new Ext.Button({
            iconCls: "pimcore_icon_filter_condition",
            hidden: true,
            text: '<b>' + t("filter_active") + '</b>',
            tooltip: t("filter_condition"),
            handler: function (button) {
                Ext.MessageBox.alert(t("filter_condition"), button.pimcore_filter_condition);
            }.bind(this)
        });

        this.clearFilterButton = new Ext.Button({
            iconCls: "pimcore_icon_clear_filters",
            hidden: true,
            text: t("clear_filters"),
            tooltip: t("clear_filters"),
            handler: function (button) {
                this.grid.filters.clearFilters();
                this.toolbarFilterInfo.hide();
                this.clearFilterButton.hide();
            }.bind(this)
        });


        this.createSqlEditor();

        this.checkboxOnlyDirectChildren = new Ext.form.Checkbox({
            name: "onlyDirectChildren",
            style: "margin-bottom: 5px; margin-left: 5px",
            checked: this.onlyDirectChildren,
            boxLabel: t("only_children"),
            listeners: {
                "change": function (field, checked) {
                    this.grid.filters.clearFilters();

                    this.store.getProxy().setExtraParam("only_direct_children", checked);

                    this.onlyDirectChildren = checked;
                    this.pagingtoolbar.moveFirst();
                }.bind(this)
            }
        });

        var hideSaveColumnConfig = !fromConfig || save;

        this.saveColumnConfigButton = new Ext.Button({
            tooltip: t('save_column_configuration'),
            iconCls: "pimcore_icon_publish",
            hidden: hideSaveColumnConfig,
            handler: function () {
                var asCopy = !(this.settings.gridConfigId > 0);
                this.saveConfig(asCopy)
            }.bind(this)
        });

        this.columnConfigButton = new Ext.SplitButton({
            text: t('grid_options'),
            iconCls: "pimcore_icon_table_col pimcore_icon_overlay_edit",
            handler: function () {
                this.openColumnConfig();
            }.bind(this),
            menu: []
        });

        this.buildColumnConfigMenu();

        // grid
        this.grid = Ext.create('Ext.grid.Panel', {
            frame: false,
            store: this.store,
            columns: gridColumns,
            columnLines: true,
            stripeRows: true,
            bodyCls: "pimcore_editable_grid",
            border: true,
            selModel: gridHelper.getSelectionColumn(),
            trackMouseOver: true,
            loadMask: true,
            plugins: plugins,
            viewConfig: {
                forceFit: false,
                xtype: 'patchedgridview'
            },
            cls: 'pimcore_object_grid_panel',
            tbar: [this.languageInfo, "-", this.toolbarFilterInfo, this.clearFilterButton, "->",
                this.columnConfigButton,
                this.saveColumnConfigButton
            ]
        });

        this.grid.on("columnmove", function () {
            this.saveColumnConfigButton.show()
        }.bind(this));
        this.grid.on("columnresize", function () {
            this.saveColumnConfigButton.show()
        }.bind(this));

        this.grid.on("rowcontextmenu", this.onRowContextmenu);

        this.grid.on("afterrender", function (grid) {
            this.updateGridHeaderContextMenu(grid);
        }.bind(this));

        this.grid.on("sortchange", function (ct, column, direction, eOpts) {
            this.sortinfo = {
                field: column.dataIndex,
                direction: direction
            };
        }.bind(this));

        // check for filter updates
        this.grid.on("filterchange", function () {
            this.filterUpdateFunction(this.grid, this.toolbarFilterInfo, this.clearFilterButton);
        }.bind(this));

        gridHelper.applyGridEvents(this.grid);

        this.pagingtoolbar = pimcore.helpers.grid.buildDefaultPagingToolbar(this.store, {pageSize: itemsPerPage});

        this.editor = new Ext.Panel({
            layout: "border",
            items: [new Ext.Panel({
                items: [this.grid],
                region: "center",
                layout: "fit",
                bbar: this.pagingtoolbar
            })]
        });

        this.layout.removeAll();
        this.layout.add(this.editor);
        this.layout.updateLayout();

        if (save) {
            if (this.settings.isShared) {
                this.settings.gridConfigId = null;
            }
            this.saveConfig(false);
        }
    },

    onRowContextmenu: function (grid, record, tr, rowIndex, e, eOpts) {

        var menu = new Ext.menu.Menu();
        var data = grid.getStore().getAt(rowIndex);
        var selectedRows = grid.getSelectionModel().getSelection();

        if (selectedRows.length <= 1) {

            menu.add(new Ext.menu.Item({
                text: t('open'),
                iconCls: "pimcore_icon_open",
                handler: function (data) {
                    pimcore.helpers.openObject(data.data.id, "object");
                }.bind(this, data)
            }));

            if (pimcore.elementservice.showLocateInTreeButton("object")) {
                menu.add(new Ext.menu.Item({
                    text: t('show_in_tree'),
                    iconCls: "pimcore_icon_show_in_tree",
                    handler: function () {
                        try {
                            try {
                                pimcore.treenodelocator.showInTree(record.id, "object", this);
                            } catch (e) {
                                console.log(e);
                            }

                        } catch (e2) {
                            console.log(e2);
                        }
                    }
                }));
            }

            menu.add(new Ext.menu.Item({
                text: t('delete'),
                iconCls: "pimcore_icon_delete",
                handler: function (data) {
                    var store = this.getStore();
                    Ext.Ajax.request({
                        'url': '/admin/clipboard/delete-object',
                        'params': {
                            objectId: data.data.id
                        },
                        'method': 'POST',
                        'success': store.reload.bind(this.getStore())
                    });
                }.bind(grid, data)
            }));
        } else {
            menu.add(new Ext.menu.Item({
                text: t('open_selected'),
                iconCls: "pimcore_icon_open",
                handler: function (data) {
                    var selectedRows = grid.getSelectionModel().getSelection();
                    for (var i = 0; i < selectedRows.length; i++) {
                        pimcore.helpers.openObject(selectedRows[i].data.id, "object");
                    }
                }.bind(this, data)
            }));
        }

        e.stopEvent();
        menu.showAt(e.pageX, e.pageY);
    }
});
