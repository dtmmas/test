<x-app-layout>
    <x-slot name="titulo_pagina">
        {{ __('Cliente: '.$user->name_complete) }}
    </x-slot>
    <x-slot name="opciones_nav">
        <ol class="breadcrumb ms-auto">
            
        </ol>
    </x-slot>
    @include('yield.resume-user')
    <h3>Planes Contratados</h3>
    <div  class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Plan</th>
                    <th>Dirección</th>
                    <th>Referencia</th>
                    <th>Zona/Nodo</th>
                    <th>Fecha de Instalacion</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($list_products as $product)
                    <tr>
                        <td>{{$product->name}}</td>
                        <td>{{$product->pivot->address}}</td>
                         <td>{{$product->pivot->reference}}</td>
                        <td>{{$product->pivot->node->zone->name.'/'.$product->pivot->node->name}}</td>
                        <td>{{$product->pivot->date_instalation}}</td>
                        <td>{!! $product->pivot->status_html!!}</td> 
                        <td style="width: 10px">
                            <a class="btn btn-info" href="{{ url('clients/'.$client->id.'/products?modeResume=true&cp='.$product->pivot->id) }}">Detalles</a>
                        </td>                          
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay resultados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div>
        {{ $list_products->links() }}
    </div>
    
    <h3>Facturación Mensual</h3>
    @livewire('invoice-component', ['pivot'=>null,'client'=>$client])

    <x-slot name="script">
        <script type="text/javascript">
            window.livewire.on('paymentUpdateStore', (message, id) => {
                swal(message, {icon: "success",});
                $('#ModalPagar'+id).modal('hide');
            });
            
        </script>
    </x-slot>
</x-app-layout>