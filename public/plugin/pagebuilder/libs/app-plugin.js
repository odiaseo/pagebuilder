/**
 * Add the lines below to your require config file
 *
 * "page-builder": "/plugins/pagebuilder/app-plugin",
 * "page-toolbar": "/plugins/pagebuilder/buttons"
 *
 */
require(['domReady', "page-toolbar", ],
    function (domReady, toolbar) {
        domReady(function () {
            (function (w, synergy, undefined) {
                if (synergy == undefined) {
                    synergy = {};
                }
                synergy['pageBuilder'] = toolbar;
            })(window, synergyDataGrid);
        });
    }
);


if (typeof(synergyDataGrid) == 'undefined') {
    synergyDataGrid = {};
}

(function (w, synergy, undefined) {
    if (synergy == undefined) {
        synergy = {};
    }
    synergy['pageBuilder'] = toolbar;
})(window, synergyDataGrid);