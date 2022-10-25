<x-app-layout>
    <x-slot name="titulo_pagina">
        {{ __('Dashboard') }}
    </x-slot>
    <x-slot name="opciones_nav">
        <ol class="breadcrumb ms-auto">
            {{-- <li><a href="#" class="fw-normal">Dashboard</a></li> --}}
        </ol>
    </x-slot>

    @if (Auth::user()->hasRole('Admin'))
        <x-slot name="widgets">
            <div class="col-lg-4 col-md-12">
                <x-widget title="Total Pagos Deposito" color="primary" :total="$total_deposito"/>
            </div>
            <div class="col-lg-4 col-md-12">
                <x-widget title="Total Pagos Tarjeta" color="info" :total="$total_tarjeta"/>
            </div>
            <div class="col-lg-4 col-md-12">
                <x-widget title="Total Pagos Efectivo" color="success" :total="$total_efectivo"/>
            </div>
            <div class="col-lg-4 col-md-12">
                <x-widget title="Total Clientes" color="success" :total="$total_clientes"/>
            </div>
            <div class="col-lg-4 col-md-12">                
                <x-widget title="Total Pagos Pendientes" color="warning" :total="$total_pagos_pendientes"/>
            </div>
        </x-slot>

        <div class="row text-center mb-3">
            <button onclick="GenerarFacturas()" class="btn btn-danger">Generar Facturas</button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($list_logs as $log)
                        <tr>
                            <td>{{$log->created_at}}</td>
                            <td>{{$log->detalle}}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No se ha ejecutado el cron</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>
            {{ $list_logs->links() }}
        </div>
    @endif

     <x-slot name="script">
        @if (Auth::user()->hasRole('Admin'))
        <script type="text/javascript">            
            function GenerarFacturas(){
                swal({
                    title: "¿Estas seguro?",
                    text: "Solo puedes generar facturas una sola vez al mes",
                    icon: "warning",
                    buttons: {
                        cancel: "Cancelar",
                        catch: {
                            text: "Si, Generar",
                            closeModal: false,
                            className: 'swal-button--danger'
                        },
                    },
                  })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "laravel/cronjob_factura.php", 
                            success: function(result){
                                swal({
                                    title: "Proceso finalizado",
                                    text: result,
                                    icon: "warning",
                                })
                            },error: function(XMLHttpRequest, textStatus, errorThrown){ 
                                swal({
                                    title: "Proceso finalizado",
                                    text: XMLHttpRequest.responseText,
                                    icon: "warning",
                                })
                            }
                        });
                    }
                });
            }
        </script>
        @endif
        
    </x-slot>
</x-app-layout>
