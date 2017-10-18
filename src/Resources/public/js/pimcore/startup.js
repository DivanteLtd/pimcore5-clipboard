pimcore.registerNS("pimcore.plugin.DivanteClipboardBundle");

pimcore.plugin.DivanteClipboardBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.DivanteClipboardBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("DivanteClipboardBundle ready!");
    }
});

var DivanteClipboardBundlePlugin = new pimcore.plugin.DivanteClipboardBundle();
