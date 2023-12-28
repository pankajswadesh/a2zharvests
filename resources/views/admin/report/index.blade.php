@extends('admin.layouts.adminlayout')
@section('content')
    <div id="content" class="content">
        <!-- begin breadcrumb -->
        <ol class="breadcrumb pull-right">
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">Report</a></li>
            <li class="active">Manage Supplier Products Report</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Manage Supplier Products Report</h1>
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
                        <form  id="warehouse_form" class="form-inline" action="{{route('admin::manageSupplierProduct')}}" method="get" enctype="multipart/form-data">
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
                        <h4 class="panel-title">Products &emsp;( Total - {{$product_count}} )</h4>
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
                                <th>Category Name</th>
                                <th>Sub Category Name</th>
                                {{--<th>Brand Name</th>--}}
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Gross Price</th>
                                <th>Discount Type</th>
                                <th>Discount</th>
                                <th>Unit Name</th>
                                <th>Tax Name</th>
                                <th>Status</th>
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
                ajax: '{{route('admin::manageSupplierProduct')}}'+'?supplier_id='+'{{$supplier_id}}',
                columns: [
                    {data: 'id', name: 'id', 'visible': false},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,searchable: false},
                    {data: 'category_name', name: 'products.category_id'},
                    {data: 'sub_category_name', name: 'products.sub_category_id'},
//                    {data: 'brand_name', name: 'product_id'},
                    {data: 'product_name', name: 'products.product_name'},
                    {data: 'product_quantity', name: 'supplier_products.quantity'},
                    {data: 'product_price', name: 'supplier_products.price'},
                    {data: 'gross_price', name: 'gross_price'},
                    {data: 'discount_type', name: 'discount_type'},
                    {data: 'discount', name: 'discount'},
                    {data: 'unit_name', name: 'unit_name'},
                    {data: 'tax_name', name: 'tax_name'},
                    {data: 'status', name: 'supplier_products.status'},


                ],
                "order": [[0,'desc']],
                "pageLength": 10,
                "fnDrawCallback": function () {
                  //  init();
                }
            });
        </script>
    @endpush
@endsection
