<?php

namespace App\Http\Controllers;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\PostTag;
use App\Models\PostCategory;
use App\Models\Post;
use App\Models\Cart;
use App\Models\Brand;
use App\User;
use App\Models\Faq;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Session;
use Newsletter;
use DB;
use Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
class FrontendController extends Controller
{

    public function index(Request $request){
        return redirect()->route($request->user()->role);
    }

    public function home(){
        $posts=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $banners=Banner::where('status','active')->limit(3)->orderBy('id','DESC')->get();
        // return $banner;
        $products=Product::where('status','active')->orderBy('id','DESC')->limit(8)->get();
        $category=Category::where('status','active')->where('is_parent',1)->orderBy('title','ASC')->get();
        // return $category;
        return view('frontend.index')
                ->with('posts',$posts)
                ->with('banners',$banners)
                ->with('product_lists',$products)
                ->with('category_lists',$category);
    }

    public function aboutUs(){
        return view('frontend.pages.about-us');
    }

    public function contact(){
        $faqs = Faq::where('status', 'active')->orderBy('id', 'DESC')->get();
        return view('frontend.pages.contact', compact('faqs'));
    }

    // public function productDetail($slug){
    //     $product_detail = Product::getProductBySlug($slug);
    //     $product_detail->increment('view_count');
    //     $product_detail->refresh();


    //     return view('frontend.pages.product_detail')->with('product_detail',$product_detail);
    //     dd($product_detail);
    // }

    public function productGrids(){
        $productsQuery = Product::query()->where('status', 'active');

        $products = $this->applyFiltersAndSorting($productsQuery);

        $perPage = !empty($_GET['show']) ? $_GET['show'] : 9;
        $products = $products->paginate($perPage);

        $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

        return view('frontend.pages.product-grids')
            ->with('products', $products)
            ->with('recent_products', $recent_products);
      }
    // public function productLists(){
    //     $productsQuery = Product::query()->where('status', 'active');

    //     // Terapkan semua filter dan sorting
    //     $products = $this->applyFiltersAndSorting($productsQuery);

    //     $perPage = !empty($_GET['show']) ? $_GET['show'] : 5;
    //     $products = $products->paginate($perPage);

    //     $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

