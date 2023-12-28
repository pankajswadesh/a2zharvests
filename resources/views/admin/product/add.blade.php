@extends('admin.layouts.fancybox')
@section('content')
    <div id="content" class="content">

        <!-- begin page-header -->
        <h1 class="page-header">Add Product</h1>
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
                        <form class="form-horizontal" id="product" action="{{route('admin::saveProduct')}}" method="post" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label class="col-md-4">Category</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="category_id" data-parsley-required="true" id="category_id">
                                        <option value="">--Select--</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}" @if(old('category_id')==$category->id) selected @endif>&emsp;{{ucwords($category->category_name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4">Sub Category</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="subcategory" data-parsley-required="true" id="subcategory">
                                        <option value="">Select Subcategory</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4">Brand</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="brand_id" data-parsley-required="true">
                                        @foreach($brands as $brand)
                                            <option value="{{$brand->id}}" @if(old('brand_id')==$brand->id) selected @endif>&emsp;{{ucwords($brand->brand_name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Product Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="product_name" value="{{old('product_name')}}" placeholder="Enter Product Name" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Print Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="print_name" value="{{old('print_name')}}" placeholder="Enter Print Name" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Product Image</label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control" name="image" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Product Description</label>
                                <div class="col-md-6">
                                    <textarea class="summernote" id="product_description" name="product_description" placeholder="Enter Your Product Description">{!! old('product_description') !!}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Product Company</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="product_company" value="{{old('product_company')}}" placeholder="Enter Product Company" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4">Unit</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="unit_id" data-parsley-required="true">
                                        @foreach($units as $unit)
                                            <option value="{{$unit->id}}" @if(old('unit_id')==$unit->id) selected @endif>&emsp;{{ucwords($unit->unit_name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4">Department</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="department_id" data-parsley-required="true">
                                        @foreach($depts as $dept)
                                            <option value="{{$dept->id}}" @if(old('department_id')==$dept->id) selected @endif>&emsp;{{ucwords($dept->dept_name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4">Tax</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="tax_id" data-parsley-required="true">
                                        @foreach($taxs as $tax)
                                            <option value="{{$tax->id}}" @if(old('tax_id')==$tax->id) selected @endif>&emsp;{{ucwords($tax->tax_name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" style="text-align: center">
                                    <button type="submit" class="btn btn-sm btn-success">Submit</button>
                                    <button type="button" onclick="resetForm();" class="btn btn-sm btn-danger">Reset</button>
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
            $( document ).ready(function() {
                var old_category_id = '{{old('category_id')}}';
                var old_sub_category_id = '{{old('subcategory')}}';
                $.ajax({
                    url: '{{route('admin::get_old_sub_category')}}',
                    type: 'POST',
                    data: {_token:'<?php echo csrf_token()?>', id: old_category_id,old_sub_category_id:old_sub_category_id},
                    success: function (data) {
                        console.log(data);
                        $('#subcategory').html(data);
                    }
                });

            });
            function resetForm() {
                var sel = $("#product");
                sel.find("input,select").val("");
                $('#product_description').summernote("reset");
            }
        </script>
    @endpush
@endsection
