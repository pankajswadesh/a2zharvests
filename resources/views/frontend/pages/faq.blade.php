@extends('frontend.layouts.frontendlayout')
@section('title')
    <title>A2Z Harvests: Faq</title>
@endsection
@section('content')
<!-----------bannar area start------>
<section id="breadcrumb">
    <div class="container">
        <div class="breadcrumb-area">
            <ul>
                <li><a href="{{url("/")}}">Home</a></li>
                <li><span>/</span></li>
                <li>FAQ</li>

            </ul>
        </div>
    </div>
</section>
<!-----------bannar area close------>
<!-- faq heading area starts -->
<section id="active-campaigns">
    <div class="container">
        <div class="heading">
            <h1>Frequently Asked Questions</h1>
        </div>
    </div>
</section>
<!-- faq heading area ends -->
<!--------------faq contant area area start------------>
<section id="faq-area">
    <div class="container">
        <div id="accordion">
            @foreach($data as $row)
            <div class="card">
                <div class="card-header">
                    <a class="card-link" data-toggle="collapse" href="#collapse{{$row->id}}">
                        {{$row->question}}
                        <span><i class="fas fa-plus"></i></span>
                    </a>
                </div>
                <div id="collapse{{$row->id}}" class="collapse" data-parent="#accordion">
                    <div class="card-body">
                        <span><i class="far fa-dot-circle"></i></span>
                        {{$row->answer}}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<!--------------faq contant area area close------------>
<div class="clearfix"></div>
@endsection
