@extends('admin.layouts.adminlayout')
@section('content')
    <div id="content" class="content">
        <!-- begin breadcrumb -->
        <ol class="breadcrumb pull-right">
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">Order</a></li>
            <li class="active">Manage Order</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Manage Order</h1>

        <!-- end page-header -->


        <!-- begin row -->
        <div class="row">
            <div class="col-md-12">
                <!-- begin panel -->
                <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
                    <div class="panel-heading">
                        <h4 class="panel-title">Search Date Between</h4>
                    </div>
                    <div class="panel-body">
                        <form  id="warehouse_form" class="form-inline" action="{{route('admin::manageDeliveryBoyReport')}}" method="get" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <div class="controls">
                                            <input type="text" class="form-control datepicker" name="start_date" value="{{$start_date}}" id="datepicker-1" placeholder="Auto Close Datepicker" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <div class="controls">
                                            <input type="text" class="form-control datepicker" name="end_date" value="{{$end_date}}" id="datepicker-1" placeholder="Auto Close Datepicker" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                                <!--div class="col-md-2">
                                    <div class="form-group">
                                        <label class="col-md-4">Supplier</label>
                                        <div class="controls">
                                            <select class="form-control" id="supplier" name="supplier_id"  data-parsley-required="true" >
                                                <option value="">--All--</option>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{$supplier->id}}" <?php if($supplier_id==$supplier->id){ echo 'selected';}?>>{{$supplier->user_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div -->
                                <div class="col-md-2" style="padding-top: 25px">
                                    <div class="form-group">
                                        <div class="controls">
                                            <button id="submit-btn" type="submit" class="btn btn-sm btn-success">Search</button>
                                        </div>
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
                        <h4 class="panel-title">Payment Details</h4>
                    </div>
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>{{Session::get('success')}}</strong>
                        </div>
                    @endif
                    <div></div>
                    <div class="panel-body">
                        <div class="table-responsive">
                        <table id="Datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Sl No</th>
                                <th>Date</th>
                                <th>Cash</th>
                                <th>Action</th>
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
                ajax: '{{route('admin::manageDeliveryBoyReport')}}'+'?start_date='+'{{$start_date}}'+'&end_date='+'{{$end_date}}'+'&supplier_id='+'{{$supplier_id}}',
                columns: [
                    {data: 'id', name: 'id', 'visible': false},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,searchable: false},
                    {data: 'date', name: 'date'},
                    {data: 'cash', name: 'cash'},
                    {data: 'action', name: 'action', orderable: false, searchable: true},

                ],
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Delivery Boy Report',
                        exportOptions: {
                            columns: [ 1, 2, 3]
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'Delivery Boy Report',
                        exportOptions: {
                            columns: [1, 2, 3]
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Delivery Boy Report',
                        exportOptions: {
                            columns: [ 1, 2, 3]
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
                    var api = this.api();
                    $( api.table().footer() ).html(
                        '<tr><td colspan="1"><td>Total</td>' +
                        '<td>'+
                        api.column( 3, {page:'current'} ).data().sum()
                        +'</td><td></td><tr>'
                    );
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
    @endpush
@endsection
