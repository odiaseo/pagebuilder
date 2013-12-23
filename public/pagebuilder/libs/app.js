require(['./../config'], function () {
    require(['domReady', "toolbar", ],
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
});

(function (w, synergy, undefined) {
    if (synergy == undefined) {
        synergy = {};
    }
    synergy['pageBuilder'] = toolbar;
})(window, synergyDataGrid);