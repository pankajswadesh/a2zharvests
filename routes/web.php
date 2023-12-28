<?php


Route::get('page-unauthorized', ['as' => 'unauthorized', 'uses' => 'Frontend\Dashboard\HomeController@unauthorized']);

Route::get('page-forbidden', ['as' => 'forbidden', 'uses' => 'Frontend\Dashboard\HomeController@forbidden']);

Route::get('page-notfound', ['as' => 'notfound', 'uses' => 'Frontend\Dashboard\HomeController@notfound']);

Route::get('internal-server-error', ['as' => 'server_error', 'uses' => 'Frontend\Dashboard\HomeController@server_error']);

Route::get('admin', ['as' => 'admin', 'uses' => 'Admin\AdminController@index']);
Route::get('supplier', ['as' => 'supplier', 'uses' => 'Admin\AdminController@index']);

Route::post('admin-login', ['as' => 'admin_login', 'uses' => 'Admin\AdminController@Check_login']);

Route::get('logout', ['as' => 'logout', 'uses' => 'Admin\AdminController@logout']);

Route::get('privacy', function () {
    return view('frontend.pages.privacy-policy');
});
//Route::get('/', ['as' => 'admin', 'uses' => 'Admin\AdminController@index']);
// Route::get('/', function () {
//     return view('frontend.pages.client_index');
// });


// Get Route For Show Payment Form
Route::get('paywithrazorpay', 'RazorpayController@payWithRazorpay')->name('paywithrazorpay');
// Post Route For Makw Payment Request
Route::post('payment', 'RazorpayController@payment')->name('payment');


/*frontend Routes*/

/*User Login*/
Route::post('user-signup', ['as' => 'user_signup', 'uses' => 'Frontend\LoginController@user_signup']);
Route::post('verify-account', ['as' => 'verify_account', 'uses' => 'Frontend\LoginController@verify_account']);
Route::post('user-login', ['as' => 'user_login', 'uses' => 'Frontend\LoginController@user_login']);
Route::get('user-logout', ['as' => 'user_logout', 'uses' => 'Frontend\LoginController@user_logout']);


Route::get('location-set', ['as' => 'locationSet', 'uses' => 'Frontend\PageController@locationSet']);
Route::get('update-location', ['as' => 'updateLocation', 'uses' => 'Frontend\PageController@updateLocation']);
Route::get('/', ['as' => 'home', 'uses' => 'Frontend\PageController@index']);
Route::get('about-us', ['as' => 'about_us', 'uses' => 'Frontend\PageController@about_us']);
Route::get('contact-us', ['as' => 'contact_us', 'uses' => 'Frontend\PageController@contact_us']);
Route::post('contact-submit', ['as' => 'contactSubmit', 'uses' => 'Frontend\PageController@contactSubmit']);
Route::get('faq', ['as' => 'faq', 'uses' => 'Frontend\PageController@faq']);
Route::get('terms-and-conditions', ['terms_and_condition' => 'faq', 'uses' => 'Frontend\PageController@terms_and_condition']);
Route::get('change-password', ['as' => 'change_password', 'uses' => 'Frontend\PageController@change_password']);
Route::get('manage-address', ['as' => 'manage_address', 'uses' => 'Frontend\PageController@manage_address']);
Route::get('become-a-seller', ['as' => 'becomeSeller', 'uses' => 'Frontend\PageController@becomeSeller']);
Route::post('become-a-seller-submit', ['as' => 'becomeSellerSubmit', 'uses' => 'Frontend\PageController@becomeSellerSubmit']);


Route::get('categories', ['as' => 'categories', 'uses' => 'Frontend\PageController@categories']);
Route::get('sub-categories/{slug}', ['as' => 'sub_categories', 'uses' => 'Frontend\PageController@sub_categories']);
Route::get('products/{category_url?}/{subcategory_url?}', ['as' => 'products', 'uses' => 'Frontend\PageController@products']);
Route::post('product-filter', ['as' => 'product_filter', 'uses' => 'Frontend\PageController@product_filter']);
Route::get('product-details/{supplier_id}/{slug}', ['as' => 'product_details', 'uses' => 'Frontend\PageController@product_details']);
Route::get('vendor-form', ['as' => 'vendor_form', 'uses' => 'Frontend\PageController@vendor_form']);
Route::get('top-seller-products', ['as' => 'top_seller_products', 'uses' => 'Frontend\PageController@top_seller_products']);
Route::post('top-product-filter', ['as' => 'top_product_filter', 'uses' => 'Frontend\PageController@top_product_filter']);
Route::post('newsletter-subscribe', ['as' => 'newsletter_subscribe', 'uses' => 'Frontend\PageController@newsletter_subscribe']);
Route::post('get-suggestion', ['as' => 'get_suggestion', 'uses' => 'Frontend\PageController@get_suggestion']);

Route::get('cart', ['as' => 'cart', 'uses' => 'Frontend\PageController@cart']);
Route::post('add-to-cart', ['as' => 'add_to_cart', 'uses' => 'Frontend\CartController@add_to_cart']);
Route::post('update-cart', ['as' => 'update_cart', 'uses' => 'Frontend\CartController@update_cart']);
Route::get('remove-cart/{cart_id}', ['as' => 'remove_cart', 'uses' => 'Frontend\CartController@remove_cart']);
Route::get('manage-address', ['as' => 'manageAddress', 'uses' => 'Frontend\PageController@manageAddress']);
Route::post('save-shipping-address', ['as' => 'saveShippingAddress', 'uses' => 'Frontend\PageController@saveShippingAddress']);
Route::get('checkout', ['as' => 'checkout', 'uses' => 'Frontend\PageController@checkout']);
Route::get('time-slot', ['as' => 'timeSlot', 'uses' => 'Frontend\PageController@timeSlot']);
Route::post('check-slot', ['as' => 'checkSlot', 'uses' => 'Frontend\PageController@checkSlot']);
Route::post('place-order', ['as' => 'place_order', 'uses' => 'Frontend\OrderController@place_order']);
Route::post('pay-order-payment', ['as' => 'pay_order_payment', 'uses' => 'Frontend\OrderController@pay_order_payment']);
Route::post('initiate-transaction', ['as' => 'initiate_transaction', 'uses' => 'Frontend\OrderController@initiate_transaction']);
Route::get('order-confirmation/{order_id}', ['as' => 'orderConfirmation', 'uses' => 'Frontend\OrderController@orderConfirmation']);

Route::group(['as' => 'user::', 'prefix' => 'user', 'middleware' => ['web', 'UserMiddleware']], function () {
    Route::get('my-account', ['as' => 'my_account', 'uses' => 'Frontend\UserController@my_account']);
    Route::post('update-profile', ['as' => 'update_profile', 'uses' => 'Frontend\UserController@update_profile']);
    Route::post('update-shipping', ['as' => 'update_shipping', 'uses' => 'Frontend\UserController@update_shipping']);
    Route::post('update-password', ['as' => 'update_password', 'uses' => 'Frontend\UserController@update_password']);
});
Route::post('pay-to-wallet', ['as' => 'pay_to_wallet', 'uses' => 'Frontend\UserController@pay_to_wallet']);



