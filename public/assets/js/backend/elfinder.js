define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template'], function ($, undefined, Backend, Table, Form, Template) {


});

define('elFinderConfig', {
    // elFinder options (REQUIRED)
    // Documentation for client options:
    // https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
    defaultOpts: {
        url: 'php/connector.minimal.php' // connector URL (REQUIRED)
        , commandsOptions: {
            edit: {
                extraOptions: {
                    // set API key to enable Creative Cloud image editor
                    // see https://console.adobe.io/
                    creativeCloudApiKey: '',
                    // browsing manager URL for CKEditor, TinyMCE
                    // uses self location with the empty value
                    managerUrl: ''
                }
            }
            , quicklook: {
                // to enable preview with Google Docs Viewer
                googleDocsMimes: ['application/pdf', 'image/tiff', 'application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            }
        }
        // bootCalback calls at before elFinder boot up
        , bootCallback: function (fm, extraObj) {
            /* any bind functions etc. */
            fm.bind('init', function () {
                // any your code
            });
            // for example set document.title dynamically.
            var title = document.title;
            fm.bind('open', function () {
                var path = '',
                    cwd = fm.cwd();
                if (cwd) {
                    path = fm.path(cwd.hash) || null;
                }
                document.title = path ? path + ':' + title : title;
            }).bind('destroy', function () {
                document.title = title;
            });
        }
    },
    managers: {
        // 'DOM Element ID': { /* elFinder options of this DOM Element */ }
        'elfinder': {}
    }
});
