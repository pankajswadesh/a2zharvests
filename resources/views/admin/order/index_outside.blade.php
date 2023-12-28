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
                        <form  id="warehouse_form" class="form-inline" action="{{route('admin::manageOutsideOrder')}}" method="get" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-md-3" style="text-align:center;">From Date</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control datepicker" name="start_date" value="{{$start_date}}" autocomplete="off" placeholder="Auto Close Datepicker" />
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-4">

                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-md-3"  style="text-align:center;">To Date</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control datepicker"  name="end_date" value="{{$end_date}}" autocomplete="off" placeholder="Auto Close Datepicker" />
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
                        <h4 class="panel-title">Order</h4>
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
                                <th>Order Id</th>
                                <th>Transaction Id</th>
                                <th>Gross Amount</th>
                                <th>Order Date</th>
                                <th>Order status</th>
                                <th>Payment Method</th>
                                <th>Payment Status</th>
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
                ajax: '{{route('admin::manageOutsideOrder')}}'+'?start_date='+'{{$start_date}}'+'&end_date='+'{{$end_date}}',
                columns: [
                    {data: 'id', name: 'id', 'visible': false},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,searchable: false},
                    {data: 'order_id', name: 'order_id'},
                    {data: 'transaction_id', name: 'transaction_id'},
                    {data: 'gross_amount', name: 'gross_amount'},
                    {data: 'datetime', name: 'datetime'},
                    {data: 'status', name: 'status'},
                    {data: 'payment_method', name: 'payment_method'},
                    {data: 'payment_status', name: 'payment_status'},
                    {data: 'action', name: 'action', orderable: false, searchable: true},

                ],
                "order": [[0,'desc']],
                "pageLength": 10,
                "fnDrawCallback": function () {
                    var api = this.api();
                    var total = api.column( 4, {page:'current'} ).data().sum();
                    var total_sum = total.toFixed(2);
                    $( api.table().footer() ).html(
                        '<tr><td colspan="2"></td><td>Total</td>' +
                        '<td>'+total_sum +'<td><td colspan="6"></td><tr>'
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
