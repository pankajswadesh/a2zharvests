@extends('admin.layouts.adminlayout')
@section('content')
    <div id="content" class="content">
        <!-- begin breadcrumb -->
        <ol class="breadcrumb pull-right">
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">Supplier</a></li>
            <li class="active">Order Details</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Order Details</h1>
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
                        <h4 class="panel-title">Order Details</h4>
                    </div>
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>{{Session::get('success')}}</strong>
                        </div>
                    @endif
                    <div></div>
                    <div class="panel-body" id="print_data">
                        <h2 style="text-align: center;">ORDER ID - {{$order->order_id}} <button onclick="printOrder();" class="non-printable btn btn-sm btn-success">Print</button></h2>
                        <table id="data-table" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Supplier Details</th>
                                <th>Product Name</th>
                                <th>Image</th>
                                <th>Order Qty</th>
                                <th>Supplier Qty</th>
                                <th>Product Gross Price</th>
                                <th>Status</th>
                                <th>Delivery Boy</th>
                                <th class="non-printable">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i=0;
                            $total=0;
                            ?>
                            @foreach($order_details as $details)
                                <?php
                                $i++;
                                if($details->status!='Cancel' && $details->status!='Refunded'  && $details->status!='Rejected'){
                                    $total=$total+$details->gross_price;
                                }
                                if($details->status!='Cancel' && $details->status!='Refunded')
                                ?>
                                <tr class="">
                                    <td>{{$i}}</td>
                                    <td>
                                        <?php
                                        $supplier_name=$details->supplier->user_name;
                                        $location=$details->supplier->location;
                                        echo '<b>Supplier Name : </b>'.$supplier_name;
                                        echo '<br/>';
                                        echo '<b>Address : </b>'.$location;
                                        ?>
                                    </td>
                                    <td>{{$details->product_name}}</td>
                                    <td><img src="{{$details->product->product_image}}" style="height: 100px; width: 100px"/> </td>
                                    <td>{{$details->qty}}</td>
                                    <td>{{$details->supplier_quantity.' '.$details->unit}}</td>
                                    <td>{{$details->gross_price}}</td>
                                    <td>{{$details->status}}</td>
                                    <td>{{ucwords($details->delivery->user_name)}}</td>
                                    <td class="non-printable">
                                        <a href="{{route('admin::viewOrderDelivery', ['id' => $details->id])}}" class="fancybox fancybox.iframe btn btn-xs btn btn-primary" title="Delivery Boy"><span class="glyphicon glyphicon-user"></span></a>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="5"></td>
                                <td>Promo Discount</td>
                                <td>{{$order->promo_discount}} @if($order->applied_promo_code!="")by using {{$order->applied_promo_code}} @endif</td>
                                <td></td>
                                <td></td>
                                <td class="non-printable"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td>Delivery Charge</td>
                                <td>{{$order->delivery_charge}}</td>
                                <td></td>
                                <td></td>
                                <td class="non-printable"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td>Total</td>
                                <td>{{$order->gross_amount}}</td>
                                <td></td>
                                <td></td>
                                <td class="non-printable"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td>Use Wallet Amount</td>
                                <td>{{$order->use_wallet}} @if($order->use_wallet=="Yes") of {{$order->wallet_amount}} @endif</td>
                                <td></td>
                                <td></td>
                                <td class="non-printable"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td>Payment Method</td>
                                <td>{{$payment_details->payment_method}} @if($order->use_wallet=="Yes") + Wallet @endif</td>
                                <td></td>
                                <td></td>
                                <td class="non-printable"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td>Total Due</td>
                                <td>@if($payment_details->payment_method=="cod"){{$order->gross_amount - $order->wallet_amount}} @else 0 @endif</td>
                                <td></td>
                                <td></td>
                                <td class="non-printable"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- end panel -->
            </div>
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
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">	Payment Method:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">{{$payment_details->payment_method}}</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">	Due amount:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">{{$order->gross_amount}}</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">	Payment Status:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">{{$payment_details->payment_status}}</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">	Payment Date:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">{{substr($payment_details->payment_date_time,0,10)}}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end panel -->
            </div>
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
                        <h4 class="panel-title">Shipping Details</h4>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">	Name:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                @if($shipping_details->name=="")
                                    <label for="email">{{ucwords($order->user->user_name)}}</label>
                                @else
                                    <label for="email">{{ucwords($shipping_details->name)}}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">	Email:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                @if($shipping_details->email=="")
                                    <label for="email">{{ucwords($order->user->email)}}</label>
                                @else
                                    <label for="email">{{ucwords($shipping_details->email)}}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">	Phone No:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                @if($shipping_details->phone_no=="")
                                    <label for="email">{{ucwords($order->user->phone)}}</label>
                                @else
                                    <label for="email">{{ucwords($shipping_details->phone_no)}}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">	Address:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                @if($shipping_details->address=="")
                                    <label for="email">{{ucwords($order->user->location)}}</label>
                                @else
                                    <label for="email">{{ucwords($shipping_details->address)}}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">	Pincode:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">{{$shipping_details->pincode}}</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">	Landmark:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">{{$shipping_details->landmark}}</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4">
                                <label for="email">	Booked Slot:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">:</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="email">
                                    @if($order->user_delivery_date!="")
                                        {{$order->user_delivery_date}} ({{$order->user_delivery_time}})
                                    @else
                                        Slot Not Booked
                                    @endif
                                </label>
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
