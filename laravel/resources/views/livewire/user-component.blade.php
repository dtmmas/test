<div>   
    <div class="row">
        <div class=" mb-3 col-xs-12 col-sm-8">
            <div class="input-group col">
                <input wire:model="search" type="text" class="form-control mayusculas" placeholder="Buscar por nombre o dni">
                <span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
            </div>
        </div>
        <div class="mb-3 col-xs-12 col-sm-4">
            @can('users.create')
            <button type="button" class="btn btn-primary btnCrearModal float-end" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Crear Usuario
            </button>
            @endcan
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Dni</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($list_users as $user)
                <tr>
                    <td>{{$user->name}}</td>
                    <td>{{$user->lastname}}</td>
                    <td>{{$user->dni}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->name_roles}}</td>
                    <td style="width: 10px">
                        <div class="btn-group " role="group">
                            @can('users.edit')
                                <a href="#!" wire:loading.attr="disabled" wire:click="edit({{$user->id}})"  type="button" class="btn btn-sm btn-info btn-secondary" >
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
                                @can('users.editrole')
                                <a wire:click="editRole({{$user->id}})" class="dropdown-item" href="#!">Role</a>
                                @endcan
                                @can('users.destroy')
                                <a onclick="EliminarUser({{$user->id}},'{{$user->name_complete}}')" class="dropdown-item" href="#!">Eliminar</a>
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
        {{ $list_users->links() }}
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="exampleModal" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ (($createMode)?'Crear usuario':'Editar Usuario')}}</h5>
            </div>
            <div class="modal-body">
                
                @if($createMode)
                {!! Form::open(['id' => 'FormUser', 'method' => 'POST']) !!}
                @else
                {!! Form::model($user,['id' => 'FormUser', 'method' => 'PUT']) !!}
                @endif
                    @include('yield.input-user')
                {!! Form::close() !!}

            </div>
            <div class="modal-footer">
                <button type="button" wire:click="resetInputFields" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                @if ($createMode)
                <button type="button" wire:loading.attr="disabled" wire:click="store()" class="btn btn-primary">Crear Usuario</button>
                @else
                <button type="button" wire:loading.attr="disabled" wire:click="update()" class="btn btn-primary">Editar Usuario</button>
                @endif
            </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="roleModal" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalLabel">Actualizar rol usuario</h5>
            </div>
            <div class="modal-body">
                
                @if($editRoleMode)

                {!! Form::model($user,['id' => 'FormUser', 'method' => 'PUT']) !!}

                <h4>Listado de roles</h4>
                @forelse ($rolesAll  as $index => $rol)
                    <li class="list-group-item ">
                        <div class="custom-control custom-checkbox ">
                            {!! Form::checkbox('roles[]', $rol->id, null, ['wire:model.defer'=>'rolesUser.'.$rol->id,'class'=>'custom-control-input','id'=>'role'.$rol->id]) !!}
                            {!! Form::label('role'.$rol->id, $rol->name.': '.$rol->description, ['class'=>'custom-control-label ']) !!}
                        </div>
                    </li>
                @empty
                @endforelse
                {!! Form::close() !!}
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" wire:click="resetInputFields" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" wire:loading.attr="disabled" wire:click="updateRole()" class="btn btn-primary">Actualizar</button>
            </div>
            </div>
        </div>
    </div>
</div>
