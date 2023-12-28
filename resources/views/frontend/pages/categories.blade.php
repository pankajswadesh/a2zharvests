@extends('frontend.layouts.frontendlayout')
@section('title')
    <title>A2Z Harvests : Shop By Catgeory</title>
@endsection
@section('content')
    <section id="category-sec">
        <div class="container">
            <div class="all-category">
                <div class="head-line">
                    <h2>@if(!empty($parent_details)){{$parent_details["category_name"]}}@endif </h2>
                </div>
                <div class="row">
                    @if(!empty($parent_details))
                        @forelse($category_list as $row)
                            <div class="col-4 col-lg-2">
                                <div class="item">
                                    <a href="{{route('products',[$parent_details["url"],$row->url])}}">
                                        <div class="pdt-pic">
                                            <img src="{{$row->category_image}}" alt="{{$row->category_name}} Image">
                                        </div>
                                        <h4>{{$row->category_name}}</h4>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-xs-12 col-12" style="text-align: center;color: #f24610;">
                                <h3>No Sub category is available.</h3>
                            </div>
                        @endforelse
                    @else
                    @foreach($category_list as $row)
                    <div class="col-4 col-lg-2">
                        <div class="item">
                            <a href="{{route('sub_categories',$row->url)}}">
                                <div class="pdt-pic">
                                    <img src="{{$row->category_image}}" alt="{{$row->category_name}} Image">
                                </div>
                                <h4>{{$row->category_name}}</h4>
                            </a>
                        </div>
                    </div>
                    @endforeach
                   @endif
                </div>
            </div>
        </div>
    </section>
    @push('scripts')
    @endpush
@endsection
