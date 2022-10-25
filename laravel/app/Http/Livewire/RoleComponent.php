<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class RoleComponent extends Component
{
    use WithPagination;

    protected $paginationTheme= 'bootstrap';

    //variables de nuestro modelo
    public $name;
    public $description;
    public $role;
    public $permissionsAll;
    public $permissionsRole=[];
    //variables para fucnionalidades
    public $selected_id, $search;
    public $createMode = true; //creando
    public $verTabla = true;
    private $pagination = 15;
    
    public function render()
    {
        if($this->search!=''){
            $list_roles = Role::where('id','!=','1') 
            ->where(function($query){
                $query->orwhere('name','LIKE','%'.$this->search.'%');
            })
            ->paginate($this->pagination);
        }else{
            $list_roles = Role::where('id','!=','1') 
            ->paginate($this->pagination);
        }
        return view('livewire.role-component', compact('list_roles'));
    }
    
    public function updatingSearch()
    {
        //otra opcion seria $this->resetPage();
        $this->gotoPage(1);
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->permissionsRole = [];
        $this->selected_id = null;
        $this->createMode = true;
        $this->verTabla = true;
        $this->emit('verTablaForm', 'Listado de Roles');
    }

    public function create()
    {
        abort_if(!Auth::user()->can('roles.create'), 401);
        $this->permissionsAll = Permission::all();
        $this->createMode = true;
        $this->verTabla = false;
        $this->emit('verTablaForm','Crear nuevo rol');
    }

    public function store()
    {
        abort_if(!Auth::user()->can('roles.create'), 401);
        //validar informacion
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
        ]);
        
        $role = Role::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);
        
        $this->permissionsRole = array_filter($this->permissionsRole);
        $role->permissions()->sync($this->permissionsRole);
        $this->emit('roleUpdateStore','Rol creado correctamente.');
        $this->resetInputFields();
    }

    public function edit($id)
    {
        abort_if(!Auth::user()->can('roles.edit'), 401);
        $this->permissionsAll = Permission::all();
        $this->role = $role = Role::with([
            'permissions' => function($query) {
                $query->select('id');
            }
        ])->findOrFail($id,['id','name','description']);
        $this->name = $role->name;
        $this->description = $role->description;
        $this->selected_id = $role->id;

        $selectPermissions = $role->getAllPermissions()->pluck('id')->toArray();
  
        foreach($selectPermissions as $selectPermission) {
            $this->permissionsRole[$selectPermission] = $selectPermission;
        }

        $this->createMode = false;
        $this->verTabla = false;
        $this->emit('verTablaForm', 'Editar Rol: '.$role->name);
    }

    
    public function update()
    {  
        abort_if(!Auth::user()->can('roles.edit'), 401);
        $validatedDate = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
        ]);
      
        if ($this->selected_id) {
            $role = Role::findOrFail($this->selected_id);

            $role->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            
            $this->permissionsRole = array_filter($this->permissionsRole);
            $role->permissions()->sync($this->permissionsRole);
            $this->createMode = false;
            $this->emit('roleUpdateStore','Rol actualizado correctamente.');
            $this->resetInputFields();
        }
    }

    public function destroy($id)
    {
        abort_if(!Auth::user()->can('roles.destroy'), 401);
        $role = Role::findOrFail($id);
        $role->delete();
        // $this->resetInputFields();
        $this->emit('roleDelete');
    }

    //listeners / escuchar eventos js o php y ejecutar acciones livewire
    protected $listeners = [
        'onDeleteRow' =>'destroy'
    ];

}
