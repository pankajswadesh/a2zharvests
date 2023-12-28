@extends('admin.layouts.fancybox')
@section('content')
    <div id="content" class="content">
        <!-- begin breadcrumb -->
        <ol class="breadcrumb pull-right">
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">Supplier</a></li>
            <li class="active">Supplier Details</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Supplier Details</h1>
        <!-- end page-header -->
        <!-- begin row -->
        <div class="row">
            <!-- begin col-12 -->
            <div class="col-md-6">
                <!-- begin panel -->
                <div class="panel panel-inverse">
                    <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
                        </div>
                        <h4 class="panel-title">Shop Details - {{$shopDetails->user->user_name}}</h4>
                    </div>
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>{{Session::get('success')}}</strong>
                        </div>
                    @endif
                    <div></div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">business Name:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email"><?php if ($shopDetails->business_name == null){echo  'Not Updated';}else{
                                    $business_name = $shopDetails->business_name;
                                    echo $business_name;
                                   } ?></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">Business Id:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email"><?php if ($shopDetails->business_id == null){echo 'Not Updated';}else{
                                    $business_id = $shopDetails->business_id;
                                    echo $business_id;
                                    } ?></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">GST No:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email"><?php if ($shopDetails->gst_no == null){echo 'Not Updated';}else{
                                    $gst_no = $shopDetails->gst_no;
                                    echo $gst_no;
                                    } ?></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email"> FSSSI No:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email"><?php if ($shopDetails->fsssi_no == null){
                                    echo 'Not Updated';
                                    }else{
                                    $fssi_no = $shopDetails->fsssi_no;
                                    echo $fssi_no;
                                    } ?></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email"> Alternet Phone No:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email"><?php if ($shopDetails->alt_phone_no == null){echo 'Not Updated';}else{
                                    $alt_phone = $shopDetails->alt_phone_no;
                                    echo $alt_phone;
                                    }?></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email"> Start  Time:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email"><?php if ($shopDetails->start_time == null){echo 'Not Updated';}else{
                                    $start_time = $shopDetails->start_time;
                                    echo $start_time;
                                    } ?></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email"> End Time:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email"><?php if ($shopDetails->end_time == null){echo 'Not Updated';}else{
                                    $endTime = $shopDetails->end_time;
                                    echo $endTime;
                                    } ?></label>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- end panel -->
            </div>
            <div class="col-md-6">
                <!-- begin panel -->
                <div class="panel panel-inverse">
                    <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
                        </div>
                        <h4 class="panel-title">Bank Details - {{$bankDetails->user->user_name}}</h4>
                    </div>
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>{{Session::get('success')}}</strong>
                        </div>
                    @endif
                    <div></div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">	Holder Name:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email"><?php if ($bankDetails->holder_name == null) {
                                    echo 'Not updated';}else{
                                            $holder_name = $bankDetails->holder_name;
                                            echo $holder_name;
                                    }?></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">Account No:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email"><?php if ($bankDetails->account_no == null) {
                                    echo 'Not updated';
                                }else{
                                    $account_no = $bankDetails->account_no;
                                    echo $account_no;
                                    }
                                    ?></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">IFSC Code:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email"><?php if ($bankDetails->ifsc_code == null) {
                                    echo 'Not Updated';
                                    }else{
                                    $ifsCode = $bankDetails->ifsc_code;
                                    echo $ifsCode;
                                    }?></label>
                            </div>
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

    @endpush
@endsection
