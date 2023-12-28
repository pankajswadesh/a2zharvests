@extends('admin.layouts.fancybox')
@section('content')
    <div id="content" class="content">

        <!-- begin page-header -->
        <h1 class="page-header">Edit Product</h1>
        <!-- end page-header -->

        <!-- begin row -->
        <div class="row">
            <!-- begin col-6 -->
            <div class="col-md-12">
                <!-- begin panel -->
                <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
                    <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
                        </div>
                        <h4 class="panel-title">Product</h4>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div><br />
                    @endif
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>{{Session::get('success')}}</strong>
                        </div>
                    @endif
                    @if(Session::has('error'))
                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>{{Session::get('error')}}</strong>
                        </div>
                    @endif
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{route('admin::updateProduct')}}" method="post" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <input type="hidden" name="id" value="{{$productById->id}}"/>
                            <div class="form-group">
                                <label class="col-md-4">Category</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="category_id" data-parsley-required="true" id="category_id">
                                        <option value="">--Select--</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}" {{($productById->category_id == $category->id ? "selected":"")}}>&emsp;{{ucwords($category->category_name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4">SubCategory</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="subcategory" data-parsley-required="true" id="subcategory">
                                        <option value="">--- Select subcategory ---</option>
                                        @foreach($subcategories as $sub)
                                            <option value="{{$sub->id}}"  <?php if ($sub->id == $productById->sub_category_id){echo 'selected';} ?>>{{$sub->category_name}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4">Brand</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="brand_id" data-parsley-required="true">
                                        @foreach($brands as $brand)
                                            <option value="{{$brand->id}}" {{($productById->brand_id == $brand->id ? "selected":"")}}>&emsp;{{ucwords($brand->brand_name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Product Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="product_name" placeholder="Enter Product Name" value="{{$productById->product_name}}" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Print Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="print_name" placeholder="Enter Print Name" value="{{$productById->print_name}}" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Product Image</label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control" name="image" />
                                </div>
                            </div>
                            <div class="col-md-3" style="margin-top: 10px">
                                <img src="{{$productById->product_image}}" class="img-responsive" style="height: 100px; width: 200px;"/>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Product Description</label>
                                <div class="col-md-6">
                                    <textarea class="summernote" name="product_description" placeholder="Enter Your Product Description">{{$productById->product_description}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Product Company</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="product_company" placeholder="Enter Product Company"  value="{{$productById->product_company}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4">Unit</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="unit_id" data-parsley-required="true" >
                                        @foreach($units as $unit)
                                            <option value="{{$unit->id}}" {{$productById->unit_id == $unit->id  ? "selected":""}}>&emsp;{{ucwords($unit->unit_name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4">Department</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="department_id" data-parsley-required="true">
                                        @foreach($depts as $dept)
                                            <option value="{{$dept->id}}"{{$productById->department_id == $dept->id ? "selected" : ""}}>&emsp;{{ucwords($dept->dept_name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4">Tax</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="tax_id" data-parsley-required="true">
                                        @foreach($taxs as $tax)
                                            <option value="{{$tax->id}}" {{$productById->tax_id == $tax->id ? "selected": ""}}>&emsp;{{ucwords($tax->tax_name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" style="text-align: center">
                                    <button type="submit" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- end panel -->
            </div>
            <!-- end col-6 -->
        </div>
    </div>
    @push('scripts')
        <script>
            $('#category_id').on('change', function () {
                var category_id = $('#category_id').val();

                $.ajax({
                    url: '{{route('admin::get_sub_category')}}',
                    type: 'POST',
                    data: {_token:'<?php echo csrf_token()?>', id: category_id},
                    success: function (data) {
                        console.log(data);
                        $('#subcategory').html(data);
                    }
                });
            });
        </script>
    @endpush
@endsection
