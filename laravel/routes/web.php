<?php

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;
use App\Models\Client;
use App\Models\ClientProduct;
use App\Models\Collector;
use App\Models\Product;
use App\Models\Log;
use App\Models\Payment;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;
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

// use Illuminate\Support\Facades\Artisan;

// Route::get('storage-link', function(){
//     Artisan::call('storage:link');
// });

Route::get('/esp', function () {
    $u = App\Models\User::all();
    foreach ($u as $key => $user) {
        $user->name = trim($user->name);
        $user->dni = trim($user->dni);
        $user->lastname = trim($user->lastname);
        $user->update();
    }
});

Route::get('/', function () {
    return view('auth.login');
});
                
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {

    if(Auth::user()->hasRole('Cliente') || Auth::user()->hasRole('Cobrador')){
        return view('invoices.index');
    }
    $list_logs = Log::latest('id')->paginate(40);
    $payments =new Payment;
    $total_deposito = $payments->totalPayment('1');
    $total_tarjeta = $payments->totalPayment('2');
    $total_efectivo = $payments->totalPayment('3');
    $total_clientes = Client::count();
    $total_pagos_pendientes = $payments->totalPayment('0');

    return view('dashboard', compact('list_logs','total_deposito','total_tarjeta','total_efectivo','total_clientes','total_pagos_pendientes'));
})->name('dashboard');


Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::view('users','users.index')->middleware(['can:users.index'])->name('users.index');
    Route::view('roles','roles.index')->middleware(['can:roles.index'])->name('roles.index');

    //PLAES / PRODUCTOS
    Route::view('planes','products.index')->middleware(['can:products.index'])->name('products.index');
    Route::view('mis-planes','products.mine')->middleware(['can:products.mine'])->name('products.mine');
    Route::view('planes-asignados','products.asigne')->middleware(['can:products.asigne'])->name('products.asigne');
    
    //ZONAS
    Route::view('zones','zones.index')->middleware(['can:zones.index'])->name('zones.index');
    Route::get('zones/{zone}/nodes',function(Zone $zone)
    {
        return view('zones.nodes', compact('zone'));
    })->middleware(['can:zones.index'])->name('zones.nodes');


    //COLLECTORS
    Route::view('collectors','collectors.index')->middleware(['can:collectors.index'])->name('collectors.index');
    Route::get('collectors/{user}',function(Collector $user)
    {
        return view('collectors.show', compact('user'));
    })->middleware(['can:collectors.index'])->name('collectors.show');

    Route::get('collectors/{collector}/zones',function(Collector $collector)
    {
        return view('collectors.zones', compact('collector'));
    })->middleware(['can:collectors.index'])->name('collectors.zones');

    //CLIENTES
    Route::view('clients','clients.index')->middleware(['can:clients.index'])->name('clients.index');
    Route::view('clients-search','clients.search')->middleware(['can:clients.search'])->name('clients.search');
    Route::get('clients/{client}',function(Client $client)
    {///clients/1/products?modeResume=true&cp=1 URL QUEMADA
        // if(Auth::user()->hasRole('Cobrador')){
        //     $cat = Collector::find(Auth::user()->id);
        //     $clientes =  $cat->zones()
        //     ->has('nodes.client_products.client')
        //     ->with('nodes.client_products.client.user')
        //     ->get()
        //     ->pluck('nodes')->collapse()
        //     ->pluck('client_products')->collapse()
        //     ->pluck('client.id')->unique()->values()->toArray();;
        //     // dd($clientes);
        //     abort_if(!(in_array($client->id, $clientes)), 401);
            
        // }
        $user = $client->user;
        $user->reference = $client->reference;
        $user->ip = $client->ip;
        $user->clave_wifi = $client->clave_wifi;

        $list_products = $client->products()->paginate();

        return view('clients.show', compact('user','client','list_products'));
    })->middleware(['can:clients.index'])->name('clients.show');

    Route::get('clients/{client}/products',function(Client $client)
    {
        // if(Auth::user()->hasRole('Cobrador')){
        //     $cat = Collector::find(Auth::user()->id);
        //     $clientes =  $cat->zones()
        //     ->has('nodes.client_products.client')
        //     ->with('nodes.client_products.client.user')
        //     ->get()
        //     ->pluck('nodes')->collapse()
        //     ->pluck('client_products')->collapse()
        //     ->pluck('client.id')->unique()->values()->toArray();
        //     abort_if(!(in_array($client->id, $clientes)), 401);
            
        // }
        return view('clients.products', compact('client'));
    })->middleware(['can:clients.index'])->name('clients.products');
    Route::view('importar-clientes','clients.importar')->middleware(['can:clients.importar'])->name('clients.importar');

    //FACTURAS
    Route::view('invoices','invoices.index')->middleware(['can:invoices.index'])->name('invoices.index');

    //PAYMENTS
    Route::view('payments','payments.index',['mostrarPendientes'=>false])->middleware(['can:payments.index'])->name('payments.index');
    Route::view('payments/pending','payments.index',['mostrarPendientes'=>true])->middleware(['can:payments.index'])->name('payments.pending');
    Route::get('payments/pdf/{payment_id}', [PdfController::class,'payment'])->name('payments.pdf');

    //MI PERFIL
    Route::get('perfil',function()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    })->name('myprofile');

    // Route::view('categories','categories.index')->middleware(['can:categories.index'])->name('categories.index');
    // Route::get('subcategories/{category}', [CategoryController::class, 'subcategories'])->name('subcategories.index');

    //REPORTS
    Route::view('reportes-clientes','report.client')->middleware(['can:report.clients'])->name('report.clients');
    Route::view('reportes-facturas','report.invoice')->middleware(['can:report.invoices'])->name('report.invoices');

    //settings
    Route::view('configuracion','setting.index')->middleware(['can:setting.index'])->name('setting.index');
});

