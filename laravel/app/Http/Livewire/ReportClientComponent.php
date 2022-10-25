<?php

namespace App\Http\Livewire;

use \PDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportClientComponent extends Component
{
    use WithPagination;

    protected $paginationTheme= 'bootstrap';

    //variables de nuestro modelo
    public $desde='';
    public $hasta='';
    public $status='';
    //variables para fucnionalidades
    public $search;

    private $pagination = 25;

    public function render()
    {   
        $list_clients = Client::with('client_products','client_products.invoices','user')
        ->whereHas('user',function($query){
            if($this->status=='1'){//Cliente Activo
                $query->WhereRaw(' (SELECT count(invoices.id) FROM invoices WHERE status="-1" and client_product_id IN (SELECT id FROM client_product WHERE client_id = clients.id) ) = ?',['0']);
            }
            if($this->status=='0'){//Cliente Moroso
                $query->WhereRaw(' (SELECT count(invoices.id) FROM invoices WHERE status="-1" and client_product_id IN (SELECT id FROM client_product WHERE client_id = clients.id) ) != ?',['0']);
            }
            if($this->desde!=''){
                $query->where('created_at','>=',$this->desde." 00:00:00");
            }
            if($this->hasta!=''){
                $query->where('created_at','<=',$this->hasta." 23:59:59");
            }
        })        
        ->paginate($this->pagination);

        return view('livewire.report-client-component', compact('list_clients'));
    }

    public function Filtrar($desde, $hasta)
    {
        if($desde!=''){
            $this->desde = date('Y-m-d', strtotime($desde));
        }
        if($hasta!=''){
            $this->hasta = date('Y-m-d', strtotime($hasta));
        }
        
        $this->gotoPage(1);
    }

    public function exportExcel() {
        
        $list_clients = $this->ListClients();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ClientExport($list_clients), 'reporte_clientes_'.$this->desde.'.xlsx');
    }

    public function exportPdf()
    {
        $clientes = $this->ListClients();
        $nombre = 'Reporte_Clientes_'.fecha_actual().'.pdf';
        $pdf = PDF::loadView('pdfs.report-clients', compact('clientes'))
        ->setPaper('a4', 'landscape')
        ->output();
        return response()->streamDownload(
             fn () => print($pdf),
             $nombre
        );
    }

    public function ListClients(){
        return $list_clients = Client::with('client_products','client_products.invoices','user')
        ->whereHas('user',function($query){
            
            if($this->status=='1'){//Cliente Activo
                $query->WhereRaw(' (SELECT count(invoices.id) FROM invoices WHERE status="-1" and client_product_id IN (SELECT id FROM client_product WHERE client_id = clients.id) ) = ?',['0']);
            }
            if($this->status=='0'){//Cliente Moroso
                $query->WhereRaw(' (SELECT count(invoices.id) FROM invoices WHERE status="-1" and client_product_id IN (SELECT id FROM client_product WHERE client_id = clients.id) ) != ?',['0']);
            }
            if($this->desde!=''){
                $query->where('created_at','>=',$this->desde." 00:00:00");
            }
            if($this->hasta!=''){
                $query->where('created_at','<=',$this->hasta." 23:59:59");
            }
        }) 
        ->get();
    }
    //listeners / escuchar eventos js o php y ejecutar acciones livewire
    protected $listeners = [
        'onFiltrar' =>'Filtrar'
    ];
}
