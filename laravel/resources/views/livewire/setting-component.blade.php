<div>
    <div class="card">
        <div class="card-body wire:ignore.self">
            <ul class="nav nav-pills mb-3">
                <li class="nav-item"><a href="#navpills-1" class="nav-link {{(($tab=='empresa')?'active show':'')}}" data-toggle="tab" aria-expanded="false">Informacion Empresa</a>
                </li>
                </li>
            </ul>
            <div class="tab-content br-n pn">
                <div id="navpills-1" class="tab-pane {{(($tab=='empresa')?'active show':'')}}">
                    {!! Form::open(['id' => 'FormEmpresa', 'method' => 'POST', 'files' => true]) !!}
                        <div class="form-row">
                           
                            <div class="form-group col-md-12">
                                {!! Form::label('nombre_empresa', 'Nombre de la empresa'); !!}
                                {!! Form::text('nombre_empresa', null, ['wire:model.defer'=> 'nombre_empresa',  'class' => 'form-control']); !!}
                                @error('nombre_empresa') <span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group col-md-12">
                                {!! Form::label('direccion_empresa', 'Direccion'); !!}
                                {!! Form::text('direccion_empresa', null, ['wire:model.defer'=> 'direccion_empresa',  'class' => 'form-control', 'placeholder'=>'']); !!}
                                @error('direccion_empresa') <span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group col-md-12">
                                {!! Form::label('telefono_empresa', 'Telefono'); !!}
                                {!! Form::text('telefono_empresa', null, ['wire:model.defer'=> 'telefono_empresa', 'class' => 'form-control ', 'placeholder'=>'']); !!}
                                @error('telefono_empresa') <span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group col-md-12">
                                {!! Form::label('nit_empresa', 'NIT'); !!}
                                {!! Form::text('nit_empresa', null, ['wire:model.defer'=> 'nit_empresa',  'class' => 'form-control numerico', 'placeholder'=>'']); !!}
                                @error('nit_empresa') <span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group col-md-12">
                                {!! Form::label('slogan_empresa', 'Slogan'); !!}
                                {!! Form::text('slogan_empresa', null, ['wire:model.defer'=> 'slogan_empresa',  'class' => 'form-control', 'placeholder'=>'']); !!}
                                @error('slogan_empresa') <span class="text-danger">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group col-md-12">
                                {!! Form::label('logo_empresa', 'Cargar Logo'); !!}
                                {!! Form::file('logo_empresa',  ['wire:model'=> 'logo_empresa', 'class' => 'form-control', 'placeholder'=>'Cargar Logo', 'accept'=>'image/*']); !!}
                                @error('logo_empresa') <span class="text-danger">{{ $message }}</span>@enderror
                                <span wire:loading wire:target="logo_empresa" class="text-danger" > Cargando...</span>
                            </div>
                            
                            <div class="form-group col-md-12 text-center">
                                <button type="button" wire:loading.attr="disabled"  wire:target="logo_empresa,guardarEmpresa"  wire:click="guardarEmpresa()" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
