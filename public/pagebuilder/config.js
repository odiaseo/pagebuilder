/**
 * Created with JetBrains PhpStorm.
 * User: odiaseo
 * Date: 08/12/13
 * Time: 00:41
 * To change this template use File | Settings | File Templates.
 */

require.config(
    {
        baseUrl: "/pagebuilder/libs/",
        paths: {
            "domReady": "domReady",
            "fancybox": '../../fancybox/jquery.fancybox.pack',
            "toolbar": 'buttons',
            "jquery": "jquery-1.9.1.min",
            "multiselect": '../../jqGrid/plugins/ui.multiselect',
            "json2": "json2",
            "pnotify": 'jquery.pnotify.min',
            "require": "require.min",
            "select2": "select2.min",
            "tagsinput": 'jquery.tagsinput.min',
            "templates": "../templates",
            "text": "text",
            "underscore": "lodash"
        },

        // Sets the configuration for your third party scripts that are not AMD compatible
        shim: {
            "pnotify": "pnotify",
            "fancybox": 'fancybox',
            "multiselect": "multiselect"
        }
    }
);