<div>   
    <div class="row">
        <div class=" mb-3 col-xs-12 col-sm-8">
            <div class="input-group col">
                <input wire:model="search" type="text" class="form-control mayusculas" placeholder="Buscar por nombre o apellido o DPI">
                <span class="input-group-text"><i class="fa fa-search" wire:click="updatingSearch" aria-hidden="true"></i></span>
            </div>
        </div>
        
        <div class="mb-3 col-xs-12 col-sm-4">
            @can('clients.create')
            <button type="button" class="btn btn-primary btnCrearModal float-end" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Crear Cliente
            </button>
            @endcan
            @can('clients.importar')
            <a type="button" class="btn btn-success me-2  float-end" href="{{ route('clients.importar') }}">
                Importar Clientes
            </a>
            @endcan
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>DPI</th>
                    <th>Correo</th>
                    <th>Telefono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($list_clients as $client)

                    <?php if (!is_null($client)): ?>
                        <tr>
                        <td data-th="Nombre" >{{$client->user->name}}</td>
                        <td data-th="Apellido">{{$client->user->lastname}}</td>
                        <td data-th="DPI">{{$client->user->dni}}</td>
                        <td data-th="Correo">{{$client->user->email}}</td>
                        <td data-th="Telefono">{{$client->user->phone}}</td>
                        <td data-th="Acciones" >
                            <div class="btn-group " zone="group">
                                <a href="{{ route('clients.show', $client) }}"  type="button" class="btn btn-sm btn-info btn-secondary" >
                                    Ver
                                </a>
                                <button style="opacity: .8" type="button" class="btn btn-info btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuReference">
                                    @can('clients.index')
                                    <a href="{{ route('clients.products', $client) }}" class="dropdown-item">Planes</a>
                                    @endcan
                                    @can('clients.edit')
                                    <a wire:click="edit({{$client->id}})" class="dropdown-item" href="#!">Editar</a>
                                    @endcan
                                    @can('clients.destroy')
                                    <a onclick="EliminarClient({{$client->id}},'{{$client->user->name_complete}}')" class="dropdown-item" href="#!">Eliminar</a>
                                    @endcan
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endif ?>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay resultados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>
        {{ $list_clients->links() }}
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="exampleModal" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ (($createMode)?'Crear Cliente':'Editar Cliente')}}</h5>
            </div>
            <div class="modal-body">
                
                @if($createMode)
                {!! Form::open(['id' => 'FormClient', 'method' => 'POST']) !!}
                @else
                {!! Form::model($client,['id' => 'FormClient', 'method' => 'PUT']) !!}
                @endif
                    @include('yield.input-user')
                    <div class="form-group">
                        {!! Form::label('ip', 'IP '); !!}
                        {!! Form::text('ip', null, ['wire:model.defer'=> 'ip', 'class' => 'form-control', 'placeholder'=>'IP ', 'autocomplete'=>'off']); !!}
                        @error('ip') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        {!! Form::label('clave_wifi', 'Clave Wifi '); !!}
                        {!! Form::text('clave_wifi', null, ['wire:model.defer'=> 'clave_wifi', 'class' => 'form-control', 'placeholder'=>'Clave Wifi ', 'autocomplete'=>'off']); !!}
                        @error('clave_wifi') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        {!! Form::label('reference', 'Referencia *'); !!}
                        {!! Form::text('reference', null, ['wire:model.defer'=> 'reference', 'class' => 'form-control mayusculas', 'placeholder'=>'Referencia ', 'autocomplete'=>'off']); !!}
                        @error('reference') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                {!! Form::close() !!}

            </div>
            <div class="modal-footer">
                <button type="button" wire:click="resetInputFields" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                @if ($createMode)
                <button type="button" wire:loading.attr="disabled" wire:click="store()" class="btn btn-primary">Crear Cliente</button>
                @else
                <button type="button" wire:loading.attr="disabled" wire:click="update()" class="btn btn-primary">Editar Cliente</button>
                @endif
            </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="zoneModal" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="zoneModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="zoneModalLabel">Actualizar rol usuario</h5>
            </div>
            <div class="modal-body">
                
                @if($editZoneMode)

                {!! Form::model($client,['id' => 'FormClient', 'method' => 'PUT']) !!}

                <h4>Listado de zones</h4>
                @forelse ($zonesAll  as $index => $rol)
                    <li class="list-group-item ">
                        <div class="custom-control custom-checkbox ">
                            {!! Form::checkbox('zones[]', $rol->id, null, ['wire:model.defer'=>'zonesClient.'.$rol->id,'class'=>'custom-control-input','id'=>'zone'.$rol->id]) !!}
                            {!! Form::label('zone'.$rol->id, $rol->name.': '.$rol->description, ['class'=>'custom-control-label ']) !!}
                        </div>
                    </li>
                @empty
                @endforelse
                {!! Form::close() !!}
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" wire:click="resetInputFields" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" wire:loading.attr="disabled" wire:click="updateZone()" class="btn btn-primary">Actualizar</button>
            </div>
            </div>
        </div>
    </div>
</div>
