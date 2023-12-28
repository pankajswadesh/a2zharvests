@extends('frontend.layouts.frontendlayout')
@section('title')
    <title>A2Z Harvests : Cart</title>
@endsection
@section('content')
<!-----------bannar area start------>
<section id="breadcrumb">
    <div class="container">
        <div class="breadcrumb-area">
            <ul>
                <li><a href="{{url('/')}}">Home</a></li>
                <li><span>/</span></li>
                <li>Cart</li>

            </ul>
        </div>
    </div>
</section>
<!-----------bannar area close------>
<!-- cart area starts -->
<section id="shopping-cart" class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Items</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Total Price </th>
                            <th>&nbsp; </th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $total=0 ;@endphp
                        @forelse($cart_data as $row)
                            @php
                                $datavalue = new App\repo\datavalue();
                                $discount = json_decode($row->discount,true);
                                $sale_price = $datavalue->get_sale_price($row->price, $discount["discount_type"], $discount["discount_value"]);
                               $total= $total + ($sale_price * $row->quantity) ;
                            @endphp
                        <tr>
                            <td>
                                <div class="media">
                                    <div class="media-left"> <a href="#"> <img class="img-responsive" src="{{$row->product_image}}" alt=""> </a> </div>
                                    <div class="media-body">
                                        <p>{{$row->product_name}}({{$row->supplier_product->quantity}} {{$row->unit}})</p>
                                    </div>
                                </div>
                            </td>

                            <td class="text-center">₹ {{$sale_price}}</td>
                            <td class="text-center">
                                <div class="quantity">
                                    <div class="form-group">
                                        <input type="number" min="1" id="qty_{{$row->id}}" value="{{$row->quantity}}" onkeyup="update_cart('{{$row->id}}',this.value ,'{{$sale_price}}')" onchange="update_cart('{{$row->id}}',this.value ,'{{$sale_price}}')" class="form-control">
                                    </div>
                                </div>
                            </td>
                            <td class="text-center subtotal" id="sub_total_{{$row->id}}">₹ {{$sale_price * $row->quantity}}</td>
                            <td class="text-center"><a href="{{route('remove_cart',$row->id)}}" class="remove"><i class="fas fa-trash"></i></a></td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center;color: red;"> No Product in your cart.@if(!Auth::check())Please login for get your cart data.@endif</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if(count($cart_data)>0)
                <div class="promo">
                    <div class="g-totel">
                        <h5>Grand total: <span id="grand_total">₹{{$total}}</span></h5>
                    </div>
                </div>
                <div class="pro-btn">
                    <a href="{{route('manageAddress')}}" class="cstm-btn">Proceed To Checkout</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
<!-- cart area ends -->
<!--------related product area start-------->
<section class="deals-area section" id="deal-2">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="heading">
                    <h1>Top Section</h1>
                </div>
            </div>
            <div id="used-products-carousel">
                @foreach($products as $row)
                    <div class="item">
                        <div class="product-details">
                            <div class="product-details-inner">
                                <div class="product-image">
                                    <a href="{{route('product_details',[$row->user_id,$row->url])}}"><img src="{{$row->product_image}}" alt=""></a>
                                </div>
                                <div class="icon-menu">
                                    <a href="#"><i class="fa fa-cart-arrow-down"></i></a>
                                </div>
                            </div>
                            <div class="text-content">
                                <?php
                                $datavalue = new App\repo\datavalue();
                                $sale_price = $datavalue->get_sale_price($row->price,$row->discount_name,$row->discount_value);
                                ?>
                                <span>Product Category</span>
                                <h5><a href="{{route('product_details',[$row->user_id,$row->url])}}">{{$row->product_name}}</a></h5>
                                <h6>&#x20B9 {{$sale_price}} @if($sale_price<$row->price)<span>&#x20B9 {{$row->price}}</span>@endif</h6>
                                <div class="add-cart">
                                    <p>{{$row->quantity}} {{$row->unit_name}}</p>
                                    @if(Auth::check())
                                        <a href="javascript:add_cart_cart('{{$row->user_id}}','{{$row->product_id}}');" class="add-area"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>ADD</a>
                                    @else
                                        <a data-toggle="modal" data-target="#myModal1" class="add-area"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>ADD</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-12">
                <a href="{{route('top_seller_products')}}" class="cstm-btn">View All</a>
            </div>
        </div>
    </div>
</section>
<!--------related product area close-------->
<div class="clearfix"></div>
    @push('scripts')
        <script>
            function add_cart_cart(supplier_id,product_id){
                $.ajax({
                    url: "{{route('add_to_cart')}}",
                    type: "post",
                    data: {_token:'{{csrf_token()}}',supplier_id:supplier_id,product_id:product_id,quantity:1},
                    success: function (resp)
                    {
                        if(resp.status==="success"){
                            var count = resp.data.total_qty;
                            toastr.success(resp.msg, {timeOut: 2000});
                            location.reload();
                        }else{
                            toastr.warning(resp.msg, {timeOut: 2000});
                        }
                    }
                });
            }
           function update_cart(id,quantity,price){
               if(quantity!=''){
                   if(quantity<1){
                       $("#qty_"+id).val(1);
                       toastr.warning('Quantity must be greater than 0.', {timeOut: 2000});
                   }else{
                       $.ajax({
                           url: "{{route('update_cart')}}",
                           type: "post",
                           data: {_token:'{{csrf_token()}}',id:id,quantity:quantity},
                           success: function (resp)
                           {
                               console.log(resp);
                               if(resp.status==="success"){
                                   var count = resp.data.total_qty;
                                   toastr.success(resp.msg, {timeOut: 2000});
                                   $("#cart_count").text(count);
                                   var sub_total = price * quantity;
                                   $("#sub_total_"+id).text('₹'+ sub_total);
                                   $("#grand_total").text('₹'+ resp.data.grand_total);
                               }else{
                                   toastr.warning(resp.msg, {timeOut: 2000});
                               }
                           }
                       });
                   }
               }else{
                   toastr.warning('Please fill quantity.', {timeOut: 2000});
               }
           }
        </script>
    @endpush
@endsection
