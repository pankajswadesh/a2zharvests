@extends('frontend.layouts.frontendlayout')
@section('title')
    <title>A2Z Harvests : Product Details</title>
@endsection
@section('content')
<!--================product details page area start====================-->
<!-- product details area starts -->
<section id="details-sec" class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="product-details-area">
                    <!--------------exzoom area start--------->
                    @php
                        $sale_price = App\repo\datavalue::get_sale_price($product_details["price"],$product_details["discount_name"],$product_details["discount_value"])
                    @endphp
                    <div class="exzoom" id="exzoom">
                        @if($sale_price<$product_details["price"])
                        <h4 class="price exzoom_img-price product-image">
                            @if($product_details["discount_name"]=='rs')
                                <span class="discount">Rs.{{$product_details["discount_value"]}} <br> off</span>
                            @else
                                <span class="discount">{{$product_details["discount_value"]}}% <br> off</span>
                            @endif
                        </h4>
                        @endif
                        <!-- Images -->
                        <div class="exzoom_img_box">
                            <ul class='exzoom_img_ul'>
                                <li><img src="{{$product_details["product_image"]}}"/></li>
                                @foreach($product_details->images as $row)
                                   <li><img src="{{$row->image}}"/></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="exzoom_nav"></div>
                    </div>
                    <!--------------exzoom area close--------->
                </div>
            </div>
            <div class="col-md-8">
                <h2>{{$product_details["product_name"]}}, {{$product_details["quantity"].' '.$product_details["unit"]->unit_name}}</h2>
                <h5>Availability: <span>In stock</span></h5>
                <h4 class="price-new">MRP: Rs.{{$product_details["price"]}} | Price: Rs.{{$sale_price}}
                    @if($product_details["discount_name"]=='rs')
                        <span>You Save: Rs.{{$product_details["discount_value"]}}</span>(inclusive of all taxes)
                    @else
                        <span>You Save: {{$product_details["discount_value"]}}%</span>(inclusive of all taxes)
                    @endif
                </h4>
                <hr>
                <div class="form-area">
                    <div class="form-wrap">
                        <div class="form-group">
                            <label for="quantity">Quantity: </label>
                            <input type="number" style="max-width:165px; " id="quantity" value="1" class="form-control">
                        </div>
                        <div class="form-group">
                            @if($product_details["status"]=='Active')
                                @if(Auth::check())
                                    <button type="button" onclick="details_add_cart('{{$supplier_id}}','{{$product_details["product_id"]}}');" class="submit-btn">Add To Cart</button>
                                @else
                                    <a data-toggle="modal" data-target="#myModal1" class="add-area"><button type="button" class="submit-btn">Add To Cart</button></a>
                                @endif
                            @else
                                <button type="button" class="submit-btn">Out Of Stock</button>
                            @endif
                        </div>
                        @if($showSlot)
                            <p style="color: black;font-weight: 500;">Same day delivery</p>
                            <p style="color: black;font-weight: 500;">Check your next available slot in checkout.</p>
                        @else
                            <p style="color: black;font-weight: 500;">item will be delivered in 3-4 days after product is shipped.</p>
                        @endif
                    </div>
                </div>
                <hr>
                <p>{!! $product_details["product_description"] !!}</p>
            </div>
        </div>
    </div>
</section>
<!--------related product area start-------->
<section class="deals-area section" id="deal-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="heading">
                    <h1>Related Products</h1>
                </div>
            </div>
            <div id="wholesaler-carousel">
                @foreach($related_product as $row)
                    <?php  $datavalue = new App\repo\datavalue();
                    $sale_price = $datavalue->get_sale_price($row->price, $row->discount_name, $row->discount_value);
                    ?>
                <div class="item">
                    <div class="product-details">
                        <div class="product-details-inner">
                            <div class="product-image">
                                @if($sale_price<$row->price)
                                    @if($row->discount_name=='rs')
                                        <span class="discount">Rs.{{$row->discount_value}} <br> off</span>
                                    @else
                                        <span class="discount">{{$row->discount_value}}% <br> off</span>
                                    @endif
                                @endif
                                <a href="{{route('product_details', [$row->user_id, $row->url])}}"><img src="{{$row->product_image}}" alt="Product Image"></a>
                            </div>
                            <div class="icon-menu">
                                <a href="#"><i class="fa fa-cart-arrow-down"></i></a>
                            </div>
                        </div>
                        <div class="text-content">
                            <h5><a href="{{route('product_details', [$row->user_id, $row->url])}}">{{$row->product_name}}</a></h5>
                            <p>{{$row->quantity . ' ' . $row->unit_name }}</p>
                            <div class="add-cart">
                                <h6><span class="rupee">&#x20B9</span>{{$sale_price}}</h6>
                                @if(Auth::check())
                                    <a href="javascript:add_cart('{{$row->user_id}}','{{$row->product_id}}');" class="add-area"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>ADD</a>
                                @else
                                    <a data-toggle="modal" data-target="#exampleModal" class="add-area"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>ADD</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="col-12">
                <a href="{{route('products',[$product_details->category->url,$product_details->sub_category->url])}}" class="cstm-btn">View All</a>
            </div>
        </div>
    </div>
</section>
<!--------related product area close-------->
<!--================product details page area close====================-->
@push("scripts")
    <!-- zxoom-->
    <script src="{{asset('/')}}/frontendtheme/exzoom/js/jquery.exzoom.js"></script>
    <script>
        $('#exzoom').exzoom({
            autoPlay: false,
        });
    </script>
    <script>
        function details_add_cart(supplier_id,product_id){
            var quantity = $("#quantity").val();
            $.ajax({
                url: "{{route('add_to_cart')}}",
                type: "post",
                data: {_token:'{{csrf_token()}}',supplier_id:supplier_id,product_id:product_id,quantity:quantity},
                success: function (resp)
                {
                    console.log(resp);
                    if(resp.status==="success"){
                        var count = resp.data.total_qty;
                        toastr.success(resp.msg, {timeOut: 2000});
                        $("#cart_count").text(count);
                    }else{
                        toastr.warning(resp.msg, {timeOut: 2000});
                    }
                }
            });
        }
    </script>
@endpush
@endsection
