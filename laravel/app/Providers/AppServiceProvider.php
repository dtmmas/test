<?php

namespace App\Providers;

use App\Models\Payment;
use App\Observers\PaymentObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->bind('path.public', function() {
        //     return realpath(base_path().'/../');
        // });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //OBSERVEERS
        Payment::observe(PaymentObserver::class);
        //FIN OBSERVERS
        View::composer('navigation-menu', function ($view)
        {
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            $MenuSistema = array(
                        // array('title' => 'Dashboard', 'route' => 'dashboard','icon' => 'fas fa-tachometer-alt', 'can'=>'dashboard'),
                        // array('title' => 'Roles', 'route' => 'roles.index','icon' => 'fas fa-user-secret', 'can'=>'roles.index'),
                        array('title' => 'Planes', 'route' => 'products.index','icon' => 'fas fa-cubes', 'can'=>'products.index'),
                        array('title' => 'Mis Planes', 'route' => 'products.mine','icon' => 'fas fa-cubes', 'can'=>'products.mine'),
                        // array('title' => 'Planes contratados', 'route' => 'products.asigne','icon' => 'fas fa-cubes', 'can'=>'products.asigne'),
                        // array('title' => 'Categorias', 'route' => 'categories.index','icon' => 'fas fa-sitemap', 'can'=>'categories.index'),
                        array('title' => 'Zonas/Nodos', 'route' => 'zones.index','icon' => 'fas fa-globe', 'can'=>'zones.index'),
                        array('title' => 'Cobradores', 'route' => 'collectors.index','icon' => 'fas fa-hand-holding-usd', 'can'=>'collectors.index'),
                        array('title' => 'Buscar Cliente', 'route' => 'clients.search','icon' => 'fas fa-search', 'can'=>'clients.search'),
                        array('title' => 'Clientes', 'route' => 'clients.index','icon' => 'fas fa-users', 'can'=>'clients.index'),
                        array('title' => 'Facturas', 'route' => 'invoices.index','icon' => 'fas fa-file', 'can'=>'invoices.index'),
                        array('title' => 'Pagos', 'route' => 'payments.index','icon' => 'fas fa-file-powerpoint', 'can'=>'payments.index'),
                        array('title' => 'Pagos Pendientes', 'route' => 'payments.pending','icon' => 'fas fa-file-medical-alt', 'can'=>'payments.index'), 
                        array('title' => 'Reportes', 'route' => '#','icon' => 'fas fa-file-alt', 'can'=>'report.index', 'submenu'=>[
                            array('title' => __('Clientes'), 'route' => 'report.clients', 'can'=>'report.clients'),
                            array('title' => __('Facturas'), 'route' => 'report.invoices', 'can'=>'report.invoices'),
                        ]),
                        array('title' => __('Configuración'), 'route' => 'setting.index', 'icon' => 'fas fa-cogs', 'can'=>'setting.index'),


                        


                    );

            $view->with('MenuSistema',$MenuSistema);
        });
    }
}
