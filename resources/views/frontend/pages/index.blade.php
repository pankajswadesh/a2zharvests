@extends('frontend.layouts.frontendlayout')
@section('title')
    <title>A2Z Harvests : Home</title>
@endsection
@section('content')
    <section id="slider-area" class="section">
        <div class="container-fluid">
            <div class="row">
                <div id="main-slider">
                    @foreach($sliders as $row)
                    <div class="item">
                        <a href="#"><img src="{{$row->slider_image}}" alt="" loading="lazy"></a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <!-- slider area ends -->
    <section class="short-info-sec1">
        <div class="info-sec-box">
            <ul>
                <li>Get Your Order As Fast As You Want</li>
            </ul>
            <ul>
                <li>
                    <div class="icon"><i class="fas fa-truck"></i></div>
                </li>
                <li>Slot based Delivery</li>
            </ul>
            <ul>
                <li>
                    <div class="icon"><i class="far fa-credit-card"></i></div>
                </li>
                <li>We Also Accept Cash On Delivery</li>
            </ul>
            <ul>
                <li>
                    <div class="icon"><i class="fas fa-truck"></i></div>
                </li>
                <li>Free Delivery</li>
            </ul>
        </div>
    </section>
    <section id="category-sec" class="sec12">
        <div class="container">
            <div class="all-category large">
                <div class="head-line">
                    <h2>Shop by Category </h2>
                </div>
                <div class="row">
                    @foreach($categories as $row)
                    <div class="col-4 col-lg-2">
                        <div class="item">
                            <a href="{{route('sub_categories',$row->url)}}">
                                <div class="pdt-pic">
                                    <img src="{{$row->category_image}}" alt="">
                                </div>
                                <h4>{{$row->category_name}}</h4>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <!--fixed product search bar start-->
    <div class="srch-product home">
        <form class="search_form" method="get" action="{{route('products',['search',''])}}">
            <div class="form-group">
                <label for=""></label>
                <input type="text" name="search_query"  class="form-control search_query" placeholder="Search...">
                <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
            </div>
        </form>
    </div>
    <!--fixed product search bar close-->
    <!-- adds area starts -->
    <section class="adds-area section" id="add-1">
        <div class="container">
            <div class="row">
                @foreach($image_banner as $row)
                <div class="col-md-4">
                    <div class="adds">
                        <a href="#"><img src="{{$row->image}}" alt=""></a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- adds area ends -->
    @if(count($products)>0)
    <!-- new products area starts -->
    <section class="deals-area section" id="deal-1">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="heading">
                        <h1>Top Sellers</h1>
                    </div>
                </div>
                <div id="product-carousel">
                    @foreach($products as $row)
                        <?php
                        $datavalue = new App\repo\datavalue();
                        $sale_price = $datavalue->get_sale_price($row->price,$row->discount_name,$row->discount_value);
                        $opacity='';
                        if($row->status=='Inactive'){
                            $opacity='opacity';
                        }
                        ?>
                       <div class="item">
                        <div class="product-details">
                            <div class="product-details-inner {{$opacity}}">
                                <div class="product-image">
                                    @if($sale_price<$row->price)
                                        @if($row->discount_name=='rs')
                                          <span class="discount">Rs.{{$row->discount_value}} <br> off</span>
                                        @else
                                            <span class="discount">{{$row->discount_value}}% <br> off</span>
                                        @endif
                                    @endif
                                    <a href="{{route('product_details',[$row->user_id,$row->url])}}"><img src="{{$row->product_image}}" alt=""></a>
                                </div>
                                <div class="icon-menu">
                                    <a href="#"><i class="fa fa-cart-arrow-down"></i></a>
                                </div>
                            </div>
                            <div class="text-content">
                                {{--<span>Product Category</span>--}}
                                <h5><a href="{{route('product_details',[$row->user_id,$row->url])}}">{{$row->product_name}}</a></h5>
                                <h6>&#x20B9 {{$sale_price}} @if($sale_price<$row->price)<span>&#x20B9 {{$row->price}}</span>@endif</h6>
                                <div class="add-cart">
                                    <p>{{$row->quantity}} {{$row->unit_name}}</p>
                                    @if($row->status=='Active')
                                        @if(Auth::check())
                                            <a href="javascript:add_cart('{{$row->user_id}}','{{$row->product_id}}');" class="add-area"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>ADD</a>
                                        @else
                                            <a data-toggle="modal" data-target="#myModal1" class="add-area"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>ADD</a>
                                        @endif
                                    @else
                                        <a href="javascript:void(0);" class="add-area orange">Out Of Stock</a>
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
    <!-- new products area ends -->
    @endif
    <!--------slider-text------------>
    <section class="slider-area section deals-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div id="text-slider">
                        @foreach($text_banner as $row)
                        <div class="item">
                            <div class="w-100">
                                <h2>{{$row->title}}</h2>
                                <p>{{$row->description}}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--------slider-text end------------>
    <!-- high end products area starts -->
    <section class="deals-area section" id="deal-3">
        <div class="container">
            @foreach($categories_list as $row)
                @php $products_list = App\Http\Controllers\Frontend\PageController::getProductByCategory($row->id); @endphp
                @if($products_list->count()>0)
                    @php $category_slug = \App\Model\CategoryModel::where('id',$row->id)->value('url');
                 $subcategory_slug = \App\Model\CategoryModel::where('parent_id',$row->id)->value('url');
                    @endphp
                   <div class="row">
                        <div class="col-12">
                            <div class="heading">
                                <h1>{{$row->category_name}}</h1>
                            </div>
                        </div>
                        <div id="high-end-carousel">
                            @foreach($products_list as $row)
                                <?php
                                $datavalue = new App\repo\datavalue();
                                $sale_price = $datavalue->get_sale_price($row->price,$row->discount_name,$row->discount_value);
                                $opacity='';
                                if($row->status=='Inactive'){
                                    $opacity='opacity';
                                }
                                ?>
                            <div class="item">
                                <div class="product-details">
                                    <div class="product-details-inner {{$opacity}}">
                                        <div class="product-image">
                                            @if($sale_price<$row->price)
                                                @if($row->discount_name=='rs')
                                                    <span class="discount">Rs.{{$row->discount_value}} <br> off</span>
                                                @else
                                                    <span class="discount">{{$row->discount_value}}% <br> off</span>
                                                @endif
                                            @endif
                                            <a href="{{route('product_details',[$row->user_id,$row->url])}}"><img src="{{$row->product_image}}" alt=""></a>
                                        </div>
                                        <div class="icon-menu">
                                            <a href="#"><i class="fa fa-cart-arrow-down"></i></a>
                                        </div>
                                    </div>
                                    <div class="text-content">
                                        <h5><a href="{{route('product_details',[$row->user_id,$row->url])}}">{{$row->product_name}}</a></h5>
                                        <h6>&#x20B9 {{$sale_price}} @if($sale_price<$row->price)<span>&#x20B9 {{$row->price}}</span>@endif</h6>
                                        <div class="add-cart">
                                            <p>{{$row->quantity}} {{$row->unit_name}}</p>
                                            @if($row->status=='Active')
                                                @if(Auth::check())
                                                    <a href="javascript:add_cart('{{$row->user_id}}','{{$row->product_id}}');" class="add-area"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>ADD</a>
                                                @else
                                                    <a data-toggle="modal" data-target="#myModal1" class="add-area"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>ADD</a>
                                                @endif
                                            @else
                                                <a href="javascript:void(0);" class="add-area orange">Out Of Stock</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="col-12">
                            <a href="{{route('products',[$category_slug,$subcategory_slug])}}" class="cstm-btn">View All</a>
                        </div>
                   </div>
                @endif
            @endforeach
        </div>
    </section>
    <!-- high end products area ends -->
    <!-- adds area starts -->
    <section class="adds-area section" id="add-2">
        <div class="container">
            <div class="row">
                @foreach($add_images as $row)
                    <div class="col-md-6">
                        <div class="adds">
                            <img src="{{$row->image}}" alt="">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- adds area ends -->
    @if(count($recent_products)>0)
    <!-- Recent Search area starts -->
    <section class="deals-area section" id="deal-4">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="heading">
                        <h1>Recent Search</h1>
                    </div>
                </div>
                <div id="wholesaler-carousel">
                    @foreach($recent_products as $row)
                        <?php
                        $datavalue = new App\repo\datavalue();
                        $sale_price = $datavalue->get_sale_price($row->price,$row->discount_name,$row->discount_value);
                        $opacity='';
                        if($row->status=='Inactive'){
                            $opacity='opacity';
                        }
                        ?>
                       <div class="item">
                        <div class="product-details">
                            <div class="product-details-inner {{$opacity}}">
                                <div class="product-image">
                                    @if($sale_price<$row->price)
                                        @if($row->discount_name=='rs')
                                            <span class="discount">Rs.{{$row->discount_value}} <br> off</span>
                                        @else
                                            <span class="discount">{{$row->discount_value}}% <br> off</span>
                                        @endif
                                    @endif
                                    <a href="{{route('product_details',[$row->user_id,$row->url])}}"><img src="{{$row->product_image}}" alt=""></a>
                                </div>
                                <div class="icon-menu">
                                    <a href="#"><i class="fa fa-cart-arrow-down"></i></a>
                                </div>
                            </div>
                            <div class="text-content">
                                <h5><a href="{{route('product_details',[$row->user_id,$row->url])}}">{{$row->product_name}}</a></h5>
                                <h6>&#x20B9 {{$sale_price}} @if($sale_price<$row->price)<span>&#x20B9 {{$row->price}}</span>@endif</h6>
                                <div class="add-cart">
                                    <p>{{$row->quantity}} {{$row->unit_name}}</p>
                                    @if($row->status=='Active')
                                        @if(Auth::check())
                                            <a href="javascript:add_cart('{{$row->user_id}}','{{$row->product_id}}');" class="add-area"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>ADD</a>
                                        @else
                                            <a data-toggle="modal" data-target="#myModal1" class="add-area"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>ADD</a>
                                        @endif
                                    @else
                                        <a href="javascript:void(0);" class="add-area orange">Out Of Stock</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
<!-- Recent Search area ends -->
    @endif
<!-- adds area ends -->
    @push('scripts')
        <script>
        $(document).ready(function () {
        $('#locationSet').modal({backdrop: 'static', keyboard: false})
       
        
         $(window).on('load',function () {
              var locationCheck = "{{$locationCheck}}";
              if(locationCheck == 1){
                  $("#locationSet").modal('hide');
              }else{
                $("#locationSet").modal('show');
              }
            });
    });
        
           
            if("{{Session::has('error')}}"){
                toastr.warning("{{Session::get('error')}}", {timeOut: 2000});
            }
            if("{{Session::has('success')}}"){
                toastr.success("{{Session::get('success')}}", {timeOut: 2000});
            }
        </script>
    @endpush
@endsection
