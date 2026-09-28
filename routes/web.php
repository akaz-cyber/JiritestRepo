<?php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Artisan;
    use App\Http\Controllers\AdminController;
    use App\Http\Controllers\Auth\ForgotPasswordController;
    use App\Http\Controllers\FrontendController;
    use App\Http\Controllers\Auth\LoginController;
    use App\Http\Controllers\MessageController;
    use App\Http\Controllers\CartController;
    use App\Http\Controllers\WishlistController;
    use App\Http\Controllers\OrderController;
    use App\Http\Controllers\ProductReviewController;
    use App\Http\Controllers\PostCommentController;
    use App\Http\Controllers\CouponController;
    use App\Http\Controllers\PayPalController;
    use App\Http\Controllers\NotificationController;
    use App\Http\Controllers\HomeController;
    use \UniSharp\LaravelFilemanager\Lfm;
    use App\Http\Controllers\Auth\ResetPasswordController;
    use App\Http\Controllers\ProductController;
    use App\Http\Controllers\RajaOngkirController;
    use App\Http\Controllers\MidtransController;
    use App\Http\Controllers\AprioriController;
    // Tambah Route PDF
    use App\Http\Controllers\Admin\ReportController;
    // Tambah Route addresses
    use App\Http\Controllers\AddressController;
    use Illuminate\Foundation\Auth\EmailVerificationRequest;
    use Illuminate\Http\Request;


    use App\Http\Controllers\AnnouncementController;
    use App\Http\Controllers\FaqController;
    use Illuminate\Auth\Events\Verified;
    use Illuminate\Support\Facades\Password;
    use App\User;
    use Illuminate\Support\Facades\Hash;


    // use App\Models\Order;
    // use App\Mail\OrderInvoiceMail;

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    // CACHE CLEAR ROUTE KEMUNGKINAN HAPUS
    Route::get('cache-clear', function () {
        Artisan::call('optimize:clear');
        request()->session()->flash('success', 'Successfully cache cleared.');
        return redirect()->back();
    })->name('cache.clear');


    // STORAGE LINKED ROUTE KEMUNGKINAN HAPUS
    Route::get('storage-link',[AdminController::class,'storageLink'])->name('storage.link');


    Auth::routes(
        ['register' => false,
         'login'    => false,
         'reset'    => false,
         'verify'   => false,
        ]
    );


    Route::get('user/login', [FrontendController::class, 'login'])->name('login.form')->middleware('guest');
    Route::post('user/login', [FrontendController::class, 'loginSubmit'])->name('login.submit')->middleware('guest');
    Route::get('user/logout', [FrontendController::class, 'logout'])->name('user.logout');

    Route::get('user/register', [FrontendController::class, 'register'])->name('register.form')->middleware('guest');
    Route::post('user/register', [FrontendController::class, 'registerSubmit'])->name('register.submit')->middleware('guest');

    // Reset password
    // Route::get('password/reset', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    // Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
    // Password Reset Routes
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

    // Socialite
    Route::get('login/{provider}/', [LoginController::class, 'redirect'])->name('login.redirect');
    Route::get('login/{provider}/callback/', [LoginController::class, 'Callback'])->name('login.callback');

    Route::get('/', [FrontendController::class, 'home'])->name('home');

// Frontend Routes
    Route::get('/home', [FrontendController::class, 'index']);
    Route::get('/about-us', [FrontendController::class, 'aboutUs'])->name('about-us');
    Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
    Route::post('/contact/message', [MessageController::class, 'store'])->name('contact.store');
    // Route::get('product-detail/{slug}', [FrontendController::class, 'productDetail'])->name('product-detail');
    Route::get('/product-detail/{slug}', [ProductController::class, 'detail']) ->name('product-detail');
    Route::get('/product/search', [FrontendController::class, 'productSearch'])->name('product.search');
    // Route::post('/product/search', [FrontendController::class, 'productSearch'])->name('product.search');
    Route::get('/product-cat/{slug}', [FrontendController::class, 'productCat'])->name('product-cat');
    Route::get('/product-sub-cat/{slug}/{sub_slug}', [FrontendController::class, 'productSubCat'])->name('product-sub-cat');
    Route::get('/product-brand/{slug}', [FrontendController::class, 'productBrand'])->name('product-brand');
