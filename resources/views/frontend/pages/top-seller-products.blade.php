@extends('frontend.layouts.frontendlayout')
@section('title')
    <title>A2Z Harvests : Products</title>
@endsection
@push('css')
    <style>
        #loader
        {
            text-align:center;
            background: url("{{url('/')}}/frontendtheme/images/loader-gif.svg") no-repeat center;
            height: 350px;
        }
    </style>
@endpush
@section('content')
<!-----------bannar area start------>
<section id="banner-area">
    <div class="image">
        <img src="{{asset('/')}}/frontendtheme/images/about-pic2.jpg" alt="">
        <div class="pic-layer">
        </div>
        <div class="top-heading">
            <h3><a href="{{url("/")}}">Home</a><span>/</span>Product</h3>
        </div>
    </div>
</section>
<!-----------product area start------>
<section id="all-product">
<div class="prodact-item">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="product-filter">
                    <div class="filter-area">
                        <div class="category-area">
                            <h4>CATEGORY</h4>
                            <ul>
                                <li class="ctg"><a href="javascript:void(0);">Top Sellers</a></li>
                                <li class="ctg ctg1"><span><i class="fa fa-circle" aria-hidden="true"></i></span><a href="javascript:void(0);">Top Sellers Products</a></li>
                            </ul>
                        </div>
                        <form>
                            <div class="form-group">
                                <!-- Default unchecked -->
                                <h5>Price</h5>
                                <div class="check-area">
                                    <div id="price-slider"></div>
                                    <input type="hidden" id="hidden_minimum_price" value="₹10"/>
                                    <input type="hidden" id="hidden_maximum_price" value="₹3000" />
                                </div>
                            </div>
                            <div class="form-group">
                                <!-- Default unchecked -->
                                <h5>Discount</h5>
                                <div class="check-area">
                                <div class="checkbox">
                                    <label>
                                        <input type="radio" class="common_selector" name="discount" value="0-10" data-ng-model="example.check">
                                        <span class="box"></span>
                                        0% - 10%
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="radio" class="common_selector" name="discount" value="10-25" data-ng-model="example.check">
                                        <span class="box"></span>
                                        15% - 25%
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="radio" class="common_selector" name="discount" value="25-99" data-ng-model="example.check">
                                        <span class="box"></span>
                                        More than 25%
                                    </label>
                                </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-9 col-sm-6">
                <div class="srch-product">
                        <div class="form-group">
                            <label for=""></label>
                            <input type="text" class="form-control" name="search_query" id="search_query" placeholder="Search...">
                            <button type="button" class="btn btn-primary" onclick="search_query()"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                </div>
                <div class="row" id="product_container">

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="shop-pagination">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
 @push('scripts')
     <script src="{{url('/')}}/frontendtheme/js/plugins/nouislider.min.js"></script>
     <script src="{{url('/')}}/frontendtheme/js/plugins/wNumb.js"></script>
      <script>
         $(document).ready(function() {
             var page = localStorage.getItem('page');
             if(page===null){
                 filter_data(1);
             }else{
                 localStorage.removeItem('page');
                 filter_data(page);
             }
             $("#search_query").val("{{$search_query}}");
         });
         var slider = document.getElementById('price-slider');
         if(slider !==null){
             noUiSlider.create(slider, {
                 start: [10, 3000],
                 connect: true,
                 range: {
                     'min': 10,
                     'max': 1000
                 },
                 tooltips: true,
                 format: wNumb({
                     decimals: 0,
                     thousand: '',
                     prefix: '₹'
                 })

             });
         }
         slider.noUiSlider.on('change', function( values, handle ) {
             if (handle) {
                 $('#hidden_maximum_price').val(values[handle]);
             } else {
                 $('#hidden_minimum_price').val(values[handle]);
             }
             filter_data(1);
         });

         $(document).on('click','.common_selector',function () {
             filter_data(1);
         });
         function search_query() {
             filter_data(1);
         }
         function filter_data(page) {
             var action = 'fetch_data';
             var search_query =  $("#search_query").val();
             var minimum_price = $('#hidden_minimum_price').val();
             var minimum_price = minimum_price.slice(1);
             var maximum_price = $('#hidden_maximum_price').val();
             var maximum_price = maximum_price.slice(1);
             var discount = $("input[name='discount']:checked"). val();
             console.log('discount:'+discount);
             console.log('max price:'+maximum_price);
             console.log('min price:'+minimum_price);
             $.ajax({
                 url: '{{route('top_product_filter')}}',
                 method: "POST",
                 beforeSend: function () {
                     $(window).scrollTop(400);
                     $('#product_container').html('<div class="col-lg-12 col-md-12 col-sm-12"><div id="loader"></div></div>');
                     $('.shop-pagination').hide();
                 },
                 data: {
                     _token: '<?php echo csrf_token();?>',
                     action: action,
                     search_query: search_query,
                     minimum_price: minimum_price,
                     maximum_price: maximum_price,
                     discount: discount,
                     page:page
                 },
                 success: function (data) {
                     console.log(data);
                     var resp = JSON.parse(data);
                     var product_container = $('#product_container');
                     product_container.html(resp.html);
                     $('.shop-pagination').html(resp.pagination);
                     $('.shop-pagination').show();
                 }
             });
         }
         $(document).on("click", ".clickPage", function () {
             var page = parseInt($(this).attr('data-page'));
             localStorage.setItem('page',page);
             filter_data(page);
         });
     </script>
 @endpush
@endsection
