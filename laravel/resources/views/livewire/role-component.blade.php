<div>   
    @if($verTabla)
    <div class="row mb-3">
        <div class="input-group col">
            <input wire:model="search" type="text" class="form-control mayusculas" placeholder="Buscar por nombre">
            <span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
        </div>
        <div class="col">
            @can('roles.create')
            <button type="button" wire:click="create" class="btn btn-primary btnCrearModal float-end" >
                Crear Rol
            </button>
            @endcan
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descriccion</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($list_roles as $role)
                <tr>
                    <td>{{$role->name}}</td>
                    <td>{{$role->description}}</td>
                    <td style="width: 10px">
                        <div class="btn-group " role="group">
                            @can('roles.edit')
                                <a href="#!" wire:loading.attr="disabled" wire:click="edit({{$role->id}})"  type="button" class="btn btn-sm btn-info btn-secondary" >
                                    Editar
                                </a>
                            @else
                                <a href="#!" type="button" class="btn btn-sm btn-info btn-secondary">
                                    Acciones
                                </a>
                            @endcan
                            <button style="opacity: .8" type="button" class="btn btn-info btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuReference">
                                @can('roles.editrole')
                                <a class="dropdown-item" href="#!">Role</a>
                                @endcan
                                @can('roles.destroy')
                                <a onclick="EliminarRole({{$role->id}},'{{$role->name}}')" class="dropdown-item" href="#!">Eliminar</a>
                                @endcan
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No hay resultados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div>
        {{ $list_roles->links() }}
    </div>
    @else
    
    @if($createMode)
    {!! Form::open(['id' => 'FormUser', 'method' => 'POST']) !!}
    @else
    {!! Form::model($role,['id' => 'FormUser', 'method' => 'PUT']) !!}
    @endif
    
        <div class="form-group">
            {!! Form::label('name', 'Nombre'); !!}
            {!! Form::text('name', null, ['wire:model.defer'=> 'name', 'class' => 'form-control', 'placeholder'=>'Nombre', 'autocomplete'=>'off']); !!}
            @error('name') <span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            {!! Form::label('description', 'Descripcion'); !!}
            {!! Form::text('description', null, ['wire:model.defer'=> 'description', 'class' => 'form-control', 'placeholder'=>'Apellido', 'autocomplete'=>'off']); !!}
            @error('description') <span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <h2>Lista de permisos</h2>
        @forelse ($permissionsAll  as $index => $permission)
            <li class="list-group-item ">
                <div class="custom-control custom-checkbox ">
                    {!! Form::checkbox('permissions[]', $permission->id, null, ['wire:model.defer'=>'permissionsRole.'.$permission->id,'class'=>'custom-control-input','id'=>'permission'.$permission->id]) !!}
                    {!! Form::label('permission'.$permission->id, $permission->description, ['class'=>'custom-control-label ']) !!}
                </div>
            </li>
        @empty
            
        @endforelse
        <button type="button" wire:click="resetInputFields" class="btn btn-danger mt-2" >Cancelar</button>
        @if ($createMode)
        <button type="button" wire:loading.attr="disabled" wire:click="store()" class="btn btn-primary mt-2">Crear Rol</button>
        @else
        <button type="button" wire:loading.attr="disabled" wire:click="update()" class="btn btn-primary mt-2">Editar Rol</button>
        @endif
    
    {!! Form::close() !!}
    @endif

</div>