// Cart section
    Route::get('/add-to-cart/{slug}', [CartController::class, 'addToCart'])->name('add-to-cart')->middleware(['auth','user','check.verified']);
    Route::post('/add-to-cart', [CartController::class, 'singleAddToCart'])->name('single-add-to-cart')->middleware(['auth','user','check.verified']);
    // Route::post('/add-to-cart', [CartController::class, 'singleAddToCart'])->name('single-add-to-cart')->middleware('user');
    Route::get('cart-delete/{id}', [CartController::class, 'cartDelete'])->name('cart-delete');
    Route::post('cart-update', [CartController::class, 'cartUpdate'])->name('cart.update');
    Route::post('cart/update-selection', [CartController::class, 'updateSelection'])->name('cart.update-selection');
    Route::post('cart-delete-selected', [CartController::class, 'cartDeleteSelected'])->name('cart.delete-selected');

    // Route::get('/cart', function () {
    //     return view('frontend.pages.cart');
    // })->name('cart')->middleware('user');
    Route::get('/cart', [CartController::class, 'cartIndex'])->name('cart')->middleware('user');
    // checkout
    Route::get('/checkout', [\App\Http\Controllers\CartController::class, 'checkout'])
        ->name('checkout')
        ->middleware(['auth','check.verified','user']);

    Route::post('/checkout', [\App\Http\Controllers\CartController::class, 'placeOrder'])
        ->name('checkout.place')
        ->middleware(['auth','check.verified','user']);
    //duplicated route
    // Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout')->middleware('user');
    // Route::post('/checkout', [CartController::class, 'placeOrder'])->name('checkout.place')->middleware('user');

    // Route untuk RajaOngkir
    Route::get('/provinces', [RajaOngkirController::class, 'getProvinces'])
    ->name('rajaongkir.provinces')
    ->middleware(['user', 'ajax']);

    Route::get('/cities/{province_id}', [RajaOngkirController::class, 'getCities'])
    ->name('rajaongkir.cities')
    ->middleware(['user', 'ajax']);

    Route::get('/districts/{city_id}', [RajaOngkirController::class, 'getDistricts'])
    ->name('rajaongkir.districts')
    ->middleware(['user', 'ajax']);

    Route::get('/sub-districts/{district_id}', [RajaOngkirController::class, 'getSubDistricts'])
    ->name('rajaongkir.subdistricts')
    ->middleware(['user', 'ajax']);

    Route::post('/check-ongkir', [RajaOngkirController::class, 'checkOngkir'])
    ->name('rajaongkir.checkOngkir')
    ->middleware(['user', 'ajax']);

    Route::get('/track-resi', [RajaOngkirController::class, 'trackOrder'])
    ->name('rajaongkir.trackResi')
    ->middleware(['user', 'ajax']);
    //MIDTRANS
    Route::post('/midtrans/notification', [MidtransController::class, 'notificationHandler'])->name('midtrans.notification');
    Route::post('/order/cancel-unpaid', 'OrderController@cancelUnpaidOrder')->name('order.cancel.unpaid');

// Wishlist
    // Route::get('/wishlist', function () {
    //     return view('frontend.pages.wishlist');
    // })->name('wishlist');
    // Route::get('/wishlist/{slug}', [WishlistController::class, 'wishlist'])->name('add-to-wishlist')->middleware('user');
    // Route::get('wishlist-delete/{id}', [WishlistController::class, 'wishlistDelete'])->name('wishlist-delete');
    Route::post('cart/order', [OrderController::class, 'store'])->name('cart.order');
    Route::get('order/pdf/{id}', [OrderController::class, 'pdf'])->name('order.pdf');
    Route::get('/income', [OrderController::class, 'incomeChart'])->name('product.order.income');
// Route::get('/user/chart',[AdminController::class, 'userPieChart'])->name('user.piechart');
    Route::get('/product-grids', [FrontendController::class, 'productGrids'])->name('product-grids');
    // Route::get('/product-lists', [FrontendController::class, 'productLists'])->name('product-lists');
    Route::match(['get', 'post'], '/filter', [FrontendController::class, 'productFilter'])->name('shop.filter');
// Order Track
    // Route::get('/product/track', [OrderController::class, 'orderTrack'])->name('order.track');
    // Route::post('product/track/order', [OrderController::class, 'productTrackOrder'])->name('product.track.order');
// Blog
    // Route::get('/blog', [FrontendController::class, 'blog'])->name('blog');
    // Route::get('/blog-detail/{slug}', [FrontendController::class, 'blogDetail'])->name('blog.detail');
    // Route::get('/blog/search', [FrontendController::class, 'blogSearch'])->name('blog.search');
    // Route::post('/blog/filter', [FrontendController::class, 'blogFilter'])->name('blog.filter');
    // Route::get('blog-cat/{slug}', [FrontendController::class, 'blogByCategory'])->name('blog.category');
    // Route::get('blog-tag/{slug}', [FrontendController::class, 'blogByTag'])->name('blog.tag');

