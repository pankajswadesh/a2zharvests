<?php
Route::group(['as'=>'admin::','prefix'=>'cpanel/admin','middleware' => ['web','AdminMiddleWare','revalidate']], function () {

    Route::get('/dashboard', ['as' => 'dashboard','middleware' => ['role:admin|manager|supplier'], 'uses' => 'Admin\Dashboard\DashboardController@dashboard']);
    Route::get('changePassForm', ['as' => 'changePassForm','middleware' => ['role:admin|manager|supplier'], 'uses' => 'Admin\Dashboard\DashboardController@changePassForm']);
    Route::post('ChangePass', ['as' => 'ChangePass','middleware' => ['role:admin|manager|supplier'], 'uses' => 'Admin\Dashboard\DashboardController@ChangePass']);
    Route::get('profile/{id}', ['as' => 'profile','middleware' => ['role:admin|manager|supplier'], 'uses' => 'Admin\Dashboard\DashboardController@profile']);
    Route::post('update-profile', ['as' => 'updateProfile','middleware' => ['role:admin|manager|supplier'], 'uses' => 'Admin\Dashboard\DashboardController@updateProfile']);

    /* Users route start*/

    Route::get('manage-user', ['as' => 'manageUser', 'middleware' => ['role:admin'], 'uses' => 'Admin\User\UserController@index']);
    Route::get('add-user', ['as' => 'addUser', 'middleware' => ['role:admin'], 'uses' => 'Admin\User\UserController@addUser']);
    Route::post('save-user', ['as' => 'saveUser','middleware' => ['role:admin'], 'uses' => 'Admin\User\UserController@saveUser']);
    Route::get('/edit-user/{id}', ['as' => 'editUser', 'middleware' => ['role:admin'], 'uses' => 'Admin\User\UserController@editUser']);
    Route::post('/update-user', ['as' => 'updateUser', 'middleware' => ['role:admin'], 'uses' => 'Admin\User\UserController@updateUser']);
    Route::get('/del-user/{id}', ['as' => 'delUser', 'middleware' => ['role:admin'], 'uses' => 'Admin\User\UserController@delUser']);
    Route::post('active_inactive_user', ['as' => 'active_inactive_user', 'middleware' => ['role:admin'], 'uses' => 'Admin\User\UserController@active_inactive_user']);

    /* Users route end*/

    /* Users route start*/
    Route::get('manage-supplier', ['as' => 'manageSupplier', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Supplier\SupplierController@index']);
    Route::get('add-supplier', ['as' => 'addSupplier', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Supplier\SupplierController@addSupplier']);
    Route::post('save-supplier', ['as' => 'saveSupplier','middleware' => ['role:admin|manager'], 'uses' => 'Admin\Supplier\SupplierController@saveSupplier']);
    Route::get('/edit-supplier/{id}', ['as' => 'editSupplier', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Supplier\SupplierController@editSupplier']);
    Route::post('/update-supplier', ['as' => 'updateSupplier', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Supplier\SupplierController@updateSupplier']);
    Route::get('/del-supplier/{id}', ['as' => 'delSupplier', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Supplier\SupplierController@delSupplier']);
    Route::post('active_inactive_supplier', ['as' => 'active_inactive_supplier', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Supplier\SupplierController@active_inactive_supplier']);
    Route::get('view-supplier/{id}', ['as' => 'viewSupplier', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Supplier\SupplierController@viewSupplier']);
    /* Users route end*/
    /* Managers route start*/
    Route::get('manage-manager', ['as' => 'manageManager', 'middleware' => ['role:admin'], 'uses' => 'Admin\Manager\ManagerController@index']);
    Route::get('add-manager', ['as' => 'addManager', 'middleware' => ['role:admin'], 'uses' => 'Admin\Manager\ManagerController@add']);
    Route::post('save-manager', ['as' => 'saveManager','middleware' => ['role:admin'], 'uses' => 'Admin\Manager\ManagerController@save']);
    Route::get('/edit-manager/{id}', ['as' => 'editManager', 'middleware' => ['role:admin'], 'uses' => 'Admin\Manager\ManagerController@edit']);
    Route::post('/update-manager', ['as' => 'updateManager', 'middleware' => ['role:admin'], 'uses' => 'Admin\Manager\ManagerController@update']);
    Route::get('/del-manager/{id}', ['as' => 'delManager', 'middleware' => ['role:admin'], 'uses' => 'Admin\Manager\ManagerController@delete']);
    Route::post('active_inactive_manager', ['as' => 'active_inactive_manager', 'middleware' => ['role:admin'], 'uses' => 'Admin\Manager\ManagerController@active_inactive_manager']);
    Route::get('manager-suppliers/{id}', ['as' => 'managerSupplier', 'middleware' => ['role:admin'], 'uses' => 'Admin\Manager\ManagerController@managerSupplier']);
    Route::get('manager-delivery/{id}', ['as' => 'managerDelivery', 'middleware' => ['role:admin'], 'uses' => 'Admin\Manager\ManagerController@managerDelivery']);

    Route::get('assign-delivery/{id}', ['as' => 'assignDelivery', 'middleware' => ['role:admin'], 'uses' => 'Admin\Manager\ManagerController@assignDelivery']);
    Route::post('/assign-delivery-submit/{id}', ['as' => 'assignDeliverySubmit', 'middleware' => ['role:admin'], 'uses' => 'Admin\Manager\ManagerController@assignDeliverySubmit']);

    Route::get('assign-suppliers/{id}', ['as' => 'assignSupplier', 'middleware' => ['role:admin'], 'uses' => 'Admin\Manager\ManagerController@assignSupplier']);
    Route::post('/assign-suppliers-submit/{id}', ['as' => 'assignSupplierSubmit', 'middleware' => ['role:admin'], 'uses' => 'Admin\Manager\ManagerController@assignSupplierSubmit']);
    /* Managers route end*/

    /* Delivery Boy route start*/

    Route::get('manage-delivery', ['as' => 'manageDelivery', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Delivery\DeliveryController@index']);
    Route::get('add-delivery', ['as' => 'addDelivery', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Delivery\DeliveryController@addDelivery']);
    Route::post('save-delivery', ['as' => 'saveDelivery','middleware' => ['role:admin|manager'], 'uses' => 'Admin\Delivery\DeliveryController@saveDelivery']);
    Route::get('/edit-delivery/{id}', ['as' => 'editDelivery', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Delivery\DeliveryController@editDelivery']);
    Route::post('/update-delivery', ['as' => 'updateDelivery', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Delivery\DeliveryController@updateDelivery']);
    Route::get('/del-delivery/{id}', ['as' => 'delDelivery', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Delivery\DeliveryController@delDelivery']);
    Route::post('active_inactive_delivery', ['as' => 'active_inactive_delivery', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Delivery\DeliveryController@active_inactive_delivery']);
    Route::get('view-delivery/{id}', ['as' => 'viewDelivery', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Delivery\DeliveryController@viewDelivery']);

    /* Delivery Boy route end*/

    /* Role route start*/

    Route::get('manage-role', ['as' => 'manageRole', 'middleware' => ['role:admin'], 'uses' => 'Admin\Role\RoleController@index']);
    Route::get('add-role', ['as' => 'addRole', 'middleware' => ['role:admin'], 'uses' => 'Admin\Role\RoleController@addRole']);
    Route::post('save-role', ['as' => 'saveRole','middleware' => ['role:admin'], 'uses' => 'Admin\Role\RoleController@saveRole']);
    Route::get('/edit-role/{id}', ['as' => 'editRole', 'middleware' => ['role:admin'], 'uses' => 'Admin\Role\RoleController@editRole']);
    Route::post('/update-role', ['as' => 'updateRole', 'middleware' => ['role:admin'], 'uses' => 'Admin\Role\RoleController@updateRole']);
    Route::get('/del-role/{id}', ['as' => 'delRole', 'middleware' => ['role:admin'], 'uses' => 'Admin\Role\RoleController@delRole']);

 /* Role route end*/

    /* Category route start*/

    Route::get('manage-category', ['as' => 'manageCategory', 'middleware' => ['role:admin'], 'uses' => 'Admin\Category\CategoryController@index']);
    Route::get('add-category', ['as' => 'addCategory', 'middleware' => ['role:admin'], 'uses' => 'Admin\Category\CategoryController@addCategory']);
    Route::post('save-category', ['as' => 'saveCategory','middleware' => ['role:admin'], 'uses' => 'Admin\Category\CategoryController@saveCategory']);
    Route::get('/edit-category/{id}', ['as' => 'editCategory', 'middleware' => ['role:admin'], 'uses' => 'Admin\Category\CategoryController@editCategory']);
    Route::post('/update-category', ['as' => 'updateCategory', 'middleware' => ['role:admin'], 'uses' => 'Admin\Category\CategoryController@updateCategory']);
    Route::get('/del-category/{id}', ['as' => 'delCategory', 'middleware' => ['role:admin'], 'uses' => 'Admin\Category\CategoryController@delCategory']);
    Route::post('active-inactive-category', ['as' => 'active_inactive_category', 'middleware' => ['role:admin'], 'uses' => 'Admin\Category\CategoryController@active_inactive_category']);

    /* Category route end*/
    /* Home Category route start*/
    Route::get('home-category', ['as' => 'homeCategory', 'middleware' => ['role:admin'], 'uses' => 'Admin\Category\CategoryController@homeCategory']);
    Route::post('in-home', ['as' => 'CategoryInHome','middleware' => ['role:admin'], 'uses' => 'Admin\Category\CategoryController@CategoryInHome']);
    Route::post('category-priority', ['as' => 'CategoryPriority','middleware' => ['role:admin'], 'uses' => 'Admin\Category\CategoryController@CategoryPriority']);
    /* Home Category route end*/
    /* Faq route start*/
    Route::get('manage-faq', ['as' => 'manageFaq', 'middleware' => ['role:admin'], 'uses' => 'Admin\Faq\FaqController@index']);
    Route::get('add-faq', ['as' => 'addFaq', 'middleware' => ['role:admin'], 'uses' => 'Admin\Faq\FaqController@add']);
    Route::post('save-faq', ['as' => 'saveFaq','middleware' => ['role:admin'], 'uses' => 'Admin\Faq\FaqController@save']);
    Route::get('/edit-faq/{id}', ['as' => 'editFaq', 'middleware' => ['role:admin'], 'uses' => 'Admin\Faq\FaqController@edit']);
    Route::post('/update-faq/{id}', ['as' => 'updateFaq', 'middleware' => ['role:admin'], 'uses' => 'Admin\Faq\FaqController@update']);
    Route::get('/del-faq/{id}', ['as' => 'delFaq', 'middleware' => ['role:admin'], 'uses' => 'Admin\Faq\FaqController@delete']);
    Route::post('active-inactive-faq', ['as' => 'active_inactive_faq', 'middleware' => ['role:admin'], 'uses' => 'Admin\Faq\FaqController@active_inactive_faq']);
    /* Faq route end*/

    /* Brand route start*/

    Route::get('manage-brand', ['as' => 'manageBrand', 'middleware' => ['role:admin'], 'uses' => 'Admin\Brand\BrandController@index']);
    Route::get('add-brand', ['as' => 'addBrand', 'middleware' => ['role:admin'], 'uses' => 'Admin\Brand\BrandController@addBrand']);
    Route::post('save-brand', ['as' => 'saveBrand','middleware' => ['role:admin'], 'uses' => 'Admin\Brand\BrandController@saveBrand']);
    Route::get('/edit-brand/{id}', ['as' => 'editBrand', 'middleware' => ['role:admin'], 'uses' => 'Admin\Brand\BrandController@editBrand']);
    Route::post('/update-brand', ['as' => 'updateBrand', 'middleware' => ['role:admin'], 'uses' => 'Admin\Brand\BrandController@updateBrand']);
    Route::get('/del-brand/{id}', ['as' => 'delBrand', 'middleware' => ['role:admin'], 'uses' => 'Admin\Brand\BrandController@delBrand']);
    Route::post('active-inactive-brand', ['as' => 'active_inactive_brand', 'middleware' => ['role:admin'], 'uses' => 'Admin\Brand\BrandController@active_inactive_brand']);

    /* Brand route end*/

    /* Department route start*/

    Route::get('manage-department', ['as' => 'manageDepartment', 'middleware' => ['role:admin'], 'uses' => 'Admin\Department\DepartmentController@index']);
    Route::get('add-department', ['as' => 'addDepartment', 'middleware' => ['role:admin'], 'uses' => 'Admin\Department\DepartmentController@addDepartment']);
    Route::post('save-department', ['as' => 'saveDepartment','middleware' => ['role:admin'], 'uses' => 'Admin\Department\DepartmentController@saveDepartment']);
    Route::get('/edit-department/{id}', ['as' => 'editDepartment', 'middleware' => ['role:admin'], 'uses' => 'Admin\Department\DepartmentController@editDepartment']);
    Route::post('/update-department', ['as' => 'updateDepartment', 'middleware' => ['role:admin'], 'uses' => 'Admin\Department\DepartmentController@updateDepartment']);
    Route::get('/del-department/{id}', ['as' => 'delDepartment', 'middleware' => ['role:admin'], 'uses' => 'Admin\Department\DepartmentController@delDepartment']);
    Route::post('active-inactive-department', ['as' => 'active_inactive_department', 'middleware' => ['role:admin'], 'uses' => 'Admin\Department\DepartmentController@active_inactive_department']);

    /* Department route end*/

    /* Unit route start*/

    Route::get('manage-unit', ['as' => 'manageUnit', 'middleware' => ['role:admin'], 'uses' => 'Admin\Unit\UnitController@index']);
    Route::get('add-unit', ['as' => 'addUnit', 'middleware' => ['role:admin'], 'uses' => 'Admin\Unit\UnitController@addUnit']);
    Route::post('save-unit', ['as' => 'saveUnit','middleware' => ['role:admin'], 'uses' => 'Admin\Unit\UnitController@saveUnit']);
    Route::get('/edit-unit/{id}', ['as' => 'editUnit', 'middleware' => ['role:admin'], 'uses' => 'Admin\Unit\UnitController@editUnit']);
    Route::post('/update-unit', ['as' => 'updateUnit', 'middleware' => ['role:admin'], 'uses' => 'Admin\Unit\UnitController@updateUnit']);
    Route::get('/del-unit/{id}', ['as' => 'delUnit', 'middleware' => ['role:admin'], 'uses' => 'Admin\Unit\UnitController@delUnit']);
    Route::post('active-inactive-unit', ['as' => 'active_inactive_unit', 'middleware' => ['role:admin'], 'uses' => 'Admin\Unit\UnitController@active_inactive_unit']);

    /* Unit route end*/

    /* Brand route start*/

    Route::get('manage-discount', ['as' => 'manageDiscount', 'middleware' => ['role:admin'], 'uses' => 'Admin\Discount\DiscountController@index']);
    Route::get('add-discount', ['as' => 'addDiscount', 'middleware' => ['role:admin'], 'uses' => 'Admin\Discount\DiscountController@addDiscount']);
    Route::post('save-discount', ['as' => 'saveDiscount','middleware' => ['role:admin'], 'uses' => 'Admin\Discount\DiscountController@saveDiscount']);
    Route::get('/edit-discount/{id}', ['as' => 'editDiscount', 'middleware' => ['role:admin'], 'uses' => 'Admin\Discount\DiscountController@editDiscount']);
    Route::post('/update-discount', ['as' => 'updateDiscount', 'middleware' => ['role:admin'], 'uses' => 'Admin\Discount\DiscountController@updateDiscount']);
    Route::get('/del-discount/{id}', ['as' => 'delDiscount', 'middleware' => ['role:admin'], 'uses' => 'Admin\Discount\DiscountController@delDiscount']);
    Route::post('active-inactive-discount', ['as' => 'active_inactive_discount', 'middleware' => ['role:admin'], 'uses' => 'Admin\Discount\DiscountController@active_inactive_discount']);

    /* Brand route end*/

    /* Tax route start*/

    Route::get('manage-tax', ['as' => 'manageTax', 'middleware' => ['role:admin'], 'uses' => 'Admin\Tax\TaxController@index']);
    Route::get('add-tax', ['as' => 'addTax', 'middleware' => ['role:admin'], 'uses' => 'Admin\Tax\TaxController@addTax']);
    Route::post('save-tax', ['as' => 'saveTax','middleware' => ['role:admin'], 'uses' => 'Admin\Tax\TaxController@saveTax']);
    Route::get('/edit-tax/{id}', ['as' => 'editTax', 'middleware' => ['role:admin'], 'uses' => 'Admin\Tax\TaxController@editTax']);
    Route::post('/update-tax', ['as' => 'updateTax', 'middleware' => ['role:admin'], 'uses' => 'Admin\Tax\TaxController@updateTax']);
    Route::get('/del-tax/{id}', ['as' => 'delTax', 'middleware' => ['role:admin'], 'uses' => 'Admin\Tax\TaxController@delTax']);
    Route::post('active-inactive-tax', ['as' => 'active_inactive_tax', 'middleware' => ['role:admin'], 'uses' => 'Admin\Tax\TaxController@active_inactive_tax']);

    /* Tax route end*/

    /* Tax Value route start*/

    Route::get('manage-tax-value/{tax_id}', ['as' => 'manageTaxValue', 'middleware' => ['role:admin'], 'uses' => 'Admin\Tax\TaxValueController@index']);
    Route::get('add-tax-value/{tax_id}', ['as' => 'addTaxValue', 'middleware' => ['role:admin'], 'uses' => 'Admin\Tax\TaxValueController@addTaxValue']);
    Route::post('save-tax-value', ['as' => 'saveTaxValue','middleware' => ['role:admin'], 'uses' => 'Admin\Tax\TaxValueController@saveTaxValue']);
    Route::get('/edit-tax-value/{id}', ['as' => 'editTaxValue', 'middleware' => ['role:admin'], 'uses' => 'Admin\Tax\TaxValueController@editTaxValue']);
    Route::post('/update-tax-value', ['as' => 'updateTaxValue', 'middleware' => ['role:admin'], 'uses' => 'Admin\Tax\TaxValueController@updateTaxValue']);
    Route::get('/del-tax-value/{id}/{tax_id}', ['as' => 'delTaxValue', 'middleware' => ['role:admin'], 'uses' => 'Admin\Tax\TaxValueController@delTaxValue']);
    Route::post('active-inactive-tax-value', ['as' => 'active_inactive_tax_value', 'middleware' => ['role:admin'], 'uses' => 'Admin\Tax\TaxValueController@active_inactive_tax_value']);

    /* Tax Value route end*/

    /* Product route start*/

    Route::get('manage-product', ['as' => 'manageProduct', 'middleware' => ['role:admin'], 'uses' => 'Admin\Product\ProductController@index']);
    Route::get('add-product', ['as' => 'addProduct', 'middleware' => ['role:admin'], 'uses' => 'Admin\Product\ProductController@addProduct']);
    Route::post('save-product', ['as' => 'saveProduct','middleware' => ['role:admin'], 'uses' => 'Admin\Product\ProductController@saveProduct']);
    Route::get('/edit-product/{id}', ['as' => 'editProduct', 'middleware' => ['role:admin'], 'uses' => 'Admin\Product\ProductController@editProduct']);
    Route::post('/update-product', ['as' => 'updateProduct', 'middleware' => ['role:admin'], 'uses' => 'Admin\Product\ProductController@updateProduct']);
    Route::get('/del-product/{id}', ['as' => 'delProduct', 'middleware' => ['role:admin'], 'uses' => 'Admin\Product\ProductController@delProduct']);
    Route::post('/bulk-product-delete', ['as' => 'bulk_product_delete', 'middleware' => ['role:admin'], 'uses' => 'Admin\Product\ProductController@bulk_product_delete']);
    Route::post('active-inactive-product', ['as' => 'active_inactive_product', 'middleware' => ['role:admin'], 'uses' => 'Admin\Product\ProductController@active_inactive_product']);
    Route::get('product-image/{id}', ['as' => 'productImage', 'middleware' => ['role:admin'],'uses' => 'Admin\Product\ProductController@productImage']);
    Route::post('save-product-images', ['as' => 'saveProductImages', 'middleware' => ['role:admin'],'uses' => 'Admin\Product\ProductController@saveProductImages']);
    Route::get('del-product-images/{id}', ['as' => 'delProductImages','middleware' => ['role:admin'], 'uses' => 'Admin\Product\ProductController@delProductImages']);
    Route::post('get-subcategory', ['as' => 'get_sub_category','middleware' => ['role:admin|supplier'], 'uses' => 'Admin\Product\ProductController@get_sub_category']);
    Route::post('get-old-subcategory', ['as' => 'get_old_sub_category','middleware' => ['role:admin|supplier'], 'uses' => 'Admin\Product\ProductController@get_old_sub_category']);
    Route::get('import-product', ['as' => 'importProduct','middleware' => ['role:admin'], 'uses' => 'Admin\Product\ProductController@importProduct']);
    Route::post('save-import-product', ['as' => 'saveImportProduct','middleware' => ['role:admin'], 'uses' => 'Admin\Product\ProductController@save_import_product']);
    Route::get('import-image', ['as' => 'importImage','middleware' => ['role:admin'], 'uses' => 'Admin\Product\ProductController@importImage']);
    Route::post('save-import-image', ['as' => 'saveImportImage','middleware' => ['role:admin'], 'uses' => 'Admin\Product\ProductController@saveImportImage']);

    Route::get('manage-admin-product', ['as' => 'manageAdminProduct', 'middleware' => ['role:supplier'], 'uses' => 'Admin\Supplier\SupplierProductController@index']);
    Route::post('/bulk-product-add', ['as' => 'bulk_product_add', 'middleware' => ['role:supplier'], 'uses' => 'Admin\Supplier\SupplierProductController@bulk_product_add']);
    Route::get('/manage-my-product', ['as' => 'manageMyProduct', 'middleware' => ['role:supplier'], 'uses' => 'Admin\Supplier\SupplierProductController@manageMyProduct']);
    Route::post('/bulk-product-update', ['as' => 'bulk_product_update', 'middleware' => ['role:supplier'], 'uses' => 'Admin\Supplier\SupplierProductController@bulk_product_update']);
    Route::post('active-inactive-my-product', ['as' => 'active_inactive_my_product', 'middleware' => ['role:supplier'], 'uses' => 'Admin\Supplier\SupplierProductController@active_inactive_my_product']);
    Route::get('/del-my-product/{id}', ['as' => 'delMyProduct', 'middleware' => ['role:supplier'], 'uses' => 'Admin\Supplier\SupplierProductController@delMyProduct']);
    /* Product route end*/

    /* Product route start*/
    Route::get('manage-order', ['as' => 'manageOrder', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Order\OrderController@index']);
    Route::get('manage-outside-order', ['as' => 'manageOutsideOrder', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Order\OrderController@manageOutsideOrder']);
    Route::get('view-tracking-details/{id}', ['as' => 'viewTrackingDetails', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Order\OrderController@viewTrackingDetails']);
    Route::post('update-tracking-details/{id}', ['as' => 'updateTrackingDetails', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Order\OrderController@updateTrackingDetails']);
    Route::get('view-order-details/{order_id}', ['as' => 'viewOrderDetails', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Order\OrderController@viewOrderDetails']);
    Route::get('view-order-delivery/{order_id}', ['as' => 'viewOrderDelivery', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Order\OrderController@viewOrderDelivery']);
    Route::post('update-order-delivery', ['as' => 'updateOrderDelivery', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Order\OrderController@updateOrderDelivery']);
    Route::get('/del-order/{id}', ['as' => 'delOrder', 'middleware' => ['role:admin'], 'uses' => 'Admin\Order\OrderController@delOrder']);
    /* Product route end*/

    /* Report route start*/
    Route::get('manage-supplier-product', ['as' => 'manageSupplierProduct', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Report\ReportController@manageSupplierProduct']);
    Route::get('manage-supplier-sale', ['as' => 'manageSupplierSale', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Report\ReportController@manageSupplierSale']);
    Route::get('manage-pending-order', ['as' => 'managePendingOrder', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Report\ReportController@managePendingOrder']);
    Route::get('manage-cancel-order', ['as' => 'manageCancelOrder', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Report\ReportController@manageCancelOrder']);
    Route::get('manage-delivery-order', ['as' => 'manageDeliveryOrder', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Report\ReportController@manageDeliveryOrder']);
    Route::get('manage-reject-order', ['as' => 'manageRejectOrder', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Report\ReportController@manageRejectOrder']);
    Route::get('manage-day-end-report', ['as' => 'manageDayEndReport', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Report\ReportController@manageDayEndReport']);
    Route::get('manage-delivery-boy-report', ['as' => 'manageDeliveryBoyReport', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Report\ReportController@manageDeliveryBoyReport']);
    Route::get('manage-delivery-boy-report-details/{date}', ['as' => 'DeliveryBoyReportDetails', 'middleware' => ['role:admin|manager'], 'uses' => 'Admin\Report\ReportController@DeliveryBoyReportDetails']);
    /* Report route end*/

    /* slider route start*/

    Route::get('manage-slider', ['as' => 'manageSlider', 'middleware' => ['role:admin'], 'uses' => 'Admin\Slider\SliderController@index']);
    Route::get('add-slider', ['as' => 'addSlider', 'middleware' => ['role:admin'], 'uses' => 'Admin\Slider\SliderController@addSlider']);
    Route::post('save-slider', ['as' => 'saveSlider','middleware' => ['role:admin'], 'uses' => 'Admin\Slider\SliderController@saveSlider']);
    Route::get('/edit-slider/{id}', ['as' => 'editSlider', 'middleware' => ['role:admin'], 'uses' => 'Admin\Slider\SliderController@editSlider']);
    Route::post('/update-slider', ['as' => 'updateSlider', 'middleware' => ['role:admin'], 'uses' => 'Admin\Slider\SliderController@updateSlider']);
    Route::get('/del-slider/{id}', ['as' => 'delSlider', 'middleware' => ['role:admin'], 'uses' => 'Admin\Slider\SliderController@delSlider']);
    Route::post('active-inactive-slider', ['as' => 'active_inactive_slider', 'middleware' => ['role:admin'], 'uses' => 'Admin\Slider\SliderController@active_inactive_slider']);

    /* slider route end*/

    /* text banner route start*/

    Route::get('manage-text-banner', ['as' => 'manageTextBanner', 'middleware' => ['role:admin'], 'uses' => 'Admin\Banner\TextBannerController@index']);
    Route::get('add-text-banner', ['as' => 'addTextBanner', 'middleware' => ['role:admin'], 'uses' => 'Admin\Banner\TextBannerController@addTextBanner']);
    Route::post('save-text-banner', ['as' => 'saveTextBanner','middleware' => ['role:admin'], 'uses' => 'Admin\Banner\TextBannerController@saveTextBanner']);
    Route::get('/edit-text-banner/{id}', ['as' => 'editTextBanner', 'middleware' => ['role:admin'], 'uses' => 'Admin\Banner\TextBannerController@editTextBanner']);
    Route::post('/update-text-banner', ['as' => 'updateTextBanner', 'middleware' => ['role:admin'], 'uses' => 'Admin\Banner\TextBannerController@updateTextBanner']);
    Route::get('/del-text-banner/{id}', ['as' => 'delTextBanner', 'middleware' => ['role:admin'], 'uses' => 'Admin\Banner\TextBannerController@delTextBanner']);
    Route::post('active-inactive-text-banner', ['as' => 'active_inactive_text_banner', 'middleware' => ['role:admin'], 'uses' => 'Admin\Banner\TextBannerController@active_inactive_text_banner']);

    /* text banner route end*/

    /* image banner route start*/

    Route::get('manage-image-banner', ['as' => 'manageImageBanner', 'middleware' => ['role:admin'], 'uses' => 'Admin\Banner\ImageBannerController@index']);
    Route::get('add-image-banner', ['as' => 'addImageBanner', 'middleware' => ['role:admin'], 'uses' => 'Admin\Banner\ImageBannerController@addImageBanner']);
    Route::post('save-image-banner', ['as' => 'saveImageBanner','middleware' => ['role:admin'], 'uses' => 'Admin\Banner\ImageBannerController@saveImageBanner']);
    Route::get('/edit-image-banner/{id}', ['as' => 'editImageBanner', 'middleware' => ['role:admin'], 'uses' => 'Admin\Banner\ImageBannerController@editImageBanner']);
    Route::post('/update-image-banner', ['as' => 'updateImageBanner', 'middleware' => ['role:admin'], 'uses' => 'Admin\Banner\ImageBannerController@updateImageBanner']);
    Route::get('/del-image-banner/{id}', ['as' => 'delImageBanner', 'middleware' => ['role:admin'], 'uses' => 'Admin\Banner\ImageBannerController@delImageBanner']);
    Route::post('active-inactive-image-banner', ['as' => 'active_inactive_image_banner', 'middleware' => ['role:admin'], 'uses' => 'Admin\Banner\ImageBannerController@active_inactive_image_banner']);

    /* image banner route end*/

    /* Page route start*/
    Route::get('manage-counatct-us-page', ['as' => 'manageContactUsPage', 'middleware' => ['role:admin'], 'uses' => 'Admin\Page\PageController@contactUsPage']);
    Route::get('/edit-counatct-us-page/{id}', ['as' => 'editContactUsPage', 'middleware' => ['role:admin'], 'uses' => 'Admin\Page\PageController@editContactUsPage']);
    Route::post('/update-counatct-us-page', ['as' => 'updateContactUsPage', 'middleware' => ['role:admin'], 'uses' => 'Admin\Page\PageController@updateContactUsPage']);
    Route::get('manage-about-us-page', ['as' => 'manageAboutUsPage', 'middleware' => ['role:admin'], 'uses' => 'Admin\Page\PageController@aboutUsPage']);
    Route::get('/edit-about-us-page/{id}', ['as' => 'editAboutUsPage', 'middleware' => ['role:admin'], 'uses' => 'Admin\Page\PageController@editAboutUsPage']);
    Route::post('/update-about-us-page', ['as' => 'updateAboutUsPage', 'middleware' => ['role:admin'], 'uses' => 'Admin\Page\PageController@updateAboutUsPage']);

    Route::get('manage-terms-condition-page', ['as' => 'manageTermsConditionPage', 'middleware' => ['role:admin'], 'uses' => 'Admin\Page\PageController@TermsConditionPage']);
    Route::get('/edit-terms-condition-page/{id}', ['as' => 'editTermsConditionPage', 'middleware' => ['role:admin'], 'uses' => 'Admin\Page\PageController@editTermsConditionPage']);
    Route::post('/update-terms-condition-page', ['as' => 'updateTermsConditionPage', 'middleware' => ['role:admin'], 'uses' => 'Admin\Page\PageController@updateTermsConditionPage']);
    /* Page route end*/

    /* Setting route start*/

    Route::get('manage-setting', ['as' => 'manageSetting', 'middleware' => ['role:admin'], 'uses' => 'Admin\Setting\SettingController@index']);
    Route::get('/edit-setting/{id}', ['as' => 'editSetting', 'middleware' => ['role:admin'], 'uses' => 'Admin\Setting\SettingController@editSetting']);
    Route::post('/update-setting', ['as' => 'updateSetting', 'middleware' => ['role:admin'], 'uses' => 'Admin\Setting\SettingController@updateSetting']);

    /* Setting route end*/
    /* Delivery Slot route start*/

    Route::get('manage-delivery-slot', ['as' => 'manageDeliverySlot', 'middleware' => ['role:admin'], 'uses' => 'Admin\Setting\SettingController@manageDeliverySlot']);
    Route::post('/delivery-slot-status', ['as' => 'deliverySlotStatus', 'middleware' => ['role:admin'], 'uses' => 'Admin\Setting\SettingController@deliverySlotStatus']);

    /* Delivery Slot route end*/
    /*Delivery Setting route start*/
    Route::get('manage-setting-delivery', ['as' => 'manageDeliverySetting', 'middleware' => ['role:admin'], 'uses' => 'Admin\Setting\SettingController@manageDelivery']);
    Route::get('/edit-setting-delivery/{id}', ['as' => 'editDeliverySetting', 'middleware' => ['role:admin'], 'uses' => 'Admin\Setting\SettingController@editDelivery']);
    Route::post('/update-setting-delivery', ['as' => 'updateDeliverySetting', 'middleware' => ['role:admin'], 'uses' => 'Admin\Setting\SettingController@updateDelivery']);
    /*Delivery Setting route end*/

    /* CashBack route start*/
    Route::get('manage-cashback', ['as' => 'manageCashBack', 'middleware' => ['role:admin'], 'uses' => 'Admin\Coupon\CouponController@CashBack']);
    Route::get('add-cashback', ['as' => 'addCashBack', 'middleware' => ['role:admin'], 'uses' => 'Admin\Coupon\CouponController@addCashBack']);
    Route::post('save-cashback', ['as' => 'saveCashBack','middleware' => ['role:admin'], 'uses' => 'Admin\Coupon\CouponController@saveCashBack']);
    Route::get('/edit-cashback/{id}', ['as' => 'editCashBack', 'middleware' => ['role:admin'], 'uses' => 'Admin\Coupon\CouponController@editCashBack']);
    Route::post('/update-cashback/{id}', ['as' => 'updateCashBack', 'middleware' => ['role:admin'], 'uses' => 'Admin\Coupon\CouponController@updateCashBack']);
    Route::get('/del-cashback/{id}', ['as' => 'delCashBack', 'middleware' => ['role:admin'], 'uses' => 'Admin\Coupon\CouponController@deleteCashBack']);
    Route::post('cashback-status', ['as' => 'updateCashBackStatus', 'middleware' => ['role:admin'], 'uses' => 'Admin\Coupon\CouponController@updateCashBackStatus']);
    /*CashBack Setting route end*/
    /* PromoCode route start*/
    Route::get('manage-promocode', ['as' => 'managePromoCode', 'middleware' => ['role:admin'], 'uses' => 'Admin\Coupon\CouponController@PromoCode']);
    Route::get('add-promocode', ['as' => 'addPromoCode', 'middleware' => ['role:admin'], 'uses' => 'Admin\Coupon\CouponController@addPromoCode']);
    Route::post('save-promocode', ['as' => 'savePromoCode','middleware' => ['role:admin'], 'uses' => 'Admin\Coupon\CouponController@savePromoCode']);
    Route::get('/edit-promocode/{id}', ['as' => 'editPromoCode', 'middleware' => ['role:admin'], 'uses' => 'Admin\Coupon\CouponController@editPromoCode']);
    Route::post('/update-promocode/{id}', ['as' => 'updatePromoCode', 'middleware' => ['role:admin'], 'uses' => 'Admin\Coupon\CouponController@updatePromoCode']);
    Route::get('/del-promocode/{id}', ['as' => 'delPromoCode', 'middleware' => ['role:admin'], 'uses' => 'Admin\Coupon\CouponController@deletePromoCode']);
    Route::post('promocode-status', ['as' => 'updatePromoCodeStatus', 'middleware' => ['role:admin'], 'uses' => 'Admin\Coupon\CouponController@updatePromoCodeStatus']);
    /*PromoCode  route end*/

    /*Seo route start*/
    Route::get('manage-seo-data', ['as' => 'manageSeoData', 'middleware' => ['role:admin'], 'uses' => 'Admin\Seo\SeoController@index']);
    Route::get('/edit-seo-data/{id}', ['as' => 'editSeoData', 'middleware' => ['role:admin'], 'uses' => 'Admin\Seo\SeoController@edit']);
    Route::post('/update-seo-data/{id}', ['as' => 'updateSeoData', 'middleware' => ['role:admin'], 'uses' => 'Admin\Seo\SeoController@update']);
    /*Seo route end*/
    Route::get('manage-become-seller', ['as' => 'manageBecomeSeller', 'middleware' => ['role:admin'], 'uses' => 'Admin\Seller\SellerController@index']);
    Route::get('delete-become-seller/{id}', ['as' => 'delBecomeSeller', 'middleware' => ['role:admin'], 'uses' => 'Admin\Seller\SellerController@delete']);

    Route::get('manage-contact-messages', ['as' => 'manageContactMessage', 'middleware' => ['role:admin'], 'uses' => 'Admin\Contact\ContactController@index']);
    Route::get('delete-contact-messages/{id}', ['as' => 'delContactMessage', 'middleware' => ['role:admin'], 'uses' => 'Admin\Contact\ContactController@delete']);
    /* Web Info route start*/
    Route::get('manage-web-info', ['as' => 'manageWebInfo', 'middleware' => ['role:admin'], 'uses' => 'Admin\WebInfo\WebInfoController@index']);
    Route::get('/edit-web-info/{id}', ['as' => 'editWebInfo', 'middleware' => ['role:admin'], 'uses' => 'Admin\WebInfo\WebInfoController@edit']);
    Route::post('/update-web-info/{id}', ['as' => 'updateWebInfo', 'middleware' => ['role:admin'], 'uses' => 'Admin\WebInfo\WebInfoController@update']);
    /* Faq route end*/
    Route::get('manage-notification', ['as' => 'manageNotification', 'middleware' => ['role:admin'], 'uses' => 'Admin\Notification\NotificationController@notification']);
    Route::post('send-notification', ['as' => 'sendNotification', 'middleware' => ['role:admin'], 'uses' => 'Admin\Notification\NotificationController@sendNotification']);
});
