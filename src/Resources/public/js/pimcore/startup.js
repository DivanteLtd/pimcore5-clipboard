pimcore.registerNS("pimcore.plugin.DivanteClipboardBundle");

pimcore.plugin.DivanteClipboardBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.DivanteClipboardBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function () {
        var user = pimcore.globalmanager.get("user");
        if (user.isAllowed("objects") && this.isEnabledInPerspective()) {
            var toolbar = pimcore.globalmanager.get("layout_toolbar");
            var clipboardToolbarItem = {
                text: t("divante_clipboard"),
                iconCls: "pimcore_icon_export",
                handler:  this.showClipboard.bind(this)
            };

            if (toolbar.extrasMenu === undefined) {
                this.initExtrasMenu(toolbar);
            }

            var glossaryRecordIndex = toolbar.extrasMenu.items.findIndex("text", t("Glossary"));
            var clipboardIndex = 0;
            if (glossaryRecordIndex >= 0) {
                clipboardIndex = glossaryRecordIndex + 1;
            }
            toolbar.extrasMenu.insert(clipboardIndex, clipboardToolbarItem);

        }
    },

    prepareObjectTreeContextMenu: function(menu, tree, record) {
        if (record.data.type !== "folder" && record.data.permissions.view && this.isEnabledInPerspective()) {
            var copyRecordIndex = menu.items.findIndex("text", t("copy"));
            var clipboardMenuItem = {
                text: t("divante_clipboard_add"),
                iconCls: "pimcore_icon_export",
                handler: this.addObjectToClipboard.bind(this, record.data.id)
            };

            if (copyRecordIndex < 0) {
                menu.add(clipboardMenuItem);
            } else {
                menu.insert(copyRecordIndex, clipboardMenuItem);
            }
        }
    },

    prepareOnRowContextmenu: function (menu, obj, selectedRows) {
        if (selectedRows.length == 1) {
            var user = pimcore.globalmanager.get("user");
            if (user.isAllowed("objects") && this.isEnabledInPerspective()) {
                var openButtonIndex = menu.items.findIndex("text", t("open"));
                var record = selectedRows[0];

                menu.insert(openButtonIndex + 1, new Ext.menu.Item({
                    text: t('divante_clipboard_add'),
                    iconCls: "pimcore_icon_export",
                    handler: this.addObjectToClipboard.bind(this, record.data.id)
                }));
            }
        }
    },

    initExtrasMenu: function (toolbar) {
        var extrasMenu = new Ext.menu.Menu({
            items: [],
            shadow: false,
            cls: "pimcore_navigation_flyout"
        });
        toolbar.extrasMenu = extrasMenu;

        Ext.get("pimcore_menu_extras").on("mousedown", toolbar.showSubMenu.bind(extrasMenu)).show();
    },

    isEnabledInPerspective: function() {
        var perspectiveCfg = pimcore.globalmanager.get("perspective");
        return perspectiveCfg.inToolbar("extras") && perspectiveCfg.inToolbar("extras.clipboard");
    },

    showClipboard: function() {
        pimcore.plugin.DivanteClipboardBundle.helpers.openClipboard();
    },

    addObjectToClipboard: function(objectId) {
        Ext.Ajax.request({
            'url': '/admin/clipboard/add-object',
            'params': {
                objectId: objectId
            },
            'method': 'POST',
            'success': function (response, options) {
                var result = Ext.decode(response.responseText);
                if (result && result.success) {
                    pimcore.helpers.showNotification(t('success'), t('divante_clipboard_add_success'), 'success');
                } else {
                    pimcore.helpers.showPrettyError(
                        result.type,
                        t("error"),
                        t("divante_clipboard_error_adding_object"),
                        result.message,
                        result.stack,
                        result.code
                    );
                }
            }
        });
    }
});

new pimcore.plugin.DivanteClipboardBundle();