// NewsLetter
    // Route::post('/subscribe', [FrontendController::class, 'subscribe'])->name('subscribe');

// Product Review
    Route::resource('/review', 'ProductReviewController');
    Route::post('product/{slug}/review', [ProductReviewController::class, 'store'])->name('review.store');

// Post Comment
    Route::post('post/{slug}/comment', [PostCommentController::class, 'store'])->name('post-comment.store');
    Route::resource('/comment', 'PostCommentController');
// Coupon
    Route::post('/coupon-store', [CouponController::class, 'couponStore'])->name('coupon-store');
    // kupon
    Route::post('/coupon/apply', [\App\Http\Controllers\CouponController::class, 'apply'])
        ->name('coupon.apply')->middleware('user');
    Route::post('/coupon/remove', [\App\Http\Controllers\CouponController::class, 'remove'])
        ->name('coupon.remove')->middleware('user');


// Payment
    Route::get('payment', [PayPalController::class, 'payment'])->name('payment');
    Route::get('cancel', [PayPalController::class, 'cancel'])->name('payment.cancel');
    Route::get('payment/success', [PayPalController::class, 'success'])->name('payment.success');


// Backend section start

    Route::group(['prefix' => '/admin', 'middleware' => ['auth', 'admin']], function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin');
        Route::get('/file-manager', function () {
            return view('backend.layouts.file-manager');
        })->name('file-manager');
        // user route
        Route::resource('users', 'UsersController');
        // Banner
        Route::resource('banner', 'BannerController');
        // Brand
        // Route::resource('brand', 'BrandController');
        // Profile
        Route::get('/profile', [AdminController::class, 'profile'])->name('admin-profile');
        Route::post('/profile/{id}', [AdminController::class, 'profileUpdate'])->name('profile-update');
        // Category
        Route::resource('/category', 'CategoryController');
        Route::post('category/update-order', 'CategoryController@updateOrder')->name('category.updateOrder');
        // Product
        Route::resource('/product', 'ProductController');
        // Ajax for sub category
        Route::post('/category/{id}/child', 'CategoryController@getChildByParent');
        // POST category
        Route::resource('/post-category', 'PostCategoryController');
        // Post tag
        Route::resource('/post-tag', 'PostTagController');
        // Post
        Route::resource('/post', 'PostController');
        // Message
        Route::resource('/message', 'MessageController');
        Route::get('/message/five', [MessageController::class, 'messageFive'])->name('messages.five');

        // Order
        Route::resource('/order', 'OrderController');
        // Shipping
        Route::resource('/shipping', 'ShippingController');
        // Coupon
        Route::resource('/coupon', 'CouponController');
        // Settings
        Route::get('settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('setting/update', [AdminController::class, 'settingsUpdate'])->name('settings.update');

        // Notification
        Route::get('/notification/{id}', [NotificationController::class, 'show'])->name('admin.notification');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('all.notification');
        Route::delete('/notification/{id}', [NotificationController::class, 'delete'])->name('notification.delete');
        // Password Change
        Route::get('change-password', [AdminController::class, 'changePassword'])->name('change.password.form');
        Route::post('change-password', [AdminController::class, 'changPasswordStore'])->name('admin.change.password');

        Route::resource('announcements', AnnouncementController::class);
        Route::resource('faq', FaqController::class);


        Route::get('admin/apriori', [AprioriController::class, 'index'])->name('apriori.index');
        Route::post('admin/apriori/generate', [AprioriController::class, 'generateApriori'])->name('apriori.generate');
        Route::get('admin/setapriori', [AprioriController::class, 'setapriori'])->name('apriori.set');
        Route::get('admin/tambahdataset', [AprioriController::class, 'tambahdataset'])->name('apriori.tambah');
        Route::post('admin/apriori/upload-dataset', [AprioriController::class, 'uploadDataset'])->name('apriori.upload');
        Route::post('admin/apriori/clean-dataset', [AprioriController::class, 'cleanDataset'])->name('apriori.clean');
    });

    //super admin section start
    Route::group(['prefix' => '/superadmin', 'middleware' => ['auth',   'superadmin']], function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard.superadmin');



    });

