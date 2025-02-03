<?php

// Controllers

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JenisPupukController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\PemupukanController;
use App\Http\Controllers\RencanaPemupukanController;
use App\Http\Controllers\RencanaRealisasiPemupukanController;
use App\Http\Controllers\Security\RolePermission;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\SettingController;
use App\Models\RencanaPemupukan;
use Illuminate\Support\Facades\Artisan;
// Packages
use Illuminate\Support\Facades\Route;

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

require __DIR__ . '/auth.php';

Route::get('/storage', function () {
    Artisan::call('storage:link');
});


//Landing-Pages Routes
// Route::group(['prefix' => 'landing-pages'], function () {
//     Route::get('index', [HomeController::class, 'landing_index'])->name('landing-pages.index');
//     Route::get('blog', [HomeController::class, 'landing_blog'])->name('landing-pages.blog');
//     Route::get('blog-detail', [HomeController::class, 'landing_blog_detail'])->name('landing-pages.blog-detail');
//     Route::get('about', [HomeController::class, 'landing_about'])->name('landing-pages.about');
//     Route::get('contact', [HomeController::class, 'landing_contact'])->name('landing-pages.contact');
//     Route::get('ecommerce', [HomeController::class, 'landing_ecommerce'])->name('landing-pages.ecommerce');
//     Route::get('faq', [HomeController::class, 'landing_faq'])->name('landing-pages.faq');
//     Route::get('feature', [HomeController::class, 'landing_feature'])->name('landing-pages.feature');
//     Route::get('pricing', [HomeController::class, 'landing_pricing'])->name('landing-pages.pricing');
//     Route::get('saas', [HomeController::class, 'landing_saas'])->name('landing-pages.saas');
//     Route::get('shop', [HomeController::class, 'landing_shop'])->name('landing-pages.shop');
//     Route::get('shop-detail', [HomeController::class, 'landing_shop_detail'])->name('landing-pages.shop-detail');
//     Route::get('software', [HomeController::class, 'landing_software'])->name('landing-pages.software');
//     Route::get('startup', [HomeController::class, 'landing_startup'])->name('landing-pages.startup');
// });

// Redirect root URL to login
Route::get('/', function () {
    return redirect()->route('login');
});

//UI Pages Routs
Route::get('/uisheet', [HomeController::class, 'uisheet'])->name('uisheet');
// Route::get('/login', [AuthenticatedSessionController::class, 'create'])
//                 ->middleware('guest')
//                 ->name('login');


Route::get('/api/users', [UserController::class, 'getUsers']);





