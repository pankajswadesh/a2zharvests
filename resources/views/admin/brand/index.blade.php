@extends('admin.layouts.adminlayout')
@section('content')
    <div id="content" class="content">
        <!-- begin breadcrumb -->
        <ol class="breadcrumb pull-right">
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">Brand</a></li>
            <li class="active">Manage Brand</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Manage Brand</h1>
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
                        <h4 class="panel-title">Brand</h4>
                    </div>
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>{{Session::get('success')}}</strong>
                        </div>
                    @endif
                    <div></div>
                    <div style="padding: 10px;"><a class="btn btn-xs btn btn-success fancybox fancybox.iframe" style="float: right;margin-bottom: 10px" href="{{route('admin::addBrand')}}"><i class="fa fa-plus"></i> Add</a></div>
                    <div class="panel-body">
                        <div class="table-responsive">
                        <table id="Datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Sl No</th>
                                <th>Brand Name</th>
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
                ajax: '{{route('admin::manageBrand')}}',
                columns: [
                    {data: 'id', name: 'id', 'visible': false},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,searchable: false},
                    {data: 'brand_name', name: 'brand_name'},
                    {data: 'action', name: 'action', orderable: false, searchable: true}
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
            }
        </script>
        <script>
            var Inactive='Inactive';
            var Active='Active';
            function active_inactive_brand(id,status){
                $.ajax({
                    type: "post",
                    url: '{{route('admin::active_inactive_brand')}}',
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

        </script>
    @endpush
@endsection