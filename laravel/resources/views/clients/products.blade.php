<x-app-layout>
    <x-slot name="titulo_pagina">
        {{ __('Planes contratados por: '.$client->user->name_complete) }}
    </x-slot>
    <x-slot name="opciones_nav">
        <ol class="breadcrumb ms-auto">
        </ol>
    </x-slot>
    @livewire('client-product-component', ['client'=>$client])

    <x-slot name="script">
        
        <script type="text/javascript">            
            window.livewire.on('productUpdate', (message) => {
                swal(message, {icon: "success",});
                $('#exampleModal').modal('hide');
            });

            window.livewire.on('clientStoreInvoice', (message, icon) => {
                swal(message, {icon: icon,});
                $('#modalCreateInvoice').modal('hide');
                window.livewire.emit('onRefresh')
            });

            window.livewire.on('storePaymentAdvancement', (message) => {
                swal(message, {icon: "success",});
                $('#modalAdvancement').modal('hide');
                window.livewire.emit('onRefresh')
                window.livewire.emit('onRefreshCP')
            });

            window.livewire.on('paymentUpdateStore', (message, id) => {
                $('#ModalPagar'+id).modal('hide');
                $('#ModalPagarEfectivo'+id).modal('hide');
            });

            
            @can('collectors.edit')
            function DesactivarPlan(id, name){
                swal({
                    title: "¿Estas seguro?",
                    text: "Se cancelará el plan "+name+". Esta accion es irreversible",
                    icon: "warning",
                    buttons: {
                        cancel: "Cancelar",
                        catch: {
                            text: "Si, Desactivar",
                            closeModal: false,
                            className: 'swal-button--danger'
                        },
                    },
                  })
                .then((willDelete) => {
                    if (willDelete) {
                        window.livewire.emit('onDeleteRow',id)
                        window.livewire.on('productDelete', () => {
                            swal('Plan Cancelado', {icon: "success",});
                        });
                    }
                });
            }
            @endcan
        </script>
    </x-slot>
</x-app-layout>