Route::group(['middleware' => 'auth'], function () {
    // Permission Module
    Route::get('/role-permission', [RolePermission::class, 'index'])->name('role.permission.list');
    Route::resource('permission', PermissionController::class);
    Route::resource('role', RoleController::class);

    // Dashboard Routes
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // input pemupukan routes
    Route::get('/input-pemupukan', [PemupukanController::class, 'create'])->name('input-pemupukan');
    Route::get('/rekap-pemupukan', [PemupukanController::class, 'index'])->name('rekap-pemupukan');
    Route::get('/chart-pemupukan', [PemupukanController::class, 'chart'])->name('chart-pemupukan');
    Route::post('/pemupukan/store', [PemupukanController::class, 'storePemupukan'])->name('pemupukan.store');
    Route::get('/pemupukan/create', [PemupukanController::class, 'create'])->name('pemupukan.create');
    Route::get('/pemupukan/edit/{id}', [PemupukanController::class, 'edit'])->name('pemupukan.edit');
    Route::post('/pemupukan/update/{id}', [PemupukanController::class, 'update'])->name('pemupukan.update');
    Route::delete('/pemupukan/delete/{id}', [PemupukanController::class, 'destroy'])->name('pemupukan.destroy');
    Route::get('/pemupukan/show/{id}', [PemupukanController::class, 'show'])->name('pemupukan.show');
    Route::get('upload-data-pemupukan', [PemupukanController::class, 'upload'])->name('pemupukan.upload');
    Route::post('/pemupukan/import', [PemupukanController::class, 'import'])->name('pemupukan.import');
    Route::get('/chart-data', [PemupukanController::class, 'getChartData'])->name('pemupukan.chart-data-satu');

    Route::get('/data-realisasi-pemupukan', [PemupukanController::class, 'getPemupukanComparison'])->name('data-realisasi-pemupukan');





    // setting routes
    Route::resource('setting', SettingController::class);

    // testing whatsapp api url
    Route::get('/send-whatsapp-data', [WhatsAppController::class, 'sendData'])->name('whatsapp.send-data');
    Route::resource('whatsapp', WhatsAppController::class);
    // Users Module
    Route::get('upload-data', [MasterDataController::class, 'upload'])->name('upload-data.upload');
    Route::resource('users', UserController::class);
    // master data module
    Route::resource('master-data', MasterDataController::class);
    // rencana pemupukan
    Route::post('/rencana-pemupukan/import', [RencanaPemupukanController::class, 'import'])->name('rencana-pemupukan.import');
    Route::get('rencana-pemupukan/upload', [RencanaPemupukanController::class, 'upload'])->name('rencana-pemupukan.upload');
    Route::post('rencana-pemupukan/import', [RencanaPemupukanController::class, 'import'])->name('rencana-pemupukan.import');
    // Route::post('rencana-pemupukan/import', [RencanaPemupukanController::class, 'import'])->name('rencana-pemupukan.import');
    Route::resource('rencana-pemupukan', RencanaPemupukanController::class);




    // rencana realisasi pemupukan
    Route::get('rencana-realisasi-pemupukan/upload', [RencanaRealisasiPemupukanController::class, 'upload'])->name('rencana-realisasi-pemupukan.upload');
    Route::get('rencana-realisasi-pemupukan/data-table', [RencanaRealisasiPemupukanController::class, 'fetchData'])->name('rencana-realisasi.fetchdata');
    Route::resource('rencana-realisasi-pemupukan', RencanaRealisasiPemupukanController::class);
    // jenis pupuk
    Route::resource('jenis-pupuk', JenisPupukController::class);
    // Route::resource('whatsapp-setting', JenisPupukController::class);

    Route::post('/master-data/import', [MasterDataController::class, 'import'])->name('master-data.import');

    Route::get('/api/kebun-code/{regional}', [PemupukanController::class, 'getKebunByRegionalWithCode']);
    Route::get('/api/afdeling-code/{regional}/{kebun}', [PemupukanController::class, 'getAfdelingByKebunWithCode']);
    Route::get('/api/tahun-tanam-code/{regional}/{kebun}/{afdeling}', [PemupukanController::class, 'getDetailByTahunTanamWithCode']);

    Route::get('/api/kebun/{regional}', [PemupukanController::class, 'getKebunByRegional']);
    Route::get('/api/afdeling/{regional}/{kebun}', [PemupukanController::class, 'getAfdelingByKebun']);
    Route::get('/api/blok/{regional}/{kebun}/{afdeling}', [PemupukanController::class, 'getBlokByAfdeling']);
    Route::get('/api/tahuntanam/{regional}/{kebun}/{afdeling}', [PemupukanController::class, 'getDetailByTahunTanam']);
    Route::get('/api/detail/{regional}/{kebun}/{afdeling}/{blok}', [PemupukanController::class, 'getDetailByBlok']);


    //return data of jenis pupuk
    Route::get('/api/jenis-pupuk', [JenisPupukController::class, 'getJenisPupuk']);

    Route::get('/pemupukan/comparison/{regional}/{kebun?}/{afdeling?}/{tahun_tanam?}/{jenis_pupuk?}', [PemupukanController::class, 'getComparisonDataOfTheChart']);
});

//App Details Page => 'Dashboard'], function() {
// Route::group(['prefix' => 'menu-style'], function () {
//     //MenuStyle Page Routs
//     Route::get('horizontal', [HomeController::class, 'horizontal'])->name('menu-style.horizontal');
//     Route::get('dual-horizontal', [HomeController::class, 'dualhorizontal'])->name('menu-style.dualhorizontal');
//     Route::get('dual-compact', [HomeController::class, 'dualcompact'])->name('menu-style.dualcompact');
//     Route::get('boxed', [HomeController::class, 'boxed'])->name('menu-style.boxed');
//     Route::get('boxed-fancy', [HomeController::class, 'boxedfancy'])->name('menu-style.boxedfancy');
// });

