<div>
    <style>
        .hidden{
            display: none;
        }
    </style>
    @if ($mostrarCliente)
    <div class=" mb-3 col-xs-12 col-sm-8">
        <div class="input-group col">
            <input wire:model="search" type="text" class="form-control mayusculas" placeholder="Buscar por cliente o No. Boleta o Transferencia No.">
            <span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
        </div>
    </div> 

    <div class="row">
        <div class="form-group col-md-5">
            {!! Form::label('name', __('Desde') ); !!}
            <input wire:model.defer="desde" id="desde" type="date" data-date-format="yyyy-mm-dd" class="form-control " autocomplete="off" >
            @error('desde') <span class="text-danger">{{ $message }}</span>@enderror
        </div>
        
        <div class="form-group col-md-5">
            {!! Form::label('name', __('Hasta') ); !!}
            <input wire:model.defer="hasta" id="hasta" type="date" data-date-format="yyyy-mm-dd" class="form-control " autocomplete="off" >
            @error('hasta') <span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <div class="form-group  col-md-2"><br>
            <button class="btn btn-info " wire:click="updatingSearch" >Consultar</button>
        </div>
    </div>      
    @endif
    
    <div class="relative">
        <div   id="parent" class=" ">
            <div class="table-responsive2">
                <table class="table table-striped ">
                    <thead>
                        <tr>
                            <th>No. Recibo</th>
                            <th>Fecha</th>
                            @if ($mostrarCliente)
                            <th>Cliente</th>
                            @endif
                            <th>Mes</th>
                            <th>Reportado por</th>
                            @if ($mostrarPlan)
                            <th>Plan</th>
                            <th>Direccion</th>
                            @endif
                            <th >Img Transferencia</th>
                            <th >Transferencia No.</th>
                            <th>Monto</th>
                            <th >Metodo de pago</th>
                            @if (!$mostrarPendientes)
                            <th>Estado</th>
                            @endif
                            @if ($mostrarPendientes)
                                @can('payments.process')
                                <th >Procesar</th>        
                                @endcan
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($list_payments as $payment)
                            <tr>
                                <td data-th="Id">{{$payment->numero }}</td>
                                <td data-th="Fecha">{{$payment->created_at }}</td>
                                @if ($mostrarCliente)
                                <td  data-th="Cliente">{{$payment->name_client}}</td>
                                @endif
                                <td  data-th="Cliente">{{$payment->invoice->getMesInvoice()}}</td>
                                <td  data-th="Cliente">{{$payment->getReportadoPor()}}</td>
                                @if ($mostrarPlan)
                                <td  data-th="Plan">{{$payment->name_product}}</td>
                                <td  data-th="Direccion">{{$payment->address_plan}}</td>
                                @endif
                                <td>
                                    @if ($payment->img_voucher)
                                    <a href="{{Storage::url($payment->img_voucher)}}" target="_blank" rel="noopener noreferrer">
                                        <img style="max-width: 150px;" class="img_comprobante" src="{{Storage::url($payment->img_voucher)}}" alt=""/>
                                    </a>
                                    @else
                                       N/A 
                                    @endif
                                </td>
                                <td  data-th="No. Comprobante">
                                    {{$payment->no_voucher }}
                                    <br>
                                    <a href="{{ route('payments.pdf', $payment->id) }}" target="_blank" rel="noopener noreferrer">Ver</a>
                                </td>
                                <td  data-th="Monto">{{$payment->price_formatted }}</td>
                                <td  data-th="Metodo de pago">{!!$payment->method_payment_html !!}</td>
                                @if (!$mostrarPendientes)
                                <td  data-th="Estado">{!!$payment->status_html !!}</td>
                                @endif
                                @if ($mostrarPendientes)
                                    @can('payments.process')
                                    <td  data-th="Procesar">                                    
                                        <a onclick="ProcesarPago('{{$payment->id}}','{{$payment->no_voucher}}','1')" class="btn-xs btn btn-success" href="#!">Aprobar</a>
                                        <a onclick="ProcesarPago('{{$payment->id}}','{{$payment->no_voucher}}','0')" class="btn-xs btn btn-danger" href="#!">Rechazar</a>
                                    </td>        
                                    @endcan
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="10">{{ (($invoice_id=='9999999')?'Cargando...':'Sin reportar') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div>
        {{ $list_payments->links() }}
    </div>

</div>
