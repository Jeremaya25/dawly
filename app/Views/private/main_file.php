<?php $this->extend('layouts/base_layout') ?>

<?php $this->section('content')?>
  <script src="//cdnjs.cloudflare.com/ajax/libs/require.js/2.3.2/require.min.js"></script>
  <script>
    define('elFinderConfig', {
        // elFinder options (REQUIRED)
        // Documentation for client options:
        // https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
        defaultOpts: {
            url: '<?php echo $connector ?>', // connector URL (REQUIRED)
            height: 600,
            commandsOptions: {
                edit: {
                    extraOptions: {
                        // set API key to enable Creative Cloud image editor
                        // see https://console.adobe.io/
                        creativeCloudApiKey: '',
                        // browsing manager URL for CKEditor, TinyMCE
                        // uses self location with the empty value
                        managerUrl: ''
                    }
                },
                quicklook: {
                    // to enable preview with Google Docs Viewer
                    googleDocsMimes: ['application/pdf', 'image/tiff', 'application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
                }
            }
            // bootCalback calls at before elFinder boot up
            ,
            bootCallback: function(fm, extraObj) {
                /* any bind functions etc. */
                fm.bind('init', function() {
                    // any your code
                });
                // for example set document.title dynamically.
                var title = document.title;
                fm.bind('open', function() {
                    var path = '',
                        cwd = fm.cwd();
                    if (cwd) {
                        path = fm.path(cwd.hash) || null;
                    }
                    document.title = path ? path + ':' + title : title;
                }).bind('destroy', function() {
                    document.title = title;
                });
            }
        },
        managers: {
            // 'DOM Element ID': { /* elFinder options of this DOM Element */ }
            'elfinder': {}
        }
    });
    define('returnVoid', void 0);
    (function() {
        var // elFinder version
            elver = '<?php echo elFinder::getApiFullVersion() ?>',
            // jQuery and jQueryUI version
            jqver = '3.2.1',
            uiver = '1.12.1',

            // Detect language (optional)
            lang = (function() {
                var locq = window.location.search,
                    fullLang, locm, lang;
                if (locq && (locm = locq.match(/lang=([a-zA-Z_-]+)/))) {
                    // detection by url query (?lang=xx)
                    fullLang = locm[1];
                } else {
                    // detection by browser language
                    fullLang = (navigator.browserLanguage || navigator.language || navigator.userLanguage);
                }
                lang = fullLang.substr(0, 2);
                if (lang === 'ja') lang = 'jp';
                else if (lang === 'pt') lang = 'pt_BR';
                else if (lang === 'ug') lang = 'ug_CN';
                else if (lang === 'zh') lang = (fullLang.substr(0, 5).toLowerCase() === 'zh-tw') ? 'zh_TW' : 'zh_CN';
                return lang;
            })(),

            // Start elFinder (REQUIRED)
            start = function(elFinder, editors, config) {
                // load jQueryUI CSS
                elFinder.prototype.loadCss('//cdnjs.cloudflare.com/ajax/libs/jqueryui/' + uiver + '/themes/smoothness/jquery-ui.css');

                $(function() {
                    var optEditors = {
                            commandsOptions: {
                                edit: {
                                    editors: Array.isArray(editors) ? editors : []
                                }
                            }
                        },
                        opts = {};

                    // Interpretation of "elFinderConfig"
                    if (config && config.managers) {
                        $.each(config.managers, function(id, mOpts) {
                            opts = Object.assign(opts, config.defaultOpts || {});
                            // editors marges to opts.commandOptions.edit
                            try {
                                mOpts.commandsOptions.edit.editors = mOpts.commandsOptions.edit.editors.concat(editors || []);
                            } catch (e) {
                                Object.assign(mOpts, optEditors);
                            }
                            // Make elFinder
                            $('#' + id).elfinder(
                                // 1st Arg - options
                                $.extend(true, {
                                    lang: lang
                                }, opts, mOpts || {}),
                                // 2nd Arg - before boot up function
                                function(fm, extraObj) {
                                    // `init` event callback function
                                    fm.bind('init', function() {
                                        // Optional for Japanese decoder "extras/encoding-japanese.min"
                                        delete fm.options.rawStringDecoder;
                                        if (fm.lang === 'jp') {
                                            require(
                                                ['encoding-japanese'],
                                                function(Encoding) {
                                                    if (Encoding.convert) {
                                                        fm.options.rawStringDecoder = function(s) {
                                                            return Encoding.convert(s, {
                                                                to: 'UNICODE',
                                                                type: 'string'
                                                            });
                                                        };
                                                    }
                                                }
                                            );
                                        }
                                    });
                                }
                            );
                        });
                    } else {
                        alert('"elFinderConfig" object is wrong.');
                    }
                });
            },

            // JavaScript loader (REQUIRED)
            load = function() {
                require(
                    [
                        'elfinder', 'extras/editors.default' // load text, image editors
                        , 'elFinderConfig'
                        //  , 'extras/quicklook.googledocs'  // optional preview for GoogleApps contents on the GoogleDrive volume
                    ],
                    start,
                    function(error) {
                        alert(error.message);
                    }
                );
            },

            // is IE8? for determine the jQuery version to use (optional)
            ie8 = (typeof window.addEventListener === 'undefined' && typeof document.getElementsByClassName === 'undefined');

        // config of RequireJS (REQUIRED)
        require.config({
            baseUrl: '//cdnjs.cloudflare.com/ajax/libs/elfinder/' + elver + '/js',
            paths: {
                'jquery': '//cdnjs.cloudflare.com/ajax/libs/jquery/' + (ie8 ? '1.12.4' : jqver) + '/jquery.min',
                'jquery-ui': '//cdnjs.cloudflare.com/ajax/libs/jqueryui/' + uiver + '/jquery-ui.min',
                'elfinder': 'elfinder.min',
                'encoding-japanese': '//cdn.rawgit.com/polygonplanet/encoding.js/master/encoding.min'
            },
            waitSeconds: 10 // optional
        });

        // load JavaScripts (REQUIRED)
        load();

    })();
  </script>
  <div class="card m-auto p-3" style="max-width: 750px">
    <div class="card-body">
        <div class="text-center">
            <a href="<?=base_url("/private")?>">
                <button class="btn btn-outline-secondary">Url</button>
            </a>
            <a href="<?=base_url("/private/file")?>">
                <button class="btn btn-outline-secondary active">File</button>
            </a>
        </div>
      <h1 class="card-title text-center mb-4">Choose your file</h1>
      <form action="<?=base_url()?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
          <div id="elfinder"></div>
        </div>
      </form>
    </div>
  </div>
  <div class="m-auto mt-3" style="width:fit-content">
    <a href="<?=base_url("/private/dashboard")?>">
      <button class="btn btn-secondary">Manage URLS</button>
    </a>
    <?php if(has_permission("urls.manage")): ?>
      <a href="<?=base_url("/private/users")?>">
        <button class="btn btn-secondary">Manage Users</button>
      </a>
    <?php endif ?>
  </div>
<?php $this->endSection() ?>
