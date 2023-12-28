@extends('admin.layouts.adminlayout')
@section('content')
    <div id="content" class="content">
        <!-- begin breadcrumb -->
        <ol class="breadcrumb pull-right">
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">Supplier Earning</a></li>
            <li class="active">Manage Supplier Earning</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Manage Supplier Earning</h1>
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
                        <form  id="warehouse_form" class="form-inline" action="{{route('admin::manageSupplierEarning')}}" method="get" enctype="multipart/form-data">
                            {{csrf_field()}}
                            @if(\Illuminate\Support\Facades\Auth::user()->hasRole('admin'))
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
                            @endif
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-md-3" style="text-align:center;">From Date</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control datepicker" name="start_date" value="{{$start_date}}" autocomplete="off" placeholder="From Date" />
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-4">

                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-md-3"  style="text-align:center;">To Date</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control datepicker"  name="end_date" value="{{$end_date}}" autocomplete="off" placeholder="To Date" />
                                        </div>
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
                        </div>
                        <h4 class="panel-title">Supplier Earning</h4>
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
                                @if(\Illuminate\Support\Facades\Auth::user()->hasRole('admin'))
                                <th>Supplier Name</th>
                                @endif
                                <th>Total Price</th>
                                <th>Total Commision</th>
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
                ajax: '{{route('admin::manageSupplierEarning')}}'+'?start_date='+'{{$start_date}}'+'&end_date='+'{{$end_date}}'+'&supplier_id='+'{{$supplier_id}}',
                columns: [
                    {data: 'id', name: 'id', 'visible': false},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,searchable: false},
                    {data: 'order_id', name: 'order_id'},
                    @if(\Illuminate\Support\Facades\Auth::user()->hasRole('admin'))
                    {data: 'supplier_name', name: 'supplier_name'},
                    @endif
                    {data: 'total_price', name: 'total_price'},
                    {data: 'total_commision', name: 'total_commision'},
                ],
                "order": [[0,'desc']],
                "pageLength": 10,
                "fnDrawCallback": function () {
                        var api = this.api();
                    @if(\Illuminate\Support\Facades\Auth::user()->hasRole('admin'))
                        $( api.table().footer() ).html(
                            '<tr><td colspan="3"></td><td>Total</td>' +
                            '<td>'+
                            api.column( 5, {page:'current'} ).data().sum()
                            +''
                        );
                        @else
                        $( api.table().footer() ).html(
                            '<tr><td colspan="2"></td><td>Total</td>' +
                            '<td>'+
                            api.column( 4, {page:'current'} ).data().sum()
                            +''
                        );
                                @endif
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

    @endpush
@endsection
