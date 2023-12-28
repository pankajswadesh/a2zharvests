@extends('admin.layouts.adminlayout')
@section('content')
    <div id="content" class="content">
        <!-- begin breadcrumb -->
        <ol class="breadcrumb pull-right">
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">Report</a></li>
            <li class="active">Manage Supplier Sale Report</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Manage Supplier Sale Report</h1>
        <!-- end page-header -->


        <!-- begin row -->
        <div class="row">
            <div class="col-md-12">
                <!-- begin panel -->
                <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
                    <div class="panel-heading">
                        <h4 class="panel-title">Suppliers&emsp;( Total - {{count($suppliers)}} )</h4>
                    </div>
                    <div class="panel-body">
                        <form  id="warehouse_form" class="form-inline" action="{{route('admin::manageRejectOrder')}}" method="get" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-md-4">Select Supplier's</label>
                                    <div class="col-md-8">
                                        <select class="form-control" name="supplier_id" data-parsley-required="true" id="supplier_id">
                                            <option value="">-- All --</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{$supplier->id}}" <?php if($supplier_id==$supplier->id){ echo 'selected';}?>>{{$supplier->user_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="controls">
                                        <button id="submit-btn" type="submit" class="btn btn-sm btn-success">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- end panel -->
            </div>
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
                        <h4 class="panel-title">Orders List</h4>
                    </div>
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>{{Session::get('success')}}</strong>
                        </div>
                    @endif
                    <div class="panel-body">
                        <div class="table-responsive">
                        <table id="Datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Sl No</th>
                                <th>Order Id</th>
                                <th>User Name</th>

                                <th>Amount</th>

                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            <tr>

                            </tr>
                            </tfoot>
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
        <script src="https://cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>
        <script type="text/javascript">
            $('#Datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{route('admin::manageRejectOrder')}}'+'?supplier_id='+'{{$supplier_id}}',
                columns: [
                    {data: 'id', name: 'id', 'visible': false},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,searchable: false},
                    {data: 'order_id', name: 'order_id'},
                    {data: 'user_name', name: 'user_name'},

                    {data: 'amount', name: 'amount'},
                ],
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Rejected Orders',
                        exportOptions: {
                            columns: [ 1, 2, 3, 4, 5 ,6 ,7 ,8 ,9]
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'Rejected Orders',
                        exportOptions: {
                            columns: [ 0, 1, 3, 4, 5 ,6 ,7 ,8 ]
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Rejected Orders',
                        exportOptions: {
                            columns: [ 1, 2, 3, 4, 5 ,6 ,7 ,8 ,9]
                        }
                    }
                ],
                lengthMenu: [
                    [ 10, 25, 100, -1 ],
                    [ '10 rows', '25 rows', '100 rows', 'Show all' ]
                ],
                "order": [[0,'desc']],
                "pageLength": 10,
                "fnDrawCallback": function () {
//                    var api = this.api();
//                    $( api.table().footer() ).html(
//                        '<tr><td colspan="6"></td><td>Total</td>' +
//                        '<td>'+
//                        api.column( 8, {page:'current'} ).data().sum()
//                        +'<td><td colspan="3"></td><tr>'
//                    );
                  //  init();
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
    @endpush
@endsection
