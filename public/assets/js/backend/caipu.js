define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'caipu/index' + location.search,
                    add_url: 'caipu/add',
                    edit_url: 'caipu/edit',
                    del_url: 'caipu/del',
                    multi_url: 'caipu/multi',
                    import_url: 'caipu/import',
                    table: 'caipu',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'nickname', title: __('Nickname'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'avatar', title: __('Avatar'), operate: 'LIKE', events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'content', title: __('Content')},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'reason', title: __('Reason'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {
                            field: 'buttons',
                            width: "120px",
                            title: __('审核'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    title: __('通过'),
                                    text: __('通过'),
                                    classname: 'btn btn-xs btn-info btn-magic btn-ajax',
                                    icon: 'fa fa-check',
                                    confirm: '确认通过审核？',
                                    url: 'caipu/detail',
                                    success: function (data, ret) {
                                        console.log(ret)
                                        console.log(data)
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                        table.bootstrapTable('refresh');
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    },
                                    visible: function (row) {
                                        //返回true时按钮显示,返回false隐藏
                                        return row.status < 1;

                                    }
                                },
                                {
                                    name: 'detail',
                                    text: __('拒绝'),
                                    title: __('拒绝'),
                                    classname: 'btn btn-xs btn-danger btn-dialog',
                                    icon: 'fa fa-close',
                                    url: 'caipu/detail',
                                    callback: function (data) {
                                        console.log(data);

                                        $.ajax({
                                            url: "caipu/refuse",
                                            type: "POST",
                                            dataType: "json",
                                            data: data,
                                            success: function (ret) {
                                                //
                                                console.log(ret)
                                                Toastr.success(ret.msg)
                                                table.bootstrapTable('refresh');
                                            },
                                            error: function (xhr) {
                                                //
                                                Toastr.error(ret.msg)
                                            }
                                        });

                                    },
                                    visible: function (row) {
                                        //返回true时按钮显示,返回false隐藏
                                        return row.status < 1;
                                    }
                                }
                            ],
                            formatter: Table.api.formatter.buttons
                        },
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        detail: function () {
            $(document).on('click', '.btn-callback', function () {
                Fast.api.close({
                    ids: $("input[name=ids]").val(),
                    reason: $("input[name=reason]").val()
                });
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
