pimcore.registerNS("pimcore.plugin.DivanteClipboardBundle");

pimcore.plugin.DivanteClipboardBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.DivanteClipboardBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        var extrasMenu = pimcore.globalmanager.get("layout_toolbar").extrasMenu;

        extrasMenu.insert(1, {
            text: t("divante_clipboard"),
            iconCls: "pimcore_icon_export",
            handler:  this.showClipboard.bind(this)
        });

    },

    showClipboard: function() {
        pimcore.helpers.showNotification("Clipboard", "Work in-progress", "error", "Quite soon you'll see you here your Clipboard! :)");
    }
});

new pimcore.plugin.DivanteClipboardBundle();
