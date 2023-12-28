@extends('admin.layouts.adminlayout')
@section('content')
    <div id="content" class="content">
        <!-- begin breadcrumb -->
        <ol class="breadcrumb pull-right">
            <li><a href="javascript:;">Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->

        <h1 class="page-header">Dashboard</h1>
        <!-- end page-header -->

        <!-- begin row -->
        <div class="row">
        @if(Auth::user()->hasRole('admin'))
            <!-- begin col-3 -->
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-stats bg-green">
                    <div class="stats-icon"><i class="fa fa-desktop"></i></div>
                    <div class="stats-info">
                        <h4>TOTAL USERS</h4>
                        <p>{{$users}}</p>
                    </div>
                    <div class="stats-link">
                        <a href="{{route('admin::manageUser')}}">View Detail <i class="fa fa-arrow-circle-o-right"></i></a>
                    </div>
                </div>
            </div>
            <!-- end col-3 -->
                <!-- begin col-3 -->
                <div class="col-md-3 col-sm-6">
                    <div class="widget widget-stats bg-blue">
                        <div class="stats-icon"><i class="fa fa-chain-broken"></i></div>
                        <div class="stats-info">
                            <h4>TOTAL PRODUCTS</h4>
                            <p>{{$products}}</p>
                        </div>
                        @if(Auth::user()->hasRole('admin'))
                            <div class="stats-link">
                                <a href="{{route('admin::manageProduct')}}">View Detail <i class="fa fa-arrow-circle-o-right"></i></a>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- end col-3 -->
                <!-- begin col-3 -->
                <div class="col-md-3 col-sm-6">
                    <div class="widget widget-stats bg-purple">
                        <div class="stats-icon"><i class="fa fa-product-hunt"></i></div>
                        <div class="stats-info">
                            <h4>TOTAL SUPPLIERS PRODUCTS</h4>
                            <p>{{$product_count}}</p>
                        </div>
                        <div class="stats-link">
                            <a href="{{route('admin::manageSupplierProduct')}}">View Detail <i class="fa fa-arrow-circle-o-right"></i></a>
                        </div>
                    </div>
                </div>
                <!-- end col-3 -->
                <!-- begin col-3 -->
                <div class="col-md-3 col-sm-6">
                    <div class="widget widget-stats bg-red">
                        <div class="stats-icon"><i class="fa fa-cart-plus"></i></div>
                        <div class="stats-info">
                            <h4>TOTAL CUSTOMERS ORDERS</h4>
                            <p>{{$orders}}</p>
                        </div>
                        <div class="stats-link">
                            <a href="{{route('admin::manageOrder')}}">View Detail <i class="fa fa-arrow-circle-o-right"></i></a>
                        </div>
                    </div>
                </div>
                <!-- end col-3 -->
        @endif
            @if(Auth::user()->hasRole('supplier'))
                <!-- begin col-3 -->
                    <div class="col-md-3 col-sm-6">
                        <div class="widget widget-stats bg-purple">
                            <div class="stats-icon"><i class="fa fa-product-hunt"></i></div>
                            <div class="stats-info">
                                <h4>TOTAL PRODUCTS</h4>
                                <p>{{$products}}</p>
                            </div>
                            <div class="stats-link">
                                <a href="{{route('admin::manageAdminProduct')}}">View Detail <i class="fa fa-arrow-circle-o-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- end col-3 -->
                    <!-- begin col-3 -->
                    <div class="col-md-3 col-sm-6">
                        <div class="widget widget-stats bg-red">
                            <div class="stats-icon"><i class="fa fa-cart-plus"></i></div>
                            <div class="stats-info">
                                <h4>My Products</h4>
                                <p>{{$product_count}}</p>
                            </div>
                            <div class="stats-link">
                                <a href="{{route('admin::manageMyProduct')}}">View Detail <i class="fa fa-arrow-circle-o-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- end col-3 -->
            @endif

        </div>
        <!-- end row -->
        <!-- begin row -->

        <!-- end row -->
    </div>
@endsection