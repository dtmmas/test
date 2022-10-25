<x-app-layout>
    <x-slot name="titulo_pagina">
        {{ __('Listado de Facturas') }}
    </x-slot>
    <x-slot name="opciones_nav">
        <ol class="breadcrumb ms-auto">
        </ol>
    </x-slot>
    @livewire('invoice-component', ['pivot'=>null,'client'=>null])

    <x-slot name="script">
        <script type="text/javascript">
            window.livewire.on('paymentUpdateStore', (message, id) => {
                swal(message, {icon: "success",});
                $('#ModalPagar'+id).modal('hide');
                $('#ModalPagarEfectivo'+id).modal('hide');
            });
            
        </script>
    </x-slot>
</x-app-layout>