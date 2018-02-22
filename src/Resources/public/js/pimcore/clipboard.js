pimcore.registerNS("pimcore.plugin.DivanteClipboardBundle.Clipboard");

pimcore.plugin.DivanteClipboardBundle.Clipboard = Class.create(pimcore.object.abstract, {

    id: 1,
    type: "folder",
    managerKey: "clipboard",

    initialize: function () {
        this.options = undefined;
        this.getData();
    },

    getData: function () {
        var options = this.options || {};
        Ext.Ajax.request({
            url: "/admin/object/get-clipboard",
            params: {id: this.id},
            ignoreErrors: options.ignoreNotFoundError,
            success: this.getDataComplete.bind(this),
            failure: function () {
                pimcore.globalmanager.remove(this.managerKey);
            }.bind(this)
        });
    },

    getDataComplete: function (response) {
        try {
            this.data = Ext.decode(response.responseText);
            this.search = new pimcore.plugin.DivanteClipboardBundle.Search(this, "folder");
            this.addTab();
        } catch (e) {
            console.log(e);
            this.closeObject();
        }
    },

    addTab: function () {

        var tabTitle = this.data.general.o_key;
        if (this.id == 1) {
            tabTitle = t("divante_clipboard");
        }

        this.tabPanel = Ext.getCmp("pimcore_panel_tabs");
        var tabId = this.managerKey;

        this.tab = new Ext.Panel({
            id: tabId,
            title: tabTitle,
            closable: true,
            layout: "border",
            items: [this.getTabPanel()],
            iconCls: "pimcore_icon_export",
            object: this
        });

        // remove this instance when the panel is closed
        this.tab.on("destroy", function () {
            pimcore.globalmanager.remove(this.managerKey);
        }.bind(this));

        this.tab.on("activate", function () {
            this.tab.updateLayout();
            pimcore.layout.refresh();
        }.bind(this));

        this.tab.on("afterrender", function (tabId) {
            this.tabPanel.setActiveItem(tabId);
            // load selected class if available
            if (this.data["selectedClass"]) {
                this.search.setClass(this.data["selectedClass"]);
            }
        }.bind(this, tabId));

        this.tabPanel.add(this.tab);

        // recalculate the layout
        pimcore.layout.refresh();
    },

    getTabPanel: function () {

        var items = [];

        var search = this.search.getLayout();
        if (search) {
            items.push(search);
        }

        this.tabbar = new Ext.TabPanel({
            tabPosition: "top",
            region: "center",
            deferredRender: true,
            enableTabScroll: true,
            border: false,
            items: items,
            activeTab: 0
        });

        return this.tabbar;
    },

    closeObject: function () {
        try {
            var panel = Ext.getCmp(this.managerKey);
            if (panel) {
                panel.close();
            } else {
                console.log("to close element not found, doing nothing.");
            }

            pimcore.globalmanager.remove(this.managerKey);
        } catch (e) {
            console.log(e);
        }
    },

    activate: function () {
        var tabId = this.managerKey;
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.setActiveItem(tabId);
    }
});
