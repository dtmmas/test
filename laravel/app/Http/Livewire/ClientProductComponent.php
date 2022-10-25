<?php

namespace App\Http\Livewire;

use App\Models\Client;
use App\Models\ClientProduct;
use App\Models\Node;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Zone;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientProductComponent extends Component
{
    use WithPagination;

    protected $paginationTheme= 'bootstrap';

    public $client;
    public $pivot;
    public $product;

    public $products;
    public $zones;
    public $nodes = [];
    public $product_id;
    public $zone_id;
    public $node_id;

    public $address;
    public $reference;
    public $date_instalation;
    public $amount_installation;
    public $advance='0';
    public $status;
    //variables para fucnionalidades
    public $selected_id, $search;
    public $createMode = true; //creando
    public $modeResume = false;
    private $pagination = 15;
    public $cp = '';

    //create invoice
    public $fecha_factura;
    public $estado_factura;

    //pagos delantados
    public $monto_advancement;
    public $meses_advancement;

    protected $queryString = ['modeResume'=> ['except' => ''],'cp'=> ['except' => '']];

    public function mount($client)
    {
        $this->client = $client;
        $this->zones = Zone::select(DB::raw("CONCAT(id,' / ',name) AS name_code"), 'id')->pluck('name_code','id');

        if($this->modeResume){
            $this->detail($this->cp);
        }
    }

    public function render()
    {
        if($this->search!=''){
            $list_products = $this->client->products()->where(function($query){
                $query->orwhere('address','LIKE','%'.$this->search.'%');
            })
            ->paginate($this->pagination);
        }else{
            $list_products = $this->client->products()->paginate($this->pagination);
        }

        $this->products = Product::select(DB::raw("CONCAT(id,' / ',name) AS name_code"), 'id')->pluck('name_code','id');

        if(!empty($this->zone_id)){
            $this->nodes = Node::select(DB::raw("CONCAT(id,' / ',name) AS name_code"), 'id')
            ->where('zone_id', $this->zone_id)
            ->pluck('name_code','id');
        }

        return view('livewire.client-product-component', compact('list_products'));
    }
    
    public function updatingSearch()
    {
        //otra opcion seria $this->resetPage();
        $this->gotoPage(1);
    }

    public function resetInputFields()
    {
        $this->address = '';
        $this->reference = '';
        $this->date_instalation = '';
        $this->amount_installation = '';
        $this->advance = '0';
        
        $this->nodes='';
        $this->product_id='';
        $this->zone_id='';
        $this->node_id='';

        $this->product_id='';
        $this->selected_id = null;
        $this->createMode = true;
        $this->modeResume = false;
        $this->emit('verTablaForm', 'Listado de Zonas');
    }
    
    public function resetInputFieldsInvoice()
    {
        $this->fecha_factura='';
        $this->estado_factura='';
    }
    
    public function resetInputFieldsAdvancement()
    {
        $this->monto_advancement='';
        $this->meses_advancement='';
    }

    public function update()
    {  
        abort_if(!Auth::user()->can('clients.edit'), 401);

        if ($this->product_id) {
            $this->client->products()->attach([
                $this->product_id => [
                    'address' => $this->address ,
                    'reference' => $this->reference ,
                    'date_instalation' => $this->date_instalation ,
                    'amount_installation' => ((is_numeric($this->amount_installation))?$this->amount_installation:0) ,
                    'advance' => $this->advance ,
                    'node_id' => $this->node_id ,
                ]
            ]);
            $this->emit('productUpdate','Plan contratado correctamente.');
            $this->resetInputFields();
        }
    }

    public function destroy($id)
    {
        abort_if(!Auth::user()->can('clients.edit'), 401);
        ClientProduct::find($id)->update([
            'status' =>'-1'
        ]);
        $this->pivot = ClientProduct::find($id);
        $this->emit('productDelete');
    }

    public function detail($cp)
    {
        $this->cp = $cp;
        $this->pivot = ClientProduct::find($cp);
        $this->product = Product::findOrFail($this->pivot->product_id);
        $this->client = Client::with('user')->findOrFail($this->pivot->client_id);
        $this->modeResume = true;
    }

    public function updateMode($mode)
    {
        $this->modeResume = $mode;
    }

    public function storeInvoice(){
        abort_if(!Auth::user()->can('invoices.create'), 401);
        //validar informacion
        $this->validate([
            'fecha_factura' => ['required', 'string', ],
            'estado_factura' => ['required','in:1,-1'],
        ]);
        
        if(Invoice::where('created_at','LIKE',$this->fecha_factura.'%')->where('client_product_id',$this->pivot->id)->exists()){
            $this->emit('clientStoreInvoice','Ya existe factura para ese mes.',"warning");
        }else{
            $user = Invoice::create([
                'user_id' => Auth::user()->id,
                'client_product_id' =>$this->pivot->id,
                'price' => $this->product->price,
                'date_expiration' => ultimoDiaMesFecha($this->fecha_factura.'-05'),
                'created_at' => $this->fecha_factura.'-'.date('d'),
                'status' => $this->estado_factura
            ]);

            $this->resetInputFieldsInvoice();
            $this->emit('clientStoreInvoice','Factura creada correctamente.',"success"); // Close model to using to jquery
        }
    }

    public function storePaymentAdvancement(){
        abort_if(!Auth::user()->can('payments.advancement'), 401);
        //validar informacion
        $this->validate([
            'monto_advancement' => ['required', 'numeric', ],
            'meses_advancement' => ['required','numeric','min:1'],
        ]);
        
        $Contratacion = ClientProduct::find($this->pivot->id);
        $meses_total = $Contratacion->payments_advancement+$this->meses_advancement;
        $Contratacion->update([
            'payments_advancement' =>  $meses_total
        ]);

        $Invoice = Invoice::create([
                'client_product_id' =>$this->pivot->id,
                'price' => $this->monto_advancement,
                'date_expiration' => ultimoDiaMesSumado($meses_total-1),
                'status' => '1',
                'payments_advancement' => $this->meses_advancement
            ]);

        $paymentCreated = $Invoice->payments()->create([
            'no_voucher' => date('YmdHis'),
            'price' => $this->monto_advancement,
            'img_voucher' =>  null,
            'observation' => 'PAGO '.$this->meses_advancement.' MES(ES) POR ADELANTADO',
            'method_payment' => '3',
        ]);

        $payment = $paymentCreated;//Payment::findOrFail($paymentCreated->id);
        $payment->status = '1';
        $payment->save();

        $this->resetInputFieldsAdvancement();
        $this->emit('storePaymentAdvancement','Adelanto asociado correctamente.',"success"); // Close model to using to jquery
    }


    //listeners / escuchar eventos js o php y ejecutar acciones livewire
    protected $listeners = [
        'onDeleteRow' =>'destroy',
        'onupdateMode' => 'updateMode',
        'onRefreshCP' =>'$refresh',
    ];

}