    //     return view('frontend.pages.product-lists')
    //         ->with('products', $products)
    //         ->with('recent_products', $recent_products);
    //     }
    public function productFilter(Request $request){
            $data= $request->all();
            // return $data;
            $showURL="";
            if(!empty($data['show'])){
                $showURL .='&show='.$data['show'];
            }

            $sortByURL='';
            if(!empty($data['sortBy'])){
                $sortByURL .='&sortBy='.$data['sortBy'];
            }

            $catURL="";
            if(!empty($data['category'])){
                foreach($data['category'] as $category){
                    if(empty($catURL)){
                        $catURL .='&category='.$category;
                    }
                    else{
                        $catURL .=','.$category;
                    }
                }
            }

            $brandURL="";
            if(!empty($data['brand'])){
                foreach($data['brand'] as $brand){
                    if(empty($brandURL)){
                        $brandURL .='&brand='.$brand;
                    }
                    else{
                        $brandURL .=','.$brand;
                    }
                }
            }
            // return $brandURL;

            $priceRangeURL="";
            if(!empty($data['price_range'])){
                $priceRangeURL .='&price='.$data['price_range'];
            }
            if(request()->is('e-shop.loc/product-grids')){
                return redirect()->route('product-grids',$catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            }
            else{
                return redirect()->route('product-grids',$catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            }
    }
    public function productSearch(Request $request){
        $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
        $searchTerm = $request->search;

        $products = Product::with('variants')
            ->where('status', 'active')
            ->where(function($query) use ($searchTerm) {
                $query->where('title', 'like', '%' . $searchTerm . '%')
                      ->orWhere('slug', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%')
                      ->orWhere('summary', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('variants', function($subQuery) use ($searchTerm) {
                          $subQuery->where('price', 'like', '%' . $searchTerm . '%');
                      });
            })
            ->orderBy('id', 'DESC')
            ->paginate(9);

        return view('frontend.pages.product-grids')->with('products', $products)->with('recent_products', $recent_products);
    }


    public function productCat(Request $request){
        $category = Category::where('slug', $request->slug)->firstOrFail();

        // Mulai query dengan memfilter produk dari kategori ini saja
        $productsQuery = $category->products()->where('status', 'active');

        // Terapkan semua filter dan sorting lainnya (termasuk sortBy dari URL)
        $products = $this->applyFiltersAndSorting($productsQuery);

        // Dapatkan view name (grids atau lists) dari URL
        $viewName = 'frontend.pages.product-grids';
        $perPage = 9;

        if (!empty($_GET['show'])) {
            $perPage = $_GET['show'];
        }

        $products = $products->paginate($perPage);

        $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

        return view($viewName)
            ->with('products', $products)
            ->with('recent_products', $recent_products);
    }
    public function productSubCat(Request $request){
        $subCategory = Category::getProductBySubCat($request->sub_slug);
        // return $products;
        $productsQuery = $subCategory->sub_products()->where('status', 'active');
        $products = $this->applyFiltersAndSorting($productsQuery);
        $viewName = 'frontend.pages.product-grids';
        $perPage = 9;
        if (!empty($_GET['show'])) {
            $perPage = $_GET['show'];
        }
        $products = $products->paginate($perPage);

        $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

        return view($viewName)
            ->with('products', $products)
            ->with('recent_products', $recent_products);
    }


    public function blog(){
        $post=Post::query();

        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            // dd($slug);
            $cat_ids=PostCategory::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            return $cat_ids;
            $post->whereIn('post_cat_id',$cat_ids);
            // return $post;
        }
        if(!empty($_GET['tag'])){
            $slug=explode(',',$_GET['tag']);
            // dd($slug);
            $tag_ids=PostTag::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            // return $tag_ids;
            $post->where('post_tag_id',$tag_ids);
            // return $post;
        }

        if(!empty($_GET['show'])){
            $post=$post->where('status','active')->orderBy('id','DESC')->paginate($_GET['show']);
        }
        else{
            $post=$post->where('status','active')->orderBy('id','DESC')->paginate(9);
        }
        // $post=Post::where('status','active')->paginate(8);
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post)->with('recent_posts',$rcnt_post);
    }

    public function blogDetail($slug){
        $post=Post::getPostBySlug($slug);
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        // return $post;
        return view('frontend.pages.blog-detail')->with('post',$post)->with('recent_posts',$rcnt_post);
    }

    public function blogSearch(Request $request){
        // return $request->all();
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $posts=Post::orwhere('title','like','%'.$request->search.'%')
            ->orwhere('quote','like','%'.$request->search.'%')
            ->orwhere('summary','like','%'.$request->search.'%')
            ->orwhere('description','like','%'.$request->search.'%')
            ->orwhere('slug','like','%'.$request->search.'%')
            ->orderBy('id','DESC')
            ->paginate(8);
        return view('frontend.pages.blog')->with('posts',$posts)->with('recent_posts',$rcnt_post);
    }

    public function blogFilter(Request $request){
        $data=$request->all();
        // return $data;
        $catURL="";
        if(!empty($data['category'])){
            foreach($data['category'] as $category){
                if(empty($catURL)){
                    $catURL .='&category='.$category;
                }
                else{
                    $catURL .=','.$category;
                }
            }
        }

        $tagURL="";
        if(!empty($data['tag'])){
            foreach($data['tag'] as $tag){
                if(empty($tagURL)){
                    $tagURL .='&tag='.$tag;
                }
                else{
                    $tagURL .=','.$tag;
                }
            }
        }
        // return $tagURL;
            // return $catURL;
        return redirect()->route('blog',$catURL.$tagURL);
    }

    public function blogByCategory(Request $request){
        $post=PostCategory::getBlogByCategory($request->slug);
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post->post)->with('recent_posts',$rcnt_post);
    }

    public function blogByTag(Request $request){
        // dd($request->slug);
        $post=Post::getBlogByTag($request->slug);
        // return $post;
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post)->with('recent_posts',$rcnt_post);
    }

