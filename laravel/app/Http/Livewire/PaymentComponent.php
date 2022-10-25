<?php

namespace App\Http\Livewire;

use App\Models\Collector;
use App\Models\Payment;
use App\Traits\FunctionGeneral;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class PaymentComponent extends Component
{
    use WithPagination, FunctionGeneral;

    protected $paginationTheme= 'bootstrap';

    public $invoice_id;


    public $mostrarPendientes = false;
    public $mostrarCliente = false;
    public $mostrarPlan = false;
    public $search;
    public $desde='';
    public $hasta='';

    public function mount($invoice_id, $mostrarPendientes)
    {
        if(!empty($invoice_id)){
            $this->invoice_id = $invoice_id;
        }

        if(!empty($mostrarPendientes)){
            $this->mostrarPendientes = $mostrarPendientes;
        }
    }
    
    public function render()
    {

       if($this->invoice_id>0){
            $list_payments = Payment::with('invoice')
            ->where('invoice_id',$this->invoice_id)
            ->where(function($query){
                if($this->search!=''){
                    $query->where('users.name','LIKE','%'.$this->search.'%');
                    $query->orWhereRaw(' CONCAT(users.name," ",users.lastname) LIKE ?',['%'.$this->search.'%']);
                    $query->orWhereRaw(' LPAD(CONCAT(payments.id,DATE_FORMAT(payments.created_at,"%Y%m")),8,"0")  LIKE ?',['%'.$this->search.'%']);
                    $query->orWhereRaw(' payments.no_voucher LIKE ?',['%'.$this->search.'%']);
                }
                
            })->where( function($query){
                if($this->desde!=''){
                    $query->whereRaw('payments.created_at >= ?',[$this->desde.' 00:00:00']);
                }
                
                if($this->hasta!=''){
                    $query->whereRaw('payments.created_at <= ?',[ $this->hasta.' 23:59:59']);
                }
            })
            ->latest()
            ->paginate();
       }else{

        if(Auth::user()->hasRole('Cobrador')){
            $cat = Collector::find(Auth::user()->id);
            $list_payments = Payment::with('invoice.client_product', )
            ->whereHas('invoice.client_product.client.user',function($query)  use ( $cat){
                if($this->search!=''){
                    $query->whereRaw(' CONCAT(users.name," ",users.lastname) LIKE ?',['%'.$this->search.'%']);
                    $query->orWhereRaw(' LPAD(CONCAT(payments.id,DATE_FORMAT(payments.created_at,"%Y%m")),8,"0")  LIKE ?',['%'.$this->search.'%']);
                    $query->orWhereRaw(' payments.no_voucher LIKE ?',['%'.$this->search.'%']);
                }
                

                if(Auth::user()->hasRole('Cliente')){
                    $query->where('users.id',Auth::user()->id);
                }

                if($this->mostrarPendientes){
                    $query->where('payments.status','0');
                }

            })->where( function($query){
                if($this->desde!=''){
                    $query->whereRaw('payments.created_at >= ?',[$this->desde.' 00:00:00']);
                }
                
                if($this->hasta!=''){
                    $query->whereRaw('payments.created_at <= ?',[ $this->hasta.' 23:59:59']);
                }
            })->where('user_id',$cat->id)
           
            ->latest()
            ->paginate();

        }else{
            $list_payments = Payment::with('invoice.client_product', )
            ->whereHas('invoice.client_product.client.user',function($query){
                if($this->search!=''){
                    $query->whereRaw(' CONCAT(users.name," ",users.lastname) LIKE ?',['%'.$this->search.'%']);
                    $query->orWhereRaw(' LPAD(CONCAT(payments.id,DATE_FORMAT(payments.created_at,"%Y%m")),8,"0")  LIKE ?',['%'.$this->search.'%']);
                    $query->orWhereRaw(' payments.no_voucher LIKE ?',['%'.$this->search.'%']);
                }
                

                if(Auth::user()->hasRole('Cliente')){
                    $query->where('users.id',Auth::user()->id);
                }

                if($this->mostrarPendientes){
                    $query->where('payments.status','0');
                }
            })->where( function($query){
                if($this->desde!=''){
                    $query->whereRaw('payments.created_at >= ?',[$this->desde.' 00:00:00']);
                }
                
                if($this->hasta!=''){
                    $query->whereRaw('payments.created_at <= ?',[ $this->hasta.' 23:59:59']);
                }
            })
            ->latest()
            ->paginate();
        }
        if(Auth::user()->hasRole('Cliente')){
            $this->mostrarCliente = false;
        }else{
            $this->mostrarCliente = true;
        }
        
        $this->mostrarPlan = true;
       }
       
        return view('livewire.payment-component', compact('list_payments'));
    }

    public function processPayment($id, $tipo)
    {
        abort_if(!Auth::user()->can('payments.process'), 401);
        $payment = Payment::findOrFail($id);
        $payment->status = (($tipo=='1')?'1':'-1');
        $payment->save();
        $this->emit('ProcessPayment');
    }

    public function updatingSearch()
    {
        //otra opcion seria $this->resetPage();
        $this->gotoPage(1);
    }

    public function actInvoices($id)
    {
        $this->invoice_id =  $id;
        $this->emit('$refresh');
    }
    //listeners / escuchar eventos js o php y ejecutar acciones livewire
    protected $listeners = [
        'onProcessPaymentRow' =>'processPayment'
        ,'PaymentComponent:actInvoice' =>'actInvoices'
    ];
}
