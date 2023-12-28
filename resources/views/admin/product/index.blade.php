@extends('admin.layouts.adminlayout')
@section('content')
    <div id="content" class="content">
        <!-- begin breadcrumb -->
        <ol class="breadcrumb pull-right">
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">Product</a></li>
            <li class="active">Manage Product</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Manage Product</h1>
        <div style="padding: 10px; ">
           <a class="btn btn-xs btn btn-info fancybox fancybox.iframe" style="float: right;margin-bottom: 10px" href="{{route('admin::importImage')}}"><i class="fa fa-plus"></i> Upload Images</a>
            <a class="btn btn-xs btn btn-warning fancybox fancybox.iframe" style="float: right;margin-bottom: 10px" href="{{route('admin::importProduct')}}"><i class="fa fa-plus"></i> Import Excel</a>
        </div>

        <!-- end page-header -->


        <!-- begin row -->
        <div class="row">
            <!-- begin col-12 -->
            <div class="col-md-12">
                <!-- begin panel -->
                <div class="panel panel-inverse">
                    <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
                        </div>
                        <h4 class="panel-title">Product</h4>
                    </div>
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>{{Session::get('success')}}</strong>
                        </div>
                    @endif
                    <div></div>
                    <div style="padding: 10px;">
                        <a class="btn btn-xs btn btn-success fancybox fancybox.iframe" style="float: right;margin-bottom: 10px" href="{{route('admin::addProduct')}}"><i class="fa fa-plus"></i> Add</a>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                        <table id="Datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th><input type="checkbox" id="allSelect"><button type="button" name="bulk_delete" id="bulk_delete" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i></button></th>
                                <th>Id</th>
                                <th>Sl No</th>
                                <th>Category Name</th>
                                <th>Sub Category Name</th>
                                <th>Brand Name</th>
                                <th>Product Name</th>
                                <th>Product Image</th>
                                <th>Unit Name</th>
                                <th>Dept Name</th>
                                <th>Tax(%)</th>
                                <th>Action</th>

                            </tr>
                            </thead>
                            <tbody>
                            </tbody>

                        </table>
                        </div>
                    </div>
                </div>
                <!-- end panel -->
            </div>
            <!-- end col-12 -->
        </div>
        <!-- end row -->
    </div>
    @push('scripts')
        <script type="text/javascript">
            $('#Datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{route('admin::manageProduct')}}',
                columns: [
                    {data:'checkbox',name: 'checkbox', orderable: false, searchable: true},
                    {data: 'id', name: 'id', 'visible': false,searchable: false},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,searchable: false},
                    {data: 'category_id', name: 'category.category_name'},
                    {data: 'sub_category_id', name: 'sub_category.category_name', orderable: false},
                    {data: 'brand_id', name: 'brand.brand_name', orderable: false},
                    {data: 'product_name', name: 'product_name',searchable: true},
                    {data: 'product_image', name: 'product_image',searchable: false,orderable: false},
                    {data: 'unit_id', name: 'unit_id',searchable: false},
                    {data: 'department_id', name: 'department_id',searchable: false},
                    {data: 'tax_id', name: 'tax_id',searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: true},

                ],
                "order": [[0,'desc']],
                "pageLength": 10,
                "fnDrawCallback": function () {
                    init();
                }

            });
            function init() {
                $(document).find('.fancybox').fancybox({
                    helpers: {title: null},
                    width: 800,
                    height: 600,
                    fitToView : true,
                    autoSize : true,
                    padding: 0,
                    openEffect: 'elastic',
                    afterClose : function() {
                        var oTable = $('#Datatable').dataTable();
                        oTable.fnDraw(false);
                    }
                });

                $(".product_checkbox").click(function(){
                    if($(".product_checkbox").length == $(".product_checkbox:checked").length) {
                        $('#allSelect').prop('checked', true);
                    } else {
                        $('#allSelect').prop('checked', false);
                    }
                });
            }
        </script>
        <script>
            var Inactive='Inactive';
            var Active='Active';
            function active_inactive_product(id,status){
                $.ajax({
                    type: "post",
                    url: '{{route('admin::active_inactive_product')}}',
                    data: {
                        _token: '<?php echo csrf_token();?>',
                        id: id,
                        status:status
                    },
                    success: function (data) {
                        var resp=JSON.parse(data);
                        $('#status'+resp.id).html(resp.html);
                        $(document).find('.child #status'+resp.id).html(resp.html);
                    }

                });
            }

            $("#allSelect").click(function () {
                if(this.checked==true) {
                    $('.product_checkbox').prop('checked', true);
                }else{
                    $('.product_checkbox').prop('checked', false);
                }
            });



            $(document).on('click', '#bulk_delete', function(){
                var id = [];
                if(confirm("Are you sure you want to Delete this data?"))
                {
                    $('.product_checkbox:checked').each(function(){
                        id.push($(this).val());
                    });
                    if(id.length > 0)
                    {
                        $.ajax({
                            type: "post",
                            url: '{{route('admin::bulk_product_delete')}}',
                            data: {
                                _token: '<?php echo csrf_token();?>',
                                id: id
                            },
                            success: function (data) {
                                var resp=JSON.parse(data);
                                if(resp.status=='success'){
                                    $('#Datatable').DataTable().ajax.reload();
                                }

                            }

                        });
                    }
                    else
                    {
                        alert("Please select atleast one checkbox");
                    }
                }
            });

        </script>
    @endpush
@endsection