    // Login
    public function login(){
        return view('frontend.pages.login');
    }
    public function loginSubmit(Request $request)
    {
        $data = $request->all();

        if (Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
            'status' => 'active'
        ])) {

            // CEK EMAIL SUDAH VERIFY ATAU BELUM
            if (!Auth::user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')
                    ->with('warning', 'Silakan verifikasi email terlebih dahulu.');
            }

            Session::put('user', $data['email']);
            request()->session()->flash('success', 'Login Berhasil');

            return redirect()->route('home');
        }

        request()->session()->flash('error', 'Email dan Password Salah');
        return redirect()->back();
    }

    public function logout(Request $request){
        Session::forget('user');
        $request->session()->forget('coupon');
        Auth::logout();
        request()->session()->flash('success','Logout Berhasil');
        return back();
    }

    public function register(){
        return view('frontend.pages.register');
    }
    public function registerSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'min:2', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => 'string|required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama minimal harus terdiri dari 2 karakter.',
            'name.regex' => 'Nama tidak boleh angka atau simbol.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',

            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('register.form')
                ->withErrors($validator)
                ->withInput();
        }

        //BUAT USER
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active'
        ]);

        // KIRIM EMAIL VERIFIKASI
        $user->sendEmailVerificationNotification();

        // AUTO LOGIN
        Auth::login($user);

        //REDIRECT KE HALAMAN VERIFY
        return redirect()->route('verification.notice')
            ->with('success', 'Pendaftaran berhasil. Silakan verifikasi email Anda.');
    }

    public function create(array $data){
        return User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password']),
            'status'=>'active'
            ]);
            $user->sendEmailVerificationNotification();

            return $user;
    }
    // Reset password
    public function showResetForm(){
        return view('auth.passwords.old-reset');
    }

    public function subscribe(Request $request){
        if(! Newsletter::isSubscribed($request->email)){
                Newsletter::subscribePending($request->email);
                if(Newsletter::lastActionSucceeded()){
                    request()->session()->flash('success','Subscribed! Please check your email');
                    return redirect()->route('home');
                }
                else{
                    Newsletter::getLastError();
                    return back()->with('error','Something went wrong! please try again');
                }
            }
            else{
                request()->session()->flash('error','Already Subscribed');
                return back();
            }
    }


    // ini buat fungsi dari product grid, product list, categori product, dan sub categori product
    private function applyFiltersAndSorting($productsQuery)
{
    if (!empty($_GET['category'])) {
        $slug = explode(',', $_GET['category']);
        $cat_ids = Category::select('id')->whereIn('slug', $slug)->pluck('id')->toArray();
        $productsQuery->whereIn('cat_id', $cat_ids);
    }

    // Terapkan Sorting
    if (!empty($_GET['sortBy'])) {
        $sortBy = $_GET['sortBy'];
        switch ($sortBy) {
            case 'latest':
                $productsQuery->orderBy('id', 'DESC');
                break;
            case 'bestselling':
                $productsQuery->withCount(['orderItems as total_quantity' => function ($query) {
                    $query->select(DB::raw('sum(quantity)'));
                }])->orderBy('total_quantity', 'desc');
                break;
            case 'popular':
                $threeMonthsAgo = Carbon::now()->subMonths(3);

                $productsQuery->withCount(['views' => function ($query) use ($threeMonthsAgo) {
                    $query->where('viewed_at', '>=', $threeMonthsAgo);
                }])->orderBy('views_count', 'desc');
                break;
            case 'title':
                $productsQuery->orderBy('title', 'ASC');
                break;
            case 'price_asc':
                $productsQuery->orderByRaw('(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) ASC');
                break;
            case 'price_desc':
                $productsQuery->orderByRaw('(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) DESC');
                break;
            default:
                $productsQuery->orderBy('id', 'DESC');
                break;
        }
    } else {
        // Default sort jika tidak ada
        $productsQuery->orderBy('id', 'DESC');
    }
    // if (!empty($_GET['price'])) {
    //     $price = explode('-', $_GET['price']);

    // }

    return $productsQuery;
}



}