// User section start
    Route::group(['prefix' => '/user', 'middleware' => ['auth','check.verified','user']], function () {
        Route::get('/', [HomeController::class, 'index'])->name('user');
        // Profile
        Route::get('/profile', [HomeController::class, 'profile'])->name('user-profile');
        Route::post('/profile/{id}', [HomeController::class, 'profileUpdate'])->name('user-profile-update');
        //  Order
        Route::get('/order', "HomeController@orderIndex")->name('user.order.index');
        Route::get('/order/show/{id}', "HomeController@orderShow")->name('user.order.show');
        Route::delete('/order/delete/{id}', [HomeController::class, 'userOrderDelete'])->name('user.order.delete');
        // Product Review
        Route::get('/user-review', [HomeController::class, 'productReviewIndex'])->name('user.productreview.index');
        Route::delete('/user-review/delete/{id}', [HomeController::class, 'productReviewDelete'])->name('user.productreview.delete');
        Route::get('/user-review/edit/{id}', [HomeController::class, 'productReviewEdit'])->name('user.productreview.edit');
        Route::patch('/user-review/update/{id}', [HomeController::class, 'productReviewUpdate'])->name('user.productreview.update');

        // Post comment
        Route::get('user-post/comment', [HomeController::class, 'userComment'])->name('user.post-comment.index');
        Route::delete('user-post/comment/delete/{id}', [HomeController::class, 'userCommentDelete'])->name('user.post-comment.delete');
        Route::get('user-post/comment/edit/{id}', [HomeController::class, 'userCommentEdit'])->name('user.post-comment.edit');
        Route::patch('user-post/comment/udpate/{id}', [HomeController::class, 'userCommentUpdate'])->name('user.post-comment.update');

        // Password Change
        Route::get('change-password', [HomeController::class, 'changePassword'])->name('user.change.password.form');
        Route::post('change-password', [HomeController::class, 'changPasswordStore'])->name('user.change.password');

    });

    Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
        Lfm::routes();
    });

    Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web','auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();

});

// Report Routes
    Route::prefix('admin')->middleware(['auth','admin'])->group(function () {
    Route::get('/reports/orders', [ReportController::class, 'preview'])->name('reports.orders.preview'); // opsional: pratinjau HTML
    Route::get('/reports/orders/export', [ReportController::class, 'export'])->name('reports.orders.export'); // unduh
});

// Rute alamat pengguna
// Route::middleware(['auth'])->group(function() {
//     Route::resource('addresses', AddressController::class)->only(['index','store','update','destroy']);
//     Route::patch('addresses/{address}/set-default', [AddressController::class, 'setDefault'])->name('addresses.set-default');
// });
Route::middleware('auth')->group(function () {
    // index & create & store
    Route::get('addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::get('addresses/create', [AddressController::class, 'create'])->name('addresses.create');
    Route::post('addresses', [AddressController::class, 'store'])->name('addresses.store');

    // edit, update, destroy → pakai encryptedId
    Route::get('addresses/{encryptedId}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('addresses/{encryptedId}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('addresses/{encryptedId}', [AddressController::class, 'destroy'])->name('addresses.destroy');

    // set default address
    Route::patch('addresses/{encryptedId}/set-default', [AddressController::class, 'setDefault'])->name('addresses.setDefault');
});


//verifikasi email

Route::get('/email/verify', function (Illuminate\Http\Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('home')->with('success', 'Akun anda sudah terverifikasi.');
    }
    return view('frontend.pages.verify-email');
})->middleware('auth')->name('verification.notice');

// Proses klik link dari email
Route::get('/email/verify/{id}/{hash}', function (Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();
    auth()->setUser(auth()->user()->fresh());
    // $request->session()->regenerate();
    return redirect()->route('home')->with('success', 'Email berhasil diverifikasi!');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Kirim ulang email verifikasi
Route::post('/email/verification-notification', function (Illuminate\Http\Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('home')->with('success', 'Akun anda sudah terverifikasi.');
    }

    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Link verifikasi dikirim ulang!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// Route::get('/test-invoice', function () {
//     $order = Order::latest()->first();
//     return new OrderInvoiceMail($order);
// });


    /*
    |--------------------------------------------------------------------------
    | Forgot Password
    |--------------------------------------------------------------------------
    */

    Route::get('/forgot-password', function () {
        return view('frontend.pages.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (Illuminate\Http\Request $request) {

        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);

    })
    ->middleware('throttle:3,1')
    ->name('password.email');


    /*
    |--------------------------------------------------------------------------
    | Reset Password
    |--------------------------------------------------------------------------
    */

    Route::get('/reset-password/{token}', function ($token) {
        return view('frontend.pages.reset-password', ['token' => $token]);
    })->name('password.reset');


    Route::post('/reset-password', function (Illuminate\Http\Request $request) {

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.form')->with('success', 'Password berhasil direset')
            : back()->withErrors(['email' => [__($status)]]);
    })->name('password.update');