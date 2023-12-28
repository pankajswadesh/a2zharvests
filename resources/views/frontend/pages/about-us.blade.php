@extends('frontend.layouts.frontendlayout')
@section('title')
    <title>A2Z Harvests : About Us</title>
@endsection
@section('content')
    <section id="breadcrumb">
        <div class="container">
            <div class="breadcrumb-area">
                <ul>
                    <li><a href="{{url('/')}}">Home</a></li>
                    <li><span>/</span></li>
                    <li>About Us</li>

                </ul>
            </div>
        </div>
    </section>
    <!-----------bannar area close------>
    <!------------about area start----------->
    <section id="about-info">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-5">
                    <div class="pic-area">
                        <img src="{{$about_page["image"]}}" alt="">
                    </div>
                </div>
                <div class="col-md-6 col-lg-7">
                    <div class="info">
                        <h3>{{$about_page["title"]}}</h3>
                        {!! $about_page["description"] !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!------------about area close----------->
    <!----------our company area start------------->
    <section id="our-company">
        <div class="container">
            <ul>
                <li>
                    <div class="icon"><img src="{{asset('/')}}/frontendtheme/images/delivery.png" alt=""></div>
                    <span>Free Shipping</span></li>
                <li>
                    <div class="icon"><img src="{{asset('/')}}/frontendtheme/images/home-icon-silhouette.png" alt="">
                    </div>
                    <span>Offline Store</span></li>
                <li>
                    <div class="icon"><img src="{{asset('/')}}/frontendtheme/images/speech-bubble.png" alt=""></div>
                    <span>Quick Responses</span></li>
                <li>
                    <div class="icon"><img src="{{asset('/')}}/frontendtheme/images/discount.png" alt=""></div>
                    <span>Discount System</span></li>
            </ul>
        </div>
    </section>
    <!------------about area close----------->
    <div class="clearfix"></div>
@endsection
