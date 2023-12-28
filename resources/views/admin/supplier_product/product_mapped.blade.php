@extends('admin.layouts.adminlayout')
@section('content')
    <link rel="stylesheet" href="https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.css" />
    <div id="content" class="content">
        <!-- begin breadcrumb -->
        <ol class="breadcrumb pull-right">
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">Product</a></li>
            <li class="active">Manage Product</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Manage My Product</h1>

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

                    <div class="panel-body">
                        <div class="table-responsive">
                        <table id="Datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th><input type="checkbox" id="allSelect"></th>
                                <th>Id</th>
                                <th>Sl No</th>
                                <th>Category Name</th>
                                <th>Sub Category Name</th>
                                <th>Brand Name</th>
                                <th>Product Name</th>
                                <th>Product Image</th>
                                <th>Unit Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Discount Value</th>
                                <th>Action</th>

                            </tr>
                            </thead>
                            <tbody>
                            </tbody>

                        </table>
                            <button type="button" id="bulk_add" class="btn btn-success">Update</button>
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
        <script src="https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.js"></script>

        <script type="text/javascript">
            $('#Datatable').DataTable({
                processing: true,
                serverSide: true,
                "ajax": {
                    "url": "{{route('admin::manageMyProduct')}}",
                    "type": "get",
                },
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
                    {data: 'quantity', name: 'quantity',searchable: false},
                    {data: 'price', name: 'price',searchable: false},
                    {data: 'discount', name: 'discount',searchable: false},
                    {data: 'discount_value', name: 'discount_value',searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: true},


                ],
                "order": [[1,'desc']],
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
            function active_inactive_my_product(id,status){
                $.ajax({
                    type: "post",
                    url: '{{route('admin::active_inactive_my_product')}}',
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



            $(document).on('click', '#bulk_add', function(){
                var id = [];
                var err=0;
                var quantity=[];
                var price=[];
                var discount=[];
                var discount_value=[];
                swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this imaginary file!",
                        type: "success",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes, Update it!",
                        cancelButtonText: "No, cancel plx!",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                    $('.product_checkbox:checked').each(function(){
                        id.push($(this).val());
                    });
                   for(var i=0;i<id.length;i++){
                       var qty =$('input[name="quantity['+id[i]+']"]').val();
                       var amount =$('input[name="price['+id[i]+']"]').val();
                       var dis =$('select[name="discount['+id[i]+']"]').val();
                       var dis_val =$('input[name="discount_value['+id[i]+']"]').val();
                      if(qty=='' || amount=='' || dis=='' || dis_val==''){
                          err++;
                      }else{
                          quantity.push(qty);
                          price.push(amount);
                          discount.push(dis);
                          discount_value.push(dis_val);
                      }
                   }
                    if(id.length > 0)
                    {
                        if(err == 0) {
                            $.ajax({
                                type: "post",
                                url: '{{route('admin::bulk_product_update')}}',
                                data: {
                                    _token: '<?php echo csrf_token();?>',
                                    id: id,
                                    quantity: quantity,
                                    price: price,
                                    discount: discount,
                                    discount_value: discount_value,
                                },
                                success: function (data) {
                                    var resp = JSON.parse(data);
                                    if (resp.status == 'success') {
                                        swal("Updated!", "Product Updated Successfully.", "success");
                                        $('#Datatable').DataTable().ajax.reload();
                                    }

                                }

                            });
                        }else{

                            swal("Cancelled", "Please fill all fields. :)", "error");
                        }
                    }
                    else
                    {
                        swal("Cancelled", "Please select atleast one checkbox :)", "error");
                    }
                        } else {
                            swal("Cancelled", "Your imaginary file is safe :)", "error");
                        }
                    });

            });

        </script>
    @endpush
@endsection
