<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['prefix' => 'v1'], function () {
    /* User api route start here */
    /* Login Register Route start here*/
    Route::post('/register', ['as' => 'register', 'uses' => 'api\v1\Registration\RegistrationController@signup']);
    Route::post('/verify-account', ['as' => 'verify_account', 'uses' => 'api\v1\Registration\RegistrationController@verify_account']);
    Route::get('/verify/{id}', ['as' => 'verify', 'uses' => 'api\v1\Registration\RegistrationController@verify']);
    Route::post('/login', ['as' => 'login', 'uses' => 'api\v1\Registration\RegistrationController@signin']);
    Route::post('/forget-password', ['as' => 'forget_password', 'uses' => 'api\v1\Registration\RegistrationController@forget_password']);
    Route::get('/check-otp/{token}', ['as' => 'check_otp', 'uses' => 'api\v1\Registration\RegistrationController@check_otp']);
    Route::post('/reset-password', ['as' => 'reset_password', 'uses' => 'api\v1\Registration\RegistrationController@reset_password']);
    /* Login Register Route end here*/
    /* User api route end here *

    /* Supplier api route start here */
    Route::post('/delivery-login', ['as' => 'deliveryLogin', 'uses' => 'api\v1\Registration\RegistrationController@deliverySignin']);
    /* Supplier api route end here */

    /* Supplier api route start here */
    Route::post('/supplier-registration', ['as' => 'supplierRegister', 'uses' => 'api\v1\Registration\RegistrationController@supplierSignup']);
    Route::post('/supplier-login', ['as' => 'supplierLogin', 'uses' => 'api\v1\Registration\RegistrationController@supplierSignin']);

    /* Supplier api route end here */

    /* Page api route start here */
    Route::get('/get-contact-us-page', ['as' => 'contactUsPage', 'uses' => 'api\v1\Page\PageController@contactUsPage']);
    Route::get('/get-about-us-page', ['as' => 'aboutUsPage', 'uses' => 'api\v1\Page\PageController@aboutUsPage']);
    /* Page api route end here */
    /*version fetched */
    Route::get('/get-delivery-version', ['as' => 'getDeliveryVersion', 'uses' => 'api\v1\Page\PageController@getDeliveryVersion']);
    Route::get('/get-customer-version', ['as' => 'getCustomerVersion', 'uses' => 'api\v1\Page\PageController@getCustomerVersion']);
    Route::get('/get-supplier-version', ['as' => 'getSupplierVersion', 'uses' => 'api\v1\Page\PageController@getSupplierVersion']);

    /* Banner Route Start*/
    Route::get('/get-sliders', ['as' => 'get_sliders', 'uses' => 'api\v1\User\Slider\SliderController@get_sliders']);
    Route::get('/get-text-banner', ['as' => 'get_text_banner', 'uses' => 'api\v1\User\Slider\SliderController@get_text_banner']);
    Route::get('/get-image-banner', ['as' => 'get_image_banner', 'uses' => 'api\v1\User\Slider\SliderController@get_image_banner']);
    Route::get('/get-image-advertisement', ['as' => 'get_image_advertisement', 'uses' => 'api\v1\User\Slider\SliderController@get_image_advertisement']);
    /* Banner Route Start*/

    Route::get('/get-categories', ['as' => 'get_category', 'uses' => 'api\v1\Vendor\Product\ProductController@get_category']);
    Route::get('/get-sub-categories/{category_id}', ['as' => 'get_sub_category', 'uses' => 'api\v1\Vendor\Product\ProductController@get_sub_category']);

    Route::post('/get-products-v2', ['as' => 'get_products_v2', 'uses' => 'api\v1\User\Product\ProductController@get_products_v2']);
    Route::post('/get-top-seller-products-v2', ['as' => 'get_top_seller_products_v2', 'uses' => 'api\v1\User\Product\ProductController@get_top_seller_products_v2']);
    Route::post('/get-home-category-data', ['as' => 'get_home_category', 'uses' => 'api\v1\User\Product\ProductController@get_home_category']);
});

Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {
    /* User api route start here */
    Route::post('/get-user-data', ['as' => 'get_user_data', 'middleware' => ['role:user|supplier|delivery'], 'uses' => 'api\v1\User\UserdataController@get_user_data']);
    Route::post('/update-profile', ['as' => 'update_profile', 'middleware' => ['role:user|supplier|delivery'], 'uses' => 'api\v1\User\UserdataController@update_profile']);
    Route::post('/update-location', ['as' => 'update_location', 'middleware' => ['role:user|supplier|delivery'], 'uses' => 'api\v1\User\UserdataController@update_location']);
    Route::post('/update-password', ['as' => 'update_password', 'middleware' => ['role:user|supplier|delivery'], 'uses' => 'api\v1\User\UserdataController@update_password']);
    Route::post('/get-products', ['as' => 'get_products', 'middleware' => ['role:user|supplier'], 'uses' => 'api\v1\User\Product\ProductController@get_products']);
    Route::post('/get-top-seller-products', ['as' => 'get_top_seller_products', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Product\ProductController@get_top_seller_products']);
    Route::post('/get-simmilar-product', ['as' => 'get_simmilar_product', 'middleware' => ['role:user|supplier'], 'uses' => 'api\v1\User\Product\ProductController@get_simmilar_product']);
    Route::post('/get-recent-products', ['as' => 'get_recent_products', 'middleware' => ['role:user|supplier'], 'uses' => 'api\v1\User\Product\ProductController@recentSearches']);

    Route::post('/user-logout', ['as' => 'user_logout', 'middleware' => ['role:user|supplier|delivery'], 'uses' => 'api\v1\User\UserdataController@user_logout']);
    Route::post('/add-money-to-wallet', ['as' => 'add_money_to_wallet', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\UserdataController@add_money_to_wallet']);
    Route::post('/get-suggestion', ['middleware' => ['role:user'], 'uses' => 'api\v1\User\Product\ProductController@get_suggestion']);



    /* Cart Route Start*/
    Route::post('/add-to-cart', ['as' => 'add_to_cart', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Cart\CartController@add_to_cart']);
    Route::post('/update-cart', ['as' => 'update_cart', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Cart\CartController@update_cart']);
    Route::post('/remove-cart', ['as' => 'remove_cart', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Cart\CartController@remove_cart']);
    Route::post('/destroy-cart', ['as' => 'destroy_cart', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Cart\CartController@destroy_cart']);
    Route::post('/get-cart-details', ['as' => 'get_cart_details', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Cart\CartController@get_cart_details']);
    Route::post('/get-check-out-cart-details', ['as' => 'get_check_out_cart_details', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Cart\CartController@get_check_out_cart_details']);
    /* Cart Route End*/

    /*Shipping Route Start*/
    Route::post('/save-shipping', ['as' => 'save_shipping', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Order\OrderController@save_shipping']);
    Route::post('/get-prev-shipping-details', ['as' => 'get_prev_shipping_details', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Order\OrderController@get_prev_shipping_details']);
    Route::get('/get-next-7-days',  'api\v1\User\UserdataController@getNext7Days');
    Route::post('/check-slot',  'api\v1\User\UserdataController@checkSlot');
    /*Shipping Route End*/

    /*Order Route Start*/
    Route::post('/place-order', ['as' => 'place_order', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Order\OrderController@place_order']);
    Route::post('/get-user-order-history', ['as' => 'get_user_order_history', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Order\OrderController@get_user_order_history']);
    Route::post('/user-cancel-order-item', ['as' => 'user_cancel_order_item', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Order\OrderController@user_cancel_order_item']);
    Route::post('/user-cancel-order', ['as' => 'user_cancel_order', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Order\OrderController@user_cancel_order']);
    Route::post('/delivery-boy-review-rateing', ['as' => 'delivery_boy_review_rateing', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Order\OrderController@delivery_boy_review_rateing']);
    Route::post('/delivery-boy-tips', ['as' => 'delivery_boy_tips', 'middleware' => ['role:user'], 'uses' => 'api\v1\User\Order\OrderController@delivery_boy_tips']);
    /*Order Route End*/

    /* User api route end here */
    /* Supplier api route start here */
    Route::get('/get-supplier-shop-details', ['as' => 'get_supplier_shop_details', 'middleware' => ['role:supplier'], 'uses' => 'api\v1\User\UserdataController@get_supplier_shop_details']);
    Route::get('/get-supplier-bank-details', ['as' => 'get_supplier_bank_details', 'middleware' => ['role:supplier'], 'uses' => 'api\v1\User\UserdataController@get_supplier_bank_details']);
    Route::post('/update-supplier-shop-details', ['as' => 'update_supplier_shop_details', 'middleware' => ['role:supplier'], 'uses' => 'api\v1\User\UserdataController@update_supplier_shop_details']);
    Route::post('/update-supplier-bank-details', ['as' => 'update_supplier_bank_details', 'middleware' => ['role:supplier'], 'uses' => 'api\v1\User\UserdataController@update_supplier_bank_details']);

    Route::get('/get-product-list/{sub_category_id}', ['as' => 'get_product_list', 'middleware' => ['role:supplier'], 'uses' => 'api\v1\Vendor\Product\ProductController@get_product_list']);
    Route::get('/get-discount', ['as' => 'get_discount', 'middleware' => ['role:supplier'], 'uses' => 'api\v1\Vendor\Product\ProductController@get_discount']);
    Route::post('/vendor-product-mapped', ['as' => 'vendor_product_mapped', 'middleware' => ['role:supplier'], 'uses' => 'api\v1\Vendor\Product\ProductController@vendor_product_mapped']);
    Route::post('/get-vendor-product-list', ['as' => 'get_vendor_product_list', 'middleware' => ['role:supplier'], 'uses' => 'api\v1\Vendor\Product\ProductController@get_vendor_product_list']);
    Route::post('/update-vendor-product', ['as' => 'update_vendor_product', 'middleware' => ['role:supplier'], 'uses' => 'api\v1\Vendor\Product\ProductController@update_vendor_product']);
    Route::post('/update-vendor-product-status', ['as' => 'update_vendor_product_status', 'middleware' => ['role:supplier'], 'uses' => 'api\v1\Vendor\Product\ProductController@update_vendor_product_status']);
    Route::post('/get-vendor-order-history', ['as' => 'get_vendor_order_history', 'middleware' => ['role:supplier'], 'uses' => 'api\v1\Vendor\Order\OrderController@get_vendor_order_history']);
    Route::post('/vendor-accept-order-item', ['as' => 'vendor_accept_order_item', 'middleware' => ['role:supplier'], 'uses' => 'api\v1\Vendor\Order\OrderController@vendor_accept_order_item']);
    Route::post('/vendor-reject-order-item', ['as' => 'vendor_reject_order_item', 'middleware' => ['role:supplier'], 'uses' => 'api\v1\Vendor\Order\OrderController@vendor_reject_order_item']);
    Route::post('/get-vendor-earning-amount', ['as' => 'get_vendor_earning_amount', 'middleware' => ['role:supplier'], 'uses' => 'api\v1\Vendor\Order\OrderController@get_vendor_earning_amount']);
    /* Supplier api route end here */

    /* Delivery api route end here */
    Route::post('/get-delivery-pending-order', ['as' => 'get_delivery_pending_order', 'middleware' => ['role:delivery'], 'uses' => 'api\v1\Delivery\Order\OrderController@get_delivery_pending_order']);
    Route::post('/update-delivery-pending-order-item-status', ['as' => 'update_delivery_pending_order_item_status', 'middleware' => ['role:delivery'], 'uses' => 'api\v1\Delivery\Order\OrderController@update_delivery_pending_order_item_status']);
    Route::post('/get-delivery-pick-up-order', ['as' => 'get_delivery_pick_up_order', 'middleware' => ['role:delivery'], 'uses' => 'api\v1\Delivery\Order\OrderController@get_delivery_pick_up_order']);
    Route::post('/update-delivery-pick-up-order-item-status', ['as' => 'update_delivery_pick_up_order_item_status', 'middleware' => ['role:delivery'], 'uses' => 'api\v1\Delivery\Order\OrderController@update_delivery_pick_up_order_item_status']);
    Route::post('/get-delivery-order-history', ['as' => 'get_delivery_order_history', 'middleware' => ['role:delivery'], 'uses' => 'api\v1\Delivery\Order\OrderController@get_delivery_order_history']);
    Route::post('/get-delivery-earning-amount', ['as' => 'get_delivery_earning_amount', 'middleware' => ['role:delivery'], 'uses' => 'api\v1\Delivery\Order\OrderController@get_delivery_earning_amount']);
    Route::post('/update-customer-delivery-date', ['as' => 'update_customer_delivery_date', 'middleware' => ['role:delivery'], 'uses' => 'api\v1\Delivery\Order\OrderController@update_customer_delivery_date']);
    /* Delivery api route end here */

    /* Paytm Payment Start */
    Route::post('/initiate-transaction', ['as' => 'initiate_transaction', 'middleware' => ['role:user|supplier|delivery'], 'uses' => 'api\v1\Paytm\PaymentController@initiate_transaction']);
    Route::post('/callback-transaction', ['as' => 'callback_transaction', 'middleware' => ['role:user|supplier|delivery'], 'uses' => 'api\v1\Paytm\PaymentController@callback_transaction']);
    /* Paytm Payment End */
});


Route::post('add-amount', ['as' => 'add_amount', 'uses' => 'Frontend\UserController@add_amount']);
Route::post('online-place-order', ['as' => 'online_place_order', 'uses' => 'Frontend\OrderController@online_place_order']);
