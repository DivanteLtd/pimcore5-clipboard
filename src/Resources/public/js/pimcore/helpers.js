pimcore.registerNS("pimcore.plugin.DivanteClipboardBundle.helpers.x");

pimcore.plugin.DivanteClipboardBundle.helpers.openClipboard = function () {
    var key = 'clipboard';
    try {
        pimcore.globalmanager.get(key).activate();
    } catch (e) {
        pimcore.globalmanager.add(key, new pimcore.plugin.DivanteClipboardBundle.Clipboard());
    }
};
