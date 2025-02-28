<?php

namespace App\Http\Controllers;

use App\Models\JenisPupuk;
use App\Models\Pemupukan;
use App\Models\RencanaPemupukan;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /*
     * Dashboard Pages Routs
     */
    public function index(Request $request)
    {
        $assets = ['chart', 'animation'];

        $user = $request->user();
        $user_type = $user->user_type;
        $regional = $user->regional;

        if ($user_type == 'admin' && $regional == 'head_office') {
            // count all data rencana_pemupukan
            $rencana_pemupukan = RencanaPemupukan::count();
            // count all data pemupukan
            $pemupukan = Pemupukan::count();
            // count all data jenis_pupuk
            $jenis_pupuk = JenisPupuk::count();

            // count total jumlah_pupuk pemupukan
            $jumlah_pupuk = Pemupukan::sum('jumlah_pupuk');

            // count total jumlah_pupuk renacana_pemupukan
            $jumlah_pupuk_rencana = RencanaPemupukan::sum('jumlah_pupuk');

            // count total users
            $users = User::count();
        } elseif ($user_type == 'admin' && $regional != 'head_office') {
            // count all data rencana_pemupukan by regional
            $rencana_pemupukan = RencanaPemupukan::where('regional', $regional)->count();
            // count all data pemupukan by regional
            $pemupukan = Pemupukan::where('regional', $regional)->count();
            // count all data jenis_pupuk
            $jenis_pupuk = JenisPupuk::count();

            // count total jumlah_pupuk pemupukan by regional
            $jumlah_pupuk = Pemupukan::where('regional', $regional)->sum('jumlah_pupuk');

            // count total jumlah_pupuk renacana_pemupukan by regional
            $jumlah_pupuk_rencana = RencanaPemupukan::where('regional', $regional)->sum('jumlah_pupuk');

            // count total users by regional
            $users = User::where('regional', $regional)->count();
        } else {
            // handle other user types if necessary
            $rencana_pemupukan = 0;
            $pemupukan = 0;
            $jenis_pupuk = 0;
            $jumlah_pupuk = 0;
            $jumlah_pupuk_rencana = 0;
            $users = 0;
        }

        // get percentage of jumlah_pupuk pemupukan
        $percentage_pemupukan = ($jumlah_pupuk_rencana > 0) ? ($jumlah_pupuk / $jumlah_pupuk_rencana) * 100 : 0;

        return view('dashboards.dashboard', compact('assets', 'rencana_pemupukan', 'pemupukan', 'jenis_pupuk', 'jumlah_pupuk', 'jumlah_pupuk_rencana', 'percentage_pemupukan', 'users', 'user'));
    }

    /*
     * Menu Style Routs
     */
    public function horizontal(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.horizontal', compact('assets'));
    }
    public function dualhorizontal(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.dual-horizontal', compact('assets'));
    }
    public function dualcompact(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.dual-compact', compact('assets'));
    }
    public function boxed(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.boxed', compact('assets'));
    }
    public function boxedfancy(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.boxed-fancy', compact('assets'));
    }

    /*
     * Pages Routs
     */
    public function billing(Request $request)
    {
        return view('special-pages.billing');
    }

    public function calender(Request $request)
    {
        $assets = ['calender'];
        return view('special-pages.calender', compact('assets'));
    }

    public function kanban(Request $request)
    {
        return view('special-pages.kanban');
    }

    public function pricing(Request $request)
    {
        return view('special-pages.pricing');
    }

    public function rtlsupport(Request $request)
    {
        return view('special-pages.rtl-support');
    }

    public function timeline(Request $request)
    {
        return view('special-pages.timeline');
    }

    /*
     * Widget Routs
     */
    public function widgetbasic(Request $request)
    {
        return view('widget.widget-basic');
    }
    public function widgetchart(Request $request)
    {
        $assets = ['chart'];
        return view('widget.widget-chart', compact('assets'));
    }
    public function widgetcard(Request $request)
    {
        return view('widget.widget-card');
    }

    /*
     * Maps Routs
     */
    public function google(Request $request)
    {
        return view('maps.google');
    }
    public function vector(Request $request)
    {
        return view('maps.vector');
    }

    /*
     * Auth Routs
     */
    public function signin(Request $request)
    {
        return view('auth.login');
    }
    public function signup(Request $request)
    {
        return view('auth.register');
    }
    public function confirmmail(Request $request)
    {
        return view('auth.confirm-mail');
    }
    public function lockscreen(Request $request)
    {
        return view('auth.lockscreen');
    }
    public function recoverpw(Request $request)
    {
        return view('auth.recoverpw');
    }
    public function userprivacysetting(Request $request)
    {
        return view('auth.user-privacy-setting');
    }

    /*
     * Error Page Routs
     */

    public function error404(Request $request)
    {
        return view('errors.error404');
    }

    public function error500(Request $request)
    {
        return view('errors.error500');
    }
    public function maintenance(Request $request)
    {
        return view('errors.maintenance');
    }

    /*
     * uisheet Page Routs
     */
    public function uisheet(Request $request)
    {
        return view('uisheet');
    }

    /*
     * Form Page Routs
     */
    public function element(Request $request)
    {
        return view('forms.element');
    }

    public function wizard(Request $request)
    {
        return view('forms.wizard');
    }

    public function validation(Request $request)
    {
        return view('forms.validation');
    }

    /*
     * Table Page Routs
     */
    public function bootstraptable(Request $request)
    {
        return view('table.bootstraptable');
    }

    public function datatable(Request $request)
    {
        return view('table.datatable');
    }

    /*
     * Icons Page Routs
     */

    public function solid(Request $request)
    {
        return view('icons.solid');
    }

    public function outline(Request $request)
    {
        return view('icons.outline');
    }

    public function dualtone(Request $request)
    {
        return view('icons.dualtone');
    }

    public function colored(Request $request)
    {
        return view('icons.colored');
    }

    /*
     * Extra Page Routs
     */
    public function privacypolicy(Request $request)
    {
        return view('privacy-policy');
    }
    public function termsofuse(Request $request)
    {
        return view('terms-of-use');
    }

    /*
     * Landing Page Routs
     */
    public function landingIndex(Request $request)
    {
        return view('landing-pages.pages.index');
    }
    public function landingBlog(Request $request)
    {
        return view('landing-pages.pages.blog');
    }
    public function landingAbout(Request $request)
    {
        return view('landing-pages.pages.about');
    }
    public function landingBlogDetail(Request $request)
    {
        return view('landing-pages.pages.blog-detail');
    }
    public function landingContact(Request $request)
    {
        return view('landing-pages.pages.contact-us');
    }
    public function landingEcommerce(Request $request)
    {
        return view('landing-pages.pages.ecommerce-landing-page');
    }
    public function landingFaq(Request $request)
    {
        return view('landing-pages.pages.faq');
    }
    public function landingFeature(Request $request)
    {
        return view('landing-pages.pages.feature');
    }
    public function landingPricing(Request $request)
    {
        return view('landing-pages.pages.pricing');
    }
    public function landingSaas(Request $request)
    {
        return view('landing-pages.pages.saas-marketing-landing-page');
    }
    public function landingShop(Request $request)
    {
        return view('landing-pages.pages.shop');
    }
    public function landingShopDetail(Request $request)
    {
        return view('landing-pages.pages.shop_detail');
    }
    public function landingSoftware(Request $request)
    {
        return view('landing-pages.pages.software-landing-page');
    }
    public function landingStartup(Request $request)
    {
        return view('landing-pages.pages.startup-landing-page');
    }
}
