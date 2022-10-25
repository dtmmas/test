<?php

namespace App\Http\Livewire;

use \PDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportInvoiceComponent extends Component
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
        $list_invoices = Invoice::with('client_product')
        ->whereHas('client_product.client.user', function($query){
            
            if($this->status!=''){
                $query->where('invoices.status',$this->status);
            }
            if($this->desde!=''){
                $query->where('invoices.created_at','>=',$this->desde." 00:00:00");
            }
            if($this->hasta!=''){
                $query->where('invoices.created_at','<=',$this->hasta." 23:59:59");
            }
        })        
        ->orderByDesc('created_at')
        ->paginate($this->pagination);

        return view('livewire.report-invoice-component', compact('list_invoices'));
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
        $list_invoices = $this->ListInvoices();

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\InvoiceExport($list_invoices), 'reporte_facturas_'.$this->desde.'.xlsx');
    }

    public function exportPdf()
    {
        $invoices = $this->ListInvoices();
        $nombre = 'Reporte_Facturas_'.fecha_actual().'.pdf';
        $pdf = PDF::loadView('pdfs.report-invoices', compact('invoices'))
        ->setPaper('a4', 'landscape')
        ->output();
        return response()->streamDownload(
             fn () => print($pdf),
             $nombre
        );
    }

    public function ListInvoices()
    {
        return $list_invoices = Invoice::with('client_product')
        ->whereHas('client_product.client.user', function($query){
            if($this->status!=''){
                $query->where('invoices.status',$this->status);
            }
            if($this->desde!=''){
                $query->where('invoices.created_at','>=',$this->desde." 00:00:00");
            }
            if($this->hasta!=''){
                $query->where('invoices.created_at','<=',$this->hasta." 23:59:59");
            }
        }) 
        ->orderByDesc('created_at')
        ->get();
    }

    protected $listeners = [
        'onFiltrar' =>'Filtrar'
    ];
}
