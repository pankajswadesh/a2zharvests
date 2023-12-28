<div id="sidebar" class="sidebar">

    <!-- begin sidebar scrollbar -->

    <div data-scrollbar="true" data-height="100%">

        <!-- begin sidebar user -->

        <ul class="nav">

            <li class="nav-profile">

                <div class="image">
                    <a href="javascript:;"><img src="{{url('/')}}/frontendtheme/images/logo.png" alt="" /></a>
                </div>

                <div class="info">

                    {{ ucfirst(Auth::user()->user_name)}}

                </div>

            </li>

        </ul>

        <!-- end sidebar user -->

        <!-- begin sidebar nav -->

        <?php

        $segment= Request::segment(3);

        ?>

        <ul class="nav">

            <li class="nav-header">Navigation</li>

            <li class="<?php if($segment=='dashboard' ){ echo 'active';}?>"><a href="{{route('admin::dashboard')}}"><i class="fa fa-calendar"></i> <span>Dashboard</span></a></li>
            @role('admin')
            <li class="has-sub <?php if($segment=='manage-user' || $segment=='manage-supplier' || $segment=='manage-delivery' || $segment=='manage-manager' || $segment=='manager-suppliers' || $segment=='manager-delivery'){ echo 'active expand';}?>">
                <a href="javascript:;">
                    <b class="caret pull-right"></b>
                    <i class="fa fa-users"></i>
                    <span>All Users</span>
                </a>
                <ul class="sub-menu" style="display: <?php if( $segment=='manage-user' || $segment=='manage-supplier' || $segment=='manage-delivery' || $segment=='manage-manager' || $segment=='manager-suppliers' || $segment=='manager-delivery'){ echo 'block';}else{ echo 'none';} ?>">
                    <li class="<?php if( $segment=='manage-manager' || $segment=='manager-suppliers' || $segment=='manager-delivery'){ echo 'active';}?>"><a href="{{route('admin::manageManager')}}">Managers</a></li>
                    <li class="<?php if( $segment=='manage-user'){ echo 'active';}?>"><a href="{{route('admin::manageUser')}}">Users</a></li>
                    <li class="<?php if( $segment=='manage-supplier'){ echo 'active';}?>"><a href="{{route('admin::manageSupplier')}}">Suppliers</a></li>
                    <li class="<?php if( $segment=='manage-delivery'){ echo 'active';}?>"><a href="{{route('admin::manageDelivery')}}">Delivery Boy</a></li>
                </ul>

            </li>
            <li class="has-sub <?php if($segment=='manage-order' || $segment=='view-order-details' || $segment=='manage-outside-order'){ echo 'active expand';}?>">

                <a href="javascript:;">

                    <b class="caret pull-right"></b>

                    <i class="fa fa-users"></i>

                    <span>Orders</span>

                </a>

                <ul class="sub-menu" style="display: <?php if( $segment=='manage-order' || $segment=='view-order-details' || $segment=='manage-outside-order'){ echo 'block';}else{ echo 'none';} ?>">

                    <li class="<?php if( $segment=='manage-order'){ echo 'active';}?>"><a href="{{route('admin::manageOrder')}}">All Orders</a></li>
                    <li class="<?php if( $segment=='manage-outside-order'){ echo 'active';}?>"><a href="{{route('admin::manageOutsideOrder')}}">Outside Orders</a></li>

                </ul>

            </li>

            <li class="has-sub <?php if($segment=='manage-supplier-product' || $segment=='manage-supplier-sale' || $segment=='manage-pending-order' || $segment=='manage-cancel-order' || $segment=='manage-delivery-order' || $segment=='manage-day-end-report' || $segment=='manage-delivery-boy-report' || $segment=='manage-delivery-boy-report-details' || $segment=='manage-reject-order'){ echo 'active expand';}?>">

                <a href="javascript:;">

                    <b class="caret pull-right"></b>

                    <i class="fa fa-list"></i>

                    <span>Reports</span>

                </a>

                <ul class="sub-menu" style="display: <?php if( $segment=='manage-supplier-product' || $segment=='manage-supplier-sale' || $segment=='manage-pending-order' || $segment=='manage-cancel-order' || $segment=='manage-delivery-order' || $segment=='manage-day-end-report' || $segment=='manage-delivery-boy-report' || $segment=='manage-delivery-boy-report-details' || $segment=='manage-reject-order'){ echo 'block';}else{ echo 'none';} ?>">

                    <li class="<?php if( $segment=='manage-supplier-product'){ echo 'active';}?>"><a href="{{route('admin::manageSupplierProduct')}}">Supplier's Product</a></li>

                    <li class="<?php if( $segment=='manage-supplier-sale'){ echo 'active';}?>"><a href="{{route('admin::manageSupplierSale')}}">Supplier's Sale</a></li>

                    <li class="<?php if( $segment=='manage-pending-order'){ echo 'active';}?>"><a href="{{route('admin::managePendingOrder')}}">Pending Order</a></li>

                    <li class="<?php if( $segment=='manage-cancel-order'){ echo 'active';}?>"><a href="{{route('admin::manageCancelOrder')}}">Cancel Order</a></li>

                    <li class="<?php if( $segment=='manage-delivery-order'){ echo 'active';}?>"><a href="{{route('admin::manageDeliveryOrder')}}">Delivery Order</a></li>

                    <li class="<?php if( $segment=='manage-reject-order'){ echo 'active';}?>"><a href="{{route('admin::manageRejectOrder')}}">Reject Order</a></li>

                <li class="<?php if( $segment=='manage-day-end-report'){ echo 'active';}?>"><a href="{{route('admin::manageDayEndReport')}}">Day End Report</a></li>

                    <li class="<?php if( $segment=='manage-delivery-boy-report' || $segment=='manage-delivery-boy-report-details'){ echo 'active';}?>"><a href="{{route('admin::manageDeliveryBoyReport')}}">Delivery Boy Report</a></li>

                </ul>

            </li>

            <li class="<?php if($segment=='manage-role' ){ echo 'active';}?>"><a href="{{route('admin::manageRole')}}"><i class="fa fa-calendar"></i> <span>Role</span></a></li>

            <li class="has-sub <?php if($segment=='manage-slider' || $segment=='manage-text-banner' || $segment=='manage-image-banner'){ echo 'active expand';}?>">

                <a href="javascript:;">

                    <b class="caret pull-right"></b>

                    <i class="fa fa-sliders"></i>
                    <span>Banners</span>
                </a>
                <ul class="sub-menu" style="display: <?php if( $segment=='manage-slider' || $segment=='manage-text-banner' || $segment=='manage-image-banner'){ echo 'block';}else{ echo 'none';} ?>">

                    <li class="<?php if( $segment=='manage-slider'){ echo 'active';}?>"><a href="{{route('admin::manageSlider')}}">Sliders</a></li>

                    <li class="<?php if( $segment=='manage-text-banner'){ echo 'active';}?>"><a href="{{route('admin::manageTextBanner')}}">Text Banners</a></li>

                    <li class="<?php if( $segment=='manage-image-banner'){ echo 'active';}?>"><a href="{{route('admin::manageImageBanner')}}">Image Banners</a></li>

                </ul>

            </li>

            <li class="has-sub <?php if($segment=='manage-category'){ echo 'active expand';}?>">
                <a href="javascript:;">
                    <b class="caret pull-right"></b>
                    <i class="fa fa-sliders"></i>
                    <span>Manage Category</span>
                </a>
                <ul class="sub-menu" style="display: <?php if( $segment=='manage-category' || $segment=='home-category'){ echo 'block';}else{ echo 'none';} ?>">
                    <li class="<?php if( $segment=='manage-category'){ echo 'active';}?>"><a href="{{route('admin::manageCategory')}}">Categories</a></li>
                    <li class="<?php if( $segment=='home-category'){ echo 'active';}?>"><a href="{{route('admin::homeCategory')}}">Home Categories</a></li>
                </ul>
            </li>

            <li class="<?php if($segment=='manage-brand' ){ echo 'active';}?>"><a href="{{route('admin::manageBrand')}}"><i class="fa fa-list"></i> <span>Brand</span></a></li>

            <li class="<?php if($segment=='manage-discount' ){ echo 'active';}?>"><a href="{{route('admin::manageDiscount')}}"><i class="fa fa-money"></i> <span>Discount</span></a></li>
            <li class="<?php if($segment=='manage-setting-delivery' ){ echo 'active';}?>"><a href="{{route('admin::manageDeliverySetting')}}"><i class="fa fa-cogs"></i> <span>Delivery Setting</span></a></li>

            <li class="<?php if($segment=='manage-department' ){ echo 'active';}?>"><a href="{{route('admin::manageDepartment')}}"><i class="fa fa-arrow-circle-down"></i> <span>Department</span></a></li>

            <li class="<?php if($segment=='manage-unit' ){ echo 'active';}?>"><a href="{{route('admin::manageUnit')}}"><i class="fa fa-list"></i> <span>Unit</span></a></li>

            <li class="<?php if($segment=='manage-tax'  || $segment=='manage-tax-value' ){ echo 'active';}?>"><a href="{{route('admin::manageTax')}}"><i class="fa fa-money"></i> <span>Tax</span></a></li>

            <li class="<?php if($segment=='manage-product'){ echo 'active';}?>"><a href="{{route('admin::manageProduct')}}"><i class="fa fa-product-hunt"></i> <span>Product</span></a></li>
            <li class="<?php if($segment=='manage-faq'){ echo 'active';}?>"><a href="{{route('admin::manageFaq')}}"><i class="fa fa-calendar"></i> <span>Manage Faq</span></a></li>
            <li class="has-sub <?php if($segment=='manage-counatct-us-page' || $segment=='manage-about-us-page'){ echo 'active expand';}?>">

                <a href="javascript:;">

                    <b class="caret pull-right"></b>

                    <i class="fa fa-list"></i>

                    <span>Pages</span>

                </a>

                <ul class="sub-menu" style="display: <?php if( $segment=='manage-counatct-us-page' || $segment=='manage-about-us-page'){ echo 'block';}else{ echo 'none';} ?>">
                    <li class="<?php if( $segment=='manage-counatct-us-page'){ echo 'active';}?>"><a href="{{route('admin::manageContactUsPage')}}">Contact Us</a></li>
                    <li class="<?php if( $segment=='manage-about-us-page'){ echo 'active';}?>"><a href="{{route('admin::manageAboutUsPage')}}">About Us</a></li>
                    <li class="<?php if( $segment=='manage-terms-condition-page'){ echo 'active';}?>"><a href="{{route('admin::manageTermsConditionPage')}}">Terms & Conditions</a></li>
                </ul>
            </li>
            <li class="<?php if($segment=='manage-setting' ){ echo 'active';}?>"><a href="{{route('admin::manageSetting')}}"><i class="fa fa-calendar"></i> <span>Setting</span></a></li>
            <li class="<?php if($segment=='manage-notification' ){ echo 'active';}?>"><a href="{{route('admin::manageNotification')}}"><i class="fa fa-calendar"></i> <span>Send Notification</span></a></li>
            <li class="<?php if($segment=='manage-delivery-slot' ){ echo 'active';}?>"><a href="{{route('admin::manageDeliverySlot')}}"><i class="fa fa-calendar"></i> <span>Manage Delivery Slots</span></a></li>
            <li class="<?php if($segment=='manage-promocode' ){ echo 'active';}?>"><a href="{{route('admin::managePromoCode')}}"><i class="fa fa-calendar"></i> <span>Manage PromoCodes</span></a></li>
            <li class="<?php if($segment=='manage-seo-data' ){ echo 'active';}?>"><a href="{{route('admin::manageSeoData')}}"><i class="fa fa-calendar"></i> <span>Manage Seo Data</span></a></li>
            <li class="<?php if($segment=='manage-become-seller'){ echo 'active';}?>"><a href="{{route('admin::manageBecomeSeller')}}"><i class="fa fa-calendar"></i> <span>Manage Become Seller</span></a></li>
            <li class="<?php if($segment=='manage-contact-messages'){ echo 'active';}?>"><a href="{{route('admin::manageContactMessage')}}"><i class="fa fa-calendar"></i> <span>Manage Contact Messages</span></a></li>
            <li class="<?php if($segment=='manage-web-info'){ echo 'active';}?>"><a href="{{route('admin::manageWebInfo')}}"><i class="fa fa-info-circle"></i> <span>Manage Web Info</span></a></li>
            @endrole
            @role('manager')
            <li class="has-sub <?php if($segment=='manage-supplier' || $segment=='manage-delivery'){ echo 'active expand';}?>">
                <a href="javascript:;">
                    <b class="caret pull-right"></b>
                    <i class="fa fa-users"></i>
                    <span>All Users</span>
                </a>
                <ul class="sub-menu" style="display: <?php if( $segment=='manage-user'|| $segment=='manage-delivery' || $segment=='manage-manager' || $segment=='manage-supplier'){ echo 'block';}else{ echo 'none';} ?>">
                    <li class="<?php if( $segment=='manage-supplier'){ echo 'active';}?>"><a href="{{route('admin::manageSupplier')}}">Suppliers</a></li>
                    <li class="<?php if( $segment=='manage-delivery'){ echo 'active';}?>"><a href="{{route('admin::manageDelivery')}}">Delivery Boy</a></li>
                </ul>
            </li>
            <li class="has-sub <?php if($segment=='manage-order' || $segment=='view-order-details'){ echo 'active expand';}?>">
                <a href="javascript:;">
                    <b class="caret pull-right"></b>
                    <i class="fa fa-first-order"></i>
                    <span>Orders</span>
                </a>
                <ul class="sub-menu" style="display: <?php if( $segment=='manage-order' || $segment=='view-order-details'){ echo 'block';}else{ echo 'none';} ?>">
                    <li class="<?php if( $segment=='manage-order' || $segment=='view-order-details'){ echo 'active';}?>"><a href="{{route('admin::manageOrder')}}">Customer Orders</a></li>
                </ul>
            </li>
            <li class="has-sub <?php if($segment=='manage-supplier-product' || $segment=='manage-supplier-sale' || $segment=='manage-pending-order' || $segment=='manage-cancel-order' || $segment=='manage-delivery-order' || $segment=='manage-day-end-report' || $segment=='manage-delivery-boy-report' || $segment=='manage-delivery-boy-report-details' || $segment=='manage-reject-order'){ echo 'active expand';}?>">

                <a href="javascript:;">

                    <b class="caret pull-right"></b>

                    <i class="fa fa-telegram"></i>

                    <span>Reports</span>

                </a>

                <ul class="sub-menu" style="display: <?php if( $segment=='manage-supplier-product' || $segment=='manage-supplier-sale' || $segment=='manage-pending-order' || $segment=='manage-cancel-order' || $segment=='manage-delivery-order' || $segment=='manage-day-end-report' || $segment=='manage-delivery-boy-report' || $segment=='manage-delivery-boy-report-details' || $segment=='manage-reject-order'){ echo 'block';}else{ echo 'none';} ?>">

                    <li class="<?php if( $segment=='manage-supplier-product'){ echo 'active';}?>"><a href="{{route('admin::manageSupplierProduct')}}">Supplier's Product</a></li>

                    <li class="<?php if( $segment=='manage-supplier-sale'){ echo 'active';}?>"><a href="{{route('admin::manageSupplierSale')}}">Supplier's Sale</a></li>

                    <li class="<?php if( $segment=='manage-pending-order'){ echo 'active';}?>"><a href="{{route('admin::managePendingOrder')}}">Pending Order</a></li>

                    <li class="<?php if( $segment=='manage-cancel-order'){ echo 'active';}?>"><a href="{{route('admin::manageCancelOrder')}}">Cancel Order</a></li>

                    <li class="<?php if( $segment=='manage-delivery-order'){ echo 'active';}?>"><a href="{{route('admin::manageDeliveryOrder')}}">Delivery Order</a></li>

                    <li class="<?php if( $segment=='manage-reject-order'){ echo 'active';}?>"><a href="{{route('admin::manageRejectOrder')}}">Reject Order</a></li>

                    <li class="<?php if( $segment=='manage-day-end-report'){ echo 'active';}?>"><a href="{{route('admin::manageDayEndReport')}}">Day End Report</a></li>

                    <li class="<?php if( $segment=='manage-delivery-boy-report' || $segment=='manage-delivery-boy-report-details'){ echo 'active';}?>"><a href="{{route('admin::manageDeliveryBoyReport')}}">Delivery Boy Report</a></li>

                </ul>

            </li>
            @endrole
            @role('supplier')
            <li class="<?php if($segment=='manage-admin-product' ){ echo 'active';}?>"><a href="{{route('admin::manageAdminProduct')}}"><i class="fa fa-calendar"></i> <span>Products</span></a></li>
            <li class="<?php if($segment=='manage-my-product' ){ echo 'active';}?>"><a href="{{route('admin::manageMyProduct')}}"><i class="fa fa-calendar"></i> <span>My Products</span></a></li>
            @if(\Illuminate\Support\Facades\Auth::user()->plan_type=='Commision')
                <li class="<?php if($segment=='manage-supplier-earning' ){ echo 'active';}?>"><a href="{{route('admin::manageSupplierEarning')}}"><i class="fa fa-calendar"></i> <span>Supplier Earning</span></a></li>
            @endif
            @endrole
            <!-- begin sidebar minify button -->
            <li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i class="fa fa-angle-double-left"></i></a></li>

            <!-- end sidebar minify button -->

        </ul>

        <!-- end sidebar nav -->

    </div>

    <!-- end sidebar scrollbar -->

</div>
