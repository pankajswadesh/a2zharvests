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
                        <form  id="warehouse_form" class="form-inline" action="{{route('admin::manageDayEndReport')}}" method="get" enctype="multipart/form-data">
                            {{csrf_field()}}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <div class="controls">
                                        <input type="text" class="form-control datepicker" name="start_date" value="{{$start_date}}" id="" placeholder="Auto Close Datepicker" autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <div class="controls">
                                        <input type="text" class="form-control datepicker1" name="end_date" value="{{$end_date}}" id="" placeholder="Auto Close Datepicker" autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
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
                            </div>
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
                        <h4 class="panel-title">Order</h4>
                    </div>
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>{{Session::get('success')}}</strong>
                        </div>
                    @endif
                    <div></div>
                    <div class="panel-body" id="print_data">
                        <div class="row">
                            <div class="col-md-3">
                                <h4>Start Date - {{$start_date}}</h4>
                            </div>
                            <div class="col-md-3">
                                <h4>End Date - {{$end_date}}</h4>
                            </div>
                            <div class="col-md-3">
                                <h4>Supplier - @if(empty($supplier_details)) N/A @else {{$supplier_details["user_name"]}} @endif</h4>
                            </div>
                            <div class="col-md-3">
                                <p><button onclick="printOrder();" class="non-printable btn btn-sm btn-success">Print</button></p>
                            </div>
                        </div>
                        <div class="table-responsive">
                        <table id="Datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Supplier Name</th>
                                <th>Cash</th>
                                <th>Online</th>
                                <th>Wallet</th>
                                <th>Total Amount</th>
                                <th>Total Commission</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=0;?>
                            @forelse($delivered_supplier_id as $row)
                                <?php $i++;?>
                            <tr>
                                <td>{{$i}}</td>
                                <td>
                                    <?php
                                     $supplier_name=\App\User::where('id',$row)->value('user_name');
                                     $vendor_comission=\App\User::where('id',$row)->value('vendor_commision');
                                     echo ucfirst($supplier_name);
                                    if(isset($supplier_data[$row]['cod'])){
                                        $cod=$supplier_data[$row]['cod'];
                                    }else{
                                        $cod=0;
                                    }
                                    if(isset($supplier_data[$row]['online'])){
                                        $online=$supplier_data[$row]['online'];
                                    }else{
                                        $online=0;
                                    }
                                    if(isset($supplier_data[$row]['wallet'])){
                                        $wallet=$supplier_data[$row]['wallet'];
                                    }else{
                                        $wallet=0;
                                    }
                                    ?>
                                </td>
                                <td>
                                    {{$cod}}
                                </td>
                                <td>
                                   {{$online}}
                                </td>
                                <td>
                                    {{$wallet}}
                                </td>
                                <td>
                                   {{$cod + $online + $wallet}}
                                </td>
                                <td>
                                   <?php
                                        $total=$cod + $online + $wallet;
                                        $comission=(($total * $vendor_comission)/100);
                                        echo $comission .' ( '. $vendor_comission.' % Comission.)';

                                        ?>
                                </td>
                            </tr>
                                @empty
                                <tr>
                                    <td colspan="7" style="text-align: center;">
                                        No Record Available
                                    </td>
                                </tr>
                                @endforelse
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
        <script>
            function printOrder() {
                var printContents = document.getElementById('print_data').innerHTML;
                var originalContents = document.body.innerHTML;

                document.body.innerHTML = printContents;

                window.print();

                document.body.innerHTML = originalContents;
            }
        </script>
    @endpush
@endsection
