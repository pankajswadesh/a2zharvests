@extends('frontend.layouts.frontendlayout')
@section('title')
    <title>A2Z Harvests : Terms & Condition</title>
@endsection
@section('content')
    <!-----------bannar area start------>
    <section id="breadcrumb">
        <div class="container">
            <div class="breadcrumb-area">
                <ul>
                    <li><a href="{{url('/')}}">Home</a></li>
                    <li><span>/</span></li>
                    <li>Terms and Conditions</li>
                </ul>
            </div>
        </div>
    </section>
    <!-----------bannar area close------>
    <!--==============term-condition=============-->
    <section id="term-condition-area">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="term-details">
                        {!! $data["contents"] !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==============term-condition-end=============-->
    <div class="clearfix"></div>
@endsection
