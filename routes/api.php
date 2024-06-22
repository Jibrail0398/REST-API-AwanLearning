<?php



use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ProtectedUserController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::get('email/verify/{id}', [RegisterController::class, 'verifyEmail'])->name('verification.verify');;
Route::post('resend/verification', [RegisterController::class, 'resendVerificationEmail'])->name('verification.send');


Route::middleware('user')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile',[ProfileController::class,'store']);
    Route::put('/profile',[ProfileController::class,'update']);
    Route::post('/reset/password',[ProfileController::class,'changePassword']);

    
    // Route untuk CRUD course
    Route::get('/courses', [CourseController::class, 'showall']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);

    // Route untuk CRUD content
    Route::get('/course/owner', [CourseController::class, 'ownerCourse']);
    Route::get('/courses/{courseId}/contents', [ContentController::class, 'index']);
    Route::post('/courses/{courseId}/contents', [ContentController::class, 'store']);
    Route::get('/courses/{courseId}/contents/{id}', [ContentController::class, 'show']);
    Route::put('/courses/{courseId}/contents/{id}', [ContentController::class, 'update']);
    Route::delete('/courses/{courseId}/contents/{id}', [ContentController::class, 'destroy']);


    // Route untuk CRUD category
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    // ini untuk wishlist
    Route::post('/wishlist/{courseId}/add', [WishlistController::class, 'addToWishlist']);
    Route::delete('/wishlist/{courseId}/remove', [WishlistController::class, 'removeFromWishlist']);
    Route::get('/wishlist', [WishlistController::class, 'wishlist']);


    // ini untuk cart item
    Route::post('/cart/{courseId}/add', [CartController::class, 'addToCart']);
    Route::delete('/cart/{cartId}/remove', [CartController::class, 'removeFromCart']);
    Route::get('/cart', [CartController::class, 'cart']);


    Route::post('/transaction/create', [TransactionController::class, 'createTransactionFromCart']);
    Route::get('/transactions/{transaction}/pay', [TransactionController::class, 'showPaymentPage']);
    Route::post('/transactions/notification', [TransactionController::class, 'handleNotification']);
});







