/**
 * Page builder object
 *
 * @package Page Builder
 * @since 08/12/2013
 */
define(
    [
        "underscore",
        'text!page-templates/layout-manager.html',
        'text!page-templates/popover.html',
        'text!page-templates/sections.html',
        "multiselect",
        'tagsinput',
        'select2',
        'json2',
        'pnotify',
        'fancybox'
    ],
    function (_, layoutTemp, popTemp, sectionTemp) {
        "use strict";

        function _getRowInfo() {
            var rowInfo = $('body').data('rowInfo');
            return rowInfo;
        }

        var pageBuilder = {
            canvasClass: '.template-canvas',
            actionToolbar: '.template-actions',
            removeIconClass: 'icon-remove',
            defaultRowClass: 'row-fluid',
            compiledTemplate: _.template(popTemp),
            attributeKey: 'tagAttributes',
            pageDetails: {},

            init: function () {

                $('.grid-script').each(function () {
                    var gridScript = $(this).text();
                    eval(gridScript);
                });

            },

            poison: function () {
                var dt = new Date();
                return dt.getTime();
            },

            objToHtmlList: function (obj, prefix) {
                if (obj instanceof Object && !(obj instanceof String)) {
                    var ul = $('<ul>');
                    for (var child in obj) {
                        if (_.has(obj[child], 'title')) {
                            var li = $('<li class="asset layout-widget"/>');
                            var opt = [];
                            if (!_.isEmpty(obj[child].options)) {
                                opt = obj[child].options;
                                var optStr = JSON.stringify(opt);
                                li.attr('data-options', optStr);
                                //li.data('options', opt);
                            }

                            li.attr('id', prefix + '-' + obj[child].id);

                            var a = $('<a rel="tooltip"/>');
                            a.text(obj[child].title);
                            a.attr('title', obj[child].description);
                            li.append(a);
                            ul.addClass('asset-list');
                        } else {
                            var li = $('<li class="jstree-open"/>');
                            var a = $('<a href="#"/>');
                            a.text(child);
                            li.append(a);
                            li.append(pageBuilder.objToHtmlList(obj[child], prefix));
                        }
                        ul.append(li);
                    }
                    return ul;
                }
                else {
                    return document.createTextNode(obj);
                }
            },

            outerHtml: function (elm) {
                // this function is used in layout.manager.html
                var $this = $(elm);
                if ($this.length > 1)
                    return $.map($this,function (el) {
                        return $(el).outerHTML();
                    }).join('');
                return $this.clone().wrap('<div/>').parent().html();
            },

            addRowItem: function (attr, dest, rowData, customData) {
                var rowInfo = _getRowInfo();
                rowData = rowData || [];
                var cls = _.has(rowData, 'class') ? rowData['class'] : pageBuilder.defaultRowClass;
                var cssClass = cls.replace(',', ' ');
                var row = $('<div class="template-row"></div>').addClass(pageBuilder.defaultRowClass).data('type', attr);
                var span = '';
                var pn = pageBuilder.poison();
                row.attr('id', 'row_' + pn);

                var handle = $(rowInfo);

                switch (attr) {
                    case 'single':
                        span = $('<div></div>').addClass('span12');
                        break;
                    case '2-cols-eq':
                        span = $('<div></div><div></div>').addClass('span6');
                        break;
                    case '2-cols-ll':
                        span = $('<div class="span8"></div><div class="span4"></div>');
                        break;
                    case '2-cols-rl':
                        span = $('<div class="span4"></div><div class="span8"></div>');
                        break;
                    case '3-cols':
                        span = $('<div></div><div></div><div></div>').addClass('span4');
                        break;
                    case '4-cols':
                        span = $('<div></div><div></div><div></div><div></div>').addClass('span3');
                        break;
                    //case 'custom':
                    default:
                    {
                        if (!customData && attr) {
                            customData = attr.split('-');
                        }

                        for (var i in customData) {
                            if (customData.hasOwnProperty(i)) {
                                var cs = 'span' + customData[i];
                                span += '<div class="' + cs + '"></div>';
                            }
                        }
                        var customType = customData.join('-');
                        row.data('type', customType);
                        break;
                    }
                }

                span = $(span);
                $(span).each(function () {
                    var e = $(this);
                    e.data('class', e.attr('class'));
                });

                var li = $('<li class="asset ui-state-disabled">Empty</li>').data('id', 0);
                var ul = $('<ul class="page-assets"/>').append(li);

                span.addClass('template-item')
                    .append(ul);

                row.append(span);

                $('.template-row', dest).show();
                dest.append(row);

                row.find('.page-assets')
                    .sortable(
                    {
                        items: "li:not(.ui-state-disabled)",
                        connectWith: '.page-assets',
                        tolerance: "pointer",
                        receive: function (event, ui) {
                            if (ui.sender.hasClass("asset-list")) {
                                $('body').data('sort-ok', true);
                            } else {
                                pageBuilder.processAssets($(this));
                            }
                        },
                        update: function () {
                            pageBuilder.processAssets($(this));
                        }
                    }
                );

                row.append(handle);
                var tgr = $('.edit-row', row);
                var param = rowData || {class: cls };

                row.data(pageBuilder.attributeKey, param);
                pageBuilder.initPopover(row, tgr, 'Row Attributes');

                return row;
            },

            initPopover: function (container, trigger, title) {
                title = title || 'Asset Tag Attributes';
                trigger = $(trigger);
                trigger.popover(
                    {
                        placement: 'left',
                        title: title,
                        html: true,
                        content: function () {
                            var id = container.attr('id');
                            var p = id.split('-');
                            var widget = pageBuilder['pageDetails']['widgets']['items'];
                            var def = {};
                            if (_.has(widget, p[1]) && _.has(widget[p[1]], 'options')) {
                                def = widget[p[1]]['options'];
                            }
                            var attr = container.data(pageBuilder.attributeKey);
                            var opt = {};
                            if (_.has(attr, 'options')) {
                                opt = attr['options'];
                            }

                            for (var i in def) {
                                if (!_.has(opt, i)) {
                                    opt[i] = def[i];
                                }
                            }

                            var param = {
                                tagList: pageBuilder['pageDetails']['tags'],
                                elemTag: 'div',
                                options: opt,
                                id: container.attr('id')
                            };
                            return  pageBuilder.compiledTemplate(param);
                        },
                        trigger: 'click'
                    }
                );

                trigger.on('click', function () {
                    var me = $(this);
                    me.closest('.fancybox-inner').css('overflow', 'visible');
                    var pop = me.siblings('.popover');

                    pop.find('.tags')
                        .each(function () {
                            var elm = $(this);
                            elm.tagsInput(
                                {
                                    width: '165px',
                                    height: 'auto',
                                    removeWithBackspace: true,
                                    defaultText: elm.attr('placeholder')
                                }
                            );
                        }
                    );

                    $('#pop-cancel', pop).on('click', function (e) {
                        e.preventDefault();
                        me.popover('hide');
                    });

                    $('#pop-ok', pop).on('click', function (e) {
                        e.preventDefault();
                        var cn = $(this).closest('form.attr-container');
                        var params = cn.serializeArray();
                        var attr = {};

                        for (var i = 0; i < params.length; i++) {
                            var nm = params[i].name.split('_');
                            if (nm.length == 1) {
                                attr[params[i].name] = params[i].value;
                            } else if (_.has(attr, nm[0])) {
                                attr[nm[0]][nm[1]] = params[i].value;
                            } else {
                                attr[nm[0]] = {};
                                attr[nm[0]][nm[1]] = params[i].value;
                            }
                        }
                        container.data(pageBuilder.attributeKey, attr);
                        pageBuilder.notify('Tag attribute', 'Item attribute updated', 'success');
                        me.popover('hide');
                    });

                    var attr = container.data(pageBuilder.attributeKey);

                    pageBuilder.populatePopover(attr, pop);
                    pop.find('select').select2();
                });
            },
            processAssets: function (ul) {
                var tmp = $('#item-buttons-template', $(layoutTemp)).html();
                //tmp = $(tmp);
                //$('.item-delete>i', tmp).addClass(pageBuilder.removeIconClass);

                var kids = ul.children('li');
                if (kids.length > 1) {
                    kids.each(
                        function () {
                            var li = $(this);
                            if (li.data('id') == 0) {
                                li.hide();
                            } else if (li.find('.attr-trigger').length == 0) {
                                li.prepend(tmp);
                                var tgr = $('.attr-trigger', li);
                                pageBuilder.initPopover(li, tgr);
                            }
                        }
                    );
                } else {
                    kids.show();
                }

                pageBuilder.updateSectionCount();

                $.fancybox.update();
            },
            populatePopover: function (attr, pop) {
                if (_.isObject(attr)) {
                    for (var i in attr) {
                        if (attr.hasOwnProperty(i)) {

                            if (_.isObject(attr[i])) {
                                for (var x in attr[i]) {
                                    var inp = $('[name="' + i + '_' + x + '"]', pop);
                                    inp.importTags(attr[i][x]);
                                }
                            } else {
                                var inp = $('[name="' + i + '"]', pop);
                                if (inp.hasClass('tags')) {
                                    inp.importTags(attr[i]);
                                } else {
                                    inp.val(attr[i]);
                                }
                            }
                        }
                    }
                }
            },
            getLayout: function (sections) {
                var layout = {};
                for (var i in sections) {
                    if (sections.hasOwnProperty(i)) {
                        var s = $('#' + i + '-section');
                        var attr = s.data(pageBuilder.attributeKey);
                        layout[i] = {
                            status : s.data('status'),
                            items: pageBuilder.processSection(s)
                        };
                        layout[i][pageBuilder.attributeKey] = attr || {};
                    }
                }
                return layout;

            },
            processSection: function (section) {
                var data = {};
                var id = '.' + pageBuilder.defaultRowClass;
                var rows = $(id, section);
                rows.each(function (index, r) {
                    var elm = $(r);
                    var columns = elm.find('[class^="span"]');
                    var layout = [];
                    columns.each(function (ind, col) {
                        layout[ind] = pageBuilder.processColumn(col);
                    });
                    var param = {
                        rowItems: layout,
                        rowType: elm.data('type')
                    };
                    param[pageBuilder.attributeKey] = elm.data(pageBuilder.attributeKey) || {};
                    data[index] = param;
                });

                return data;
            },
            processColumn: function (col) {
                var elm = $(col);
                var cls = elm.data('class');

                var items = [];

                var ul = elm.find('ul').sortable("toArray");

                for (var k = 0; k < ul.length; k++) {
                    var li = $('#' + ul[k], elm);
                    var attr = li.data(pageBuilder.attributeKey);
                    items[k] = {
                        name: ul[k]
                    };
                    items[k][pageBuilder.attributeKey] = attr || {};
                }

                return {
                    'class': cls,
                    'item': items
                };
            },
            rowSelected: function (elm) {
                elm = $(elm);
                var gridId = elm.data('grid-id');
                var grid = synergyDataGrid[gridId];
                var gsr = grid.getGridParam('selrow');

                if (gsr) {
                    return grid.getRowData(gsr);
                } else {
                    //this.showDialog('norow');
                    pageBuilder.notify('Row Selection',
                        'No row was selected. Please select a row from the grid to before performing the task',
                        'error'
                    );
                    return false;
                }
            },

            checkSum: function () {
                var n = $('input', '#custom-selector');
                var l = n.length;
                var sum = 0;
                var max = 12;
                var res = [];

                for (var i = 0; i < l; i++) {
                    var c = $(n[i]);
                    var v = c.val();
                    sum += parseInt(v);
                    res[i] = v;
                }

                return ((sum - max) == 0) ? res : false;
            },
            manageSections: function (me) {
                me = $(me);
                var rowData = pageBuilder.rowSelected(me);

                if (rowData) {
                    var url = me.data('href').replace(':id', rowData.id);
                    var entity = me.data('entity');
                    var compTemp = _.template(sectionTemp);

                    $.get(url, {}, function (data) {
                        $('.temp').remove();
                        data.updateUrl = url;

                        var tmp = compTemp(data);
                        tmp = $(tmp);
                        $('body').append(tmp);

                        $('.multiselect', tmp).multiselect(
                            {
                                dividerLocation: 0.5,
                                sortable: true,
                                searchable: true,
                                animated: 'fast',
                                show: 'slideDown',
                                hide: 'slideUp',
                                nodeComparator: function (node1, node2) {
                                    var text1 = node1.text(),
                                        text2 = node2.text();
                                    return text1 == text2 ? 0 : (text1 < text2 ? -1 : 1);
                                }
                            }
                        );

                        tmp.dialog({
                            width: 473,
                            height: 400,
                            position: 'center',
                            buttons: {
                                'Update': function () {
                                    var sectionId = $('select[name="sectionId"]').val();
                                    var param = {
                                        'sections': sectionId
                                    };
                                    $.ajax(url,
                                        {
                                            type: 'PUT',
                                            data: param,
                                            success: function (res) {
                                                if (res.error) {
                                                    pageBuilder.notify('Template Sections', res.message, 'error');
                                                } else {
                                                    pageBuilder.notify('Template Sections', res.message, 'success');
                                                }
                                            }
                                        }
                                    );
                                },
                                'Close': function () {
                                    $(this).dialog('close');
                                }
                            }
                        });
                    });
                }
            },
            manageLayout: function (me) {
                var elm = $(me);
                //var toolbarId = elm.data('toolbar-id');
                //var grid = $('#' + toolbarId).data('grid');
                var rowdata = pageBuilder.rowSelected(me);

                if (rowdata) {
                    window.synergyEnableWait = false;
                    var url = elm.data('endpoint');//.replace(':id', rowdata.id);
                    var div = $("<div id='layout-manger-popup' title='Layout Manager'></div>");
                    var compiledTemplate = _.template(layoutTemp);

                    var row = $(layoutTemp).find('#row-data-template').html();
                    $('body').data('rowInfo', row);
                    var pageId = false;

                    if (_.has(rowdata, 'pageId')) {
                        var options = $('select[name="pageId"] option');
                        var option = options.filter(
                            function () {
                                var opt = $(this);
                                var txt = $.trim(opt.text());
                                return (txt == rowdata['pageId']);
                            }
                        );
                        pageId = option.val();
                    }

                    div.fancybox({
                        type: 'ajax',
                        href: url + '/' + rowdata.id,
                        padding: 10,
                        maxHeight: '90%',
                        openEffect: 'elastic',
                        modal: true,
                        ajax: {
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                pageId: pageId
                            }
                        },
                        afterLoad: function () {
                            //div.data('page-details', this.content);
                            pageBuilder['pageDetails'] = this.content;
                            if (this.content.error == true) {
                                this.title = 'An Error Occurred';
                                this.content = this.content.message;
                            } else {
                                this.title = this.content.title;
                                this.content.pageBuilder = pageBuilder;
                                this.content = compiledTemplate(this.content);
                            }
                        },
                        beforeShow: function () {

                            var details = pageBuilder['pageDetails'];//div.data('page-details');
                            var sections = $('.section', pageBuilder.canvasClass);

                            pageBuilder.initPageLayout(details.page.layout, details['assets'], details['widgets']);

                            $('.accordion').accordion({
                                heightStyle: 'content',
                                collapsible: true,
                                active: 1
                            });

                            $('.action-btn', pageBuilder.actionToolbar).on('click', function () {
                                var me = $(this);
                                var attr = me.data('type');
                                var color = me.data('color');
                                var sel = $('.ui-selected');

                                if (sel.length > 0) {
                                    if (!me.hasClass('custom-btn')) {
                                        pageBuilder.addRowItem(attr, sel);
                                        $.fancybox.update();
                                    } else {
                                        var opt = {
                                            min: 1,
                                            max: 11

                                        };

                                        var txt = $('#span-template').html();
                                        $('.custom-btn').popover(
                                            {
                                                placement: 'top',
                                                title: 'Custom Rows',
                                                content: txt,
                                                html: true,
                                                trigger: 'manual'
                                            }
                                        ).popover('show');

                                        $('input', '#custom-selector').spinner(opt);
                                        $('#add', '#custom-selector').on('click', function () {
                                            var ac = $('<label><input type="text" name="span[]" value="1"/>');
                                            var pop = $('.popover.in');
                                            $('.btn-group', '#custom-selector').before(ac);
                                            $('input', ac).spinner(opt);
                                            var t = pop.css('top').replace('px', '') - 30;
                                            pop.css('top', t + 'px');

                                            return false;
                                        });

                                        $('#cancel', '#custom-selector').on('click', function () {
                                            $('.custom-btn').popover('hide');
                                            return false;
                                        });

                                        $('#ok', '#custom-selector').on('click', function () {
                                            var data = pageBuilder.checkSum();
                                            if (data === false) {
                                                msg = "Row columns must add up to 12";
                                                pageBuilder.notify('Layout Manager', msg, 'error', true);
                                            } else {
                                                pageBuilder.addRowItem(attr, sel, null, data);
                                                $('.custom-btn').popover('hide');
                                                $.fancybox.update();
                                            }
                                            return false;
                                        });
                                    }

                                } else {
                                    var msg = 'No section was selected. Please select a section e.g. Header by clicking the section to add a row';
                                    pageBuilder.notify('Layout Manager', msg, 'error');
                                }
                            });

                            $(pageBuilder.canvasClass).selectable(
                                {
                                    filter: ".section",
                                    cancel: ".handle,btn,li,.badge-edit,.template-item"
                                }
                            );

                            sections.sortable(
                                {
                                    containment: pageBuilder.canvasClass,
                                    connectWith: '.section',
                                    handle: '.move-row',
                                    update: function () {
                                        $.fancybox.update();
                                    }
                                }
                            );

                            sections.each(function () {
                                var me = $(this);
                                var cn = $('.badge-edit', me);
                                var trg = $('.attr-trigger', cn);
                                var title = $('.status', me).text() + ' Attributes';
                                pageBuilder.initPopover(me, trg, title);
                            });

                            $(pageBuilder.canvasClass).on("load click", ".row , .remove-row, .move-row, .item-delete",
                                function (e) {
                                    if (confirm('You about about to delete this item. Click OK to continue')) {
                                        var elm = $(e.target);
                                        if (e.type == 'click') {
                                            e.preventDefault();
                                            if (elm.hasClass('item-delete')) {
                                                var li = elm.closest('li');
                                                var ul = li.parent();
                                                li.remove();

                                                pageBuilder.processAssets(ul);
                                            } else if (elm.hasClass('remove-row')) {
                                                e.stopPropagation();
                                                elm.closest('.' + pageBuilder.defaultRowClass).remove();
                                                pageBuilder.updateSectionCount();
                                            }

                                        }
                                        else if (e.type == 'load') {
                                            if (elm.hasClass(pageBuilder.defaultRowClass)) {
                                                elm.sortable();
                                            }
                                        }
                                    } else {
                                        return false;
                                    }
                                }
                            );

                            $(".popup .label", '.page-details').on('click', function (e) {
                                e.preventDefault();
                                var popup = $(this).parent('.popup');
                                if (popup.hasClass('active')) {
                                    popup.removeClass('active');
                                } else {
                                    popup.siblings().removeClass('active');
                                    popup.addClass('active');
                                }

                                return false;
                            });

                            var assetList = $('.asset-list').not('#templates');
                            assetList.sortable(
                                {
                                    connectWith: '.page-assets',
                                    helper: 'clone',
                                    revert: true,
                                    items: 'li.asset',
                                    tolerance: "pointer",
                                    stop: function () {
                                        var bdy = $('body');
                                        var data = bdy.data('temp-sort');
                                        var ok = bdy.data('sort-ok');

                                        if (data && ok === true) {
                                            if (data.prev === false) {
                                                $(data.parent).prepend(data.clone);
                                            } else {
                                                $(data.prev).after(data.clone);
                                            }
                                            data.clone.show();
                                        }
                                        bdy.data('temp-sort', false);
                                        assetList.sortable("refresh");
                                    },
                                    start: function (e, ui) {
                                        //store details to rebuild the list if sort was ok
                                        var bdy = $('body');
                                        var prev = ui.item.prev();
                                        if (!prev.length) {
                                            prev = false;
                                        }
                                        var param = {
                                            prev: prev,
                                            clone: ui.item.clone(),
                                            parent: ui.item.parent()
                                        };
                                        bdy.data('temp-sort', param);
                                        bdy.data('sort-ok', false);
                                    }
                                }
                            );

                            $('#save', pageBuilder.actionToolbar).on('click', function (e) {
                                e.preventDefault();

                                window.synergyEnableWait = false;

                                var elm = $(this);
                                //var asTemplate = !!(elm.attr('id') == 'save-template');
                                var url = elm.data('url');
                                var details = pageBuilder['pageDetails'];//div.data('page-details');

                                var param = {
                                    id: elm.data('page-id'),
                                    themeId: $('#themeId', '.page-details').val(),
                                    //page-theme-id is set in layout-manager.html template
                                    pageTheme: elm.data('page-theme-id'),
                                    layout: pageBuilder.getLayout(details['sections'])
                                };

                                $.ajax(url,
                                    {
                                        type: 'PUT',
                                        data: param,
                                        success: function (data) {
                                            if (data.error) {
                                                pageBuilder.notify('Page Layout', data.message, 'error');
                                            } else {
                                                pageBuilder.notify('Page Layout', data.message);
                                            }
                                        }
                                    }
                                );

                                return false
                            });

                            $('.template-clone, #reset-layout').on('click', function (e) {
                                e.preventDefault();

                                var elm = $(this);
                                var id = elm.data('layout-id');
                                var details = pageBuilder['pageDetails'];//div.data('page-details');

                                if (id == 0) {

                                    if (confirm('You about about to reset this template. Click OK to continue')) {

                                        var save = $('#save', pageBuilder.actionToolbar);
                                        pageBuilder.resetPageLayout();
                                        var url = save.data('url');
                                        var param = {
                                            id: save.data('page-id'),
                                            themeId: save.data('page-theme-id'),
                                            layout: {}
                                        };

                                        $.ajax(url,
                                            {
                                                type: 'PUT',
                                                data: param,
                                                success: function (data) {
                                                    if (data.error) {
                                                        pageBuilder.notify('Page Layout', data.error, 'error');
                                                    } else {
                                                        pageBuilder.notify('Page Layout', 'Layout reset successfully');
                                                    }
                                                }
                                            }
                                        );

                                    } else {
                                        return false;
                                    }
                                } else {
                                    if (confirm('You about about to clone this template. This will overwrite the current layout. Click OK to continue')) {
                                        pageBuilder.initPageLayout(details.templates.items[id].layout, details['assets'], details['widgets']);
                                        pageBuilder.notify('Page layout', 'template cloned successfully');
                                    } else {
                                        return false;
                                    }
                                }

                                pageBuilder.updateSectionCount();
                                $.fancybox.update();

                            });

                            $('#close', pageBuilder.actionToolbar).on('click', function () {
                                $.fancybox.close();
                            });

                            $.fancybox.update();
                        },
                        afterShow: function () {
                            //tree
                            $('.tree', '.page-details').each(function () {
                                    //   $(this).jstree();
                                }
                            );

                            var all = $('.att-trigger');
                            all.popover(
                                {
                                    placement: 'left',
                                    title: 'Tag Attributes',
                                    content: function () {
                                        return  $('#attribute-template').html();
                                    },
                                    html: true,
                                    trigger: 'click'
                                }
                            );

                            var tags = $('.tags', '#attribute-template');
                            if (tags.length) {
                                tags.tagsInput();
                            }
                            var btn = $('.close-attr', '#attribute-template');
                            btn.on('click', function (e) {
                                $(this).closest('.att-trigger').popover('hide');
                                e.preventDefault();
                                return false;
                            });

                            $('label.status').on('click', function () {
                                $(this).siblings('.template-row').slideToggle();
                                $.fancybox.update();
                            });

                            //initialise tooltips
                            /*$('[rel="tooltip"]').tooltip(
                             {
                             delay: {
                             show: 500,
                             hide: 50
                             }
                             }
                             );*/
                        },
                        autoCenter: true,
                        autoResize: true,
                        autoSize: true,
                        autoScale: true

                    }).trigger('click');

                }
            },
            resetPageLayout: function () {
                var sects = $('[id$="-section"]', '.template-canvas');

                sects.each(function () {
                    $(this).children('.' + pageBuilder.defaultRowClass).remove();
                });
            },
            updateSectionCount: function () {
                var sections = $('.section', '.template-canvas');
                var cnt = 0;
                for (var i = 0; i < sections.length; i++) {
                    var s = $(sections[i]);
                    var bg = $('.badge', s);
                    cnt = $('li.asset', s).not('.ui-state-disabled').length;
                    bg.text(cnt);

                    if (cnt < 1) {
                        bg.attr('class', 'badge badge-important');
                    } else {
                        bg.attr('class', 'badge badge-info');
                    }
                }
            },
            notify: function notify(title, text, type, hide) {
                type = type || 'info';
                hide = hide || (type == 'error') ? false : true;

                $.pnotify({
                    title: title,
                    text: text,
                    styling: 'bootstrap',
                    hide: hide,
                    opacity: .9,
                    type: type,
                    nonblock_opacity: .9
                });
            },
            initPageLayout: function (details, assets, widgets) {

                pageBuilder.resetPageLayout();
                var itemsInSection;

                for (var sec in details) {
                    if (details.hasOwnProperty(sec)) {
                        var section = $('#' + sec + '-section');
                        if (section) {
                            itemsInSection = 0;
                            var data = details[sec].items || [];
                            var len = data.length;
                            if (_.has(details[sec], pageBuilder.attributeKey)) {
                                section.data(pageBuilder.attributeKey, details[sec][pageBuilder.attributeKey]);
                            }
                            for (var i = 0; i < len; i++) {
                                var rowAttr = _.has(data[i], pageBuilder.attributeKey) ? data[i][pageBuilder.attributeKey] : {};
                                var row = pageBuilder.addRowItem(data[i].rowType, section, rowAttr);
                                if (data[i].rowItems) {
                                    var cols = row.find('[class^="span"]');
                                    for (var k = 0; k < cols.length; k++) {
                                        if (data[i].rowItems[k]) {
                                            var ul = $(cols[k]).find('ul');
                                            if (data[i].rowItems[k].item) {
                                                for (var x = 0; x < data[i].rowItems[k].item.length; x++) {
                                                    var li = $('<li></li>');
                                                    var asset = data[i].rowItems[k].item[x];
                                                    var parts = [];
                                                    var desc = '';
                                                    var title = '';

                                                    if (_.has(asset, 'name')) {
                                                        parts = asset.name.split('-');
                                                        title = asset.name;
                                                    } else if (_.isString(asset)) {
                                                        parts = asset.split('-');
                                                    } else {
                                                        asset['name'] = 'undefined';
                                                    }

                                                    if (parts[0] == widgets.id) {
                                                        if (!_.isUndefined(widgets['items'][parts[1]])) {
                                                            title = widgets['items'][parts[1]].title;
                                                            desc = widgets['items'][parts[1]].description;
                                                        } else {
                                                            title = parts[1];
                                                        }
                                                    } else if (!(_.isUndefined(assets[parts[0]]) ||
                                                        _.isUndefined(assets[parts[0]]['items'][parts[1]]) )) {
                                                        title = assets[parts[0]]['items'][parts[1]].title;
                                                        desc = assets[parts[0]]['items'][parts[1]].description;
                                                    } else {
                                                        title = parts[1];
                                                    }

                                                    var itemAttr = _.has(asset, pageBuilder.attributeKey) ? asset[pageBuilder.attributeKey] : {};
                                                    li.text(title)
                                                        .addClass('asset')
                                                        .attr('id', asset['name'])
                                                        .attr('title', desc)
                                                        .data(pageBuilder.attributeKey, itemAttr)
                                                    ;

                                                    ul.append(li);
                                                }
                                            }
                                            pageBuilder.processAssets(ul);
                                        }
                                    }
                                }
                            }

                        }
                    }
                }
            }
        }
        return pageBuilder;
    }
);