@extends('frontend.layouts.frontendlayout')
@section('title')
    <title>A2Z Harvests : Checkout</title>
@endsection
@section('content')
    <!-----------bannar area start------>
    <section id="breadcrumb">
        <div class="container">
            <div class="breadcrumb-area">
                <ul>
                    <li><a href="{{url('/')}}">Home</a></li>
                    <li><span>/</span></li>
                    <li>Checkout</li>
                </ul>
            </div>
        </div>
    </section>
    <!-----------bannar area close------>
    <!-- -----------checkout page start-------- -->
    <section class="checkout-main-sec">
        <div class="container">
            <div class="row">
                <div class="col-md-7 col-lg-8">
                    <div class="all-item">
                        @php $total=0 ; $tax_total = 0;@endphp
                        @for($i=0;$i<count($cart_data);$i++)
                            @php
                                $datavalue = new App\repo\datavalue();
                                $sale_price = $datavalue->get_sale_price($cart_data[$i]["cart_details"]["price"],$cart_data[$i]["cart_details"]["discount_type"],$cart_data[$i]["cart_details"]["discount_value"]);
                                $total= $total + ($sale_price * $cart_data[$i]["cart_details"]["quantity"]) ;
                                if($cart_data[$i]["cart_details"]["is_inclusive"]=='No'){
                                    $price = $sale_price * $cart_data[$i]["cart_details"]["quantity"];
                                    $tax = $cart_data[$i]["cart_details"]["total_tax_value"];
                                    $tax_price = ($tax*$price)/100;
                                    $tax_total = $tax_total+$tax_price;
                                }
                            @endphp
                        <div class="checkout-item-area">
                            <div class="product-pic">
                                <img src="{{$cart_data[$i]["cart_details"]["product_image"]}}" alt="" loading="lazy">
                            </div>
                            <div class="product-info">
                                <h4>{{$cart_data[$i]["cart_details"]["product_name"]}}</h4>
                                <p>{{$cart_data[$i]["cart_details"]["weight"]." ".$cart_data[$i]["cart_details"]["unit"]}} × {{$cart_data[$i]["cart_details"]["quantity"]}}</p>
                                <h5><span><i class="fas fa-rupee-sign"></i></span>₹ {{$sale_price * $cart_data[$i]["cart_details"]["quantity"]}}</h5>
                                <h6>(GST included)</h6>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
                <div class="col-md-5 col-lg-4">
                    <div class="item-tot-box">
                        <ul>
                            <li>Item Total <span><i class="fas fa-rupee-sign"></i>{{$total}}</span></li>
                            <li>Taxes & Charges <span><i class="fas fa-rupee-sign"></i>{{$tax_total}}</span></li>
                            <li>Delivery Charge <span><i class="fas fa-rupee-sign"></i>{{$delivery_charge}}</span></li>
                            <li>Grand Total <span><i class="fas fa-rupee-sign"></i>{{$total + $tax_total + $delivery_charge}}</span></li>
                        </ul>
                        <div class="button-area">
                            <a href="{{route('timeSlot')}}" class="btn">Next</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- -----------checkout page close-------- -->
<!-- cart area ends -->
<div class="clearfix"></div>
@endsection