//App Details Page => 'special-pages'], function() {
// Route::group(['prefix' => 'special-pages'], function () {
//     //Example Page Routs
//     Route::get('billing', [HomeController::class, 'billing'])->name('special-pages.billing');
//     Route::get('calender', [HomeController::class, 'calender'])->name('special-pages.calender');
//     Route::get('kanban', [HomeController::class, 'kanban'])->name('special-pages.kanban');
//     Route::get('pricing', [HomeController::class, 'pricing'])->name('special-pages.pricing');
//     Route::get('rtl-support', [HomeController::class, 'rtlsupport'])->name('special-pages.rtlsupport');
//     Route::get('timeline', [HomeController::class, 'timeline'])->name('special-pages.timeline');
// });

//Widget Routs
// Route::group(['prefix' => 'widget'], function () {
//     Route::get('widget-basic', [HomeController::class, 'widgetbasic'])->name('widget.widgetbasic');
//     Route::get('widget-chart', [HomeController::class, 'widgetchart'])->name('widget.widgetchart');
//     Route::get('widget-card', [HomeController::class, 'widgetcard'])->name('widget.widgetcard');
// });

//Maps Routs
// Route::group(['prefix' => 'maps'], function () {
//     Route::get('google', [HomeController::class, 'google'])->name('maps.google');
//     Route::get('vector', [HomeController::class, 'vector'])->name('maps.vector');
// });

//Auth pages Routs
Route::group(['prefix' => 'auth'], function () {
    Route::get('signin', [HomeController::class, 'signin'])->name('auth.signin');
    Route::get('signup', [HomeController::class, 'signup'])->name('auth.signup');
    Route::get('confirmmail', [HomeController::class, 'confirmmail'])->name('auth.confirmmail');
    Route::get('lockscreen', [HomeController::class, 'lockscreen'])->name('auth.lockscreen');
    Route::get('recoverpw', [HomeController::class, 'recoverpw'])->name('auth.recoverpw');
    Route::get('userprivacysetting', [HomeController::class, 'userprivacysetting'])->name('auth.userprivacysetting');
});

//Error Page Route
// Route::group(['prefix' => 'errors'], function () {
//     Route::get('error404', [HomeController::class, 'error404'])->name('errors.error404');
//     Route::get('error500', [HomeController::class, 'error500'])->name('errors.error500');
//     Route::get('maintenance', [HomeController::class, 'maintenance'])->name('errors.maintenance');
// });


//Forms Pages Routs
// Route::group(['prefix' => 'forms'], function () {
//     Route::get('element', [HomeController::class, 'element'])->name('forms.element');
//     Route::get('wizard', [HomeController::class, 'wizard'])->name('forms.wizard');
//     Route::get('validation', [HomeController::class, 'validation'])->name('forms.validation');
// });


//Table Page Routs
// Route::group(['prefix' => 'table'], function () {
//     Route::get('bootstraptable', [HomeController::class, 'bootstraptable'])->name('table.bootstraptable');
//     Route::get('datatable', [HomeController::class, 'datatable'])->name('table.datatable');
// });

//Icons Page Routs
// Route::group(['prefix' => 'icons'], function () {
//     Route::get('solid', [HomeController::class, 'solid'])->name('icons.solid');
//     Route::get('outline', [HomeController::class, 'outline'])->name('icons.outline');
//     Route::get('dualtone', [HomeController::class, 'dualtone'])->name('icons.dualtone');
//     Route::get('colored', [HomeController::class, 'colored'])->name('icons.colored');
// });
//Extra Page Routs
Route::get('privacy-policy', [HomeController::class, 'privacypolicy'])->name('pages.privacy-policy');
Route::get('terms-of-use', [HomeController::class, 'termsofuse'])->name('pages.term-of-use');
