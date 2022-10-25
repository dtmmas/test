<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserComponent extends Component
{
    use WithPagination;

    protected $paginationTheme= 'bootstrap';

    //variables de nuestro modelo
    public $name;
    public $lastname;
    public $dni;
    public $email;
    public $password;
    public $clave;
    public $user;
    public $rolesAll;
    public $rolesUser = [];

    //variables para fucnionalidades
    public $selected_id, $search;
    public $createMode = true; //creando
    private $pagination = 15;
    public $editRoleMode = false;

    public function render()
    {
        if($this->search!=''){
            $list_users = User::with('roles')
            ->where('id','!=','1') 
            ->where(function($query){
                $query->orwhere('name','LIKE','%'.$this->search.'%')
                ->orWhere('dni','LIKE','%'.$this->search.'%');
                $query->orWhereRaw(' CONCAT(users.name," ",users.lastname) LIKE ?',['%'.$this->search.'%']);
            })
            ->paginate($this->pagination);
        }else{
            $list_users = User::with('roles')
            ->where('id','!=','1') 
            ->paginate($this->pagination);
        }
        return view('livewire.user-component', compact('list_users'));
    }
    
    public function updatingSearch()
    {
        //otra opcion seria $this->resetPage();
        $this->gotoPage(1);
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->lastname = '';
        $this->email = '';
        $this->dni = '';
        $this->clave = '';
        $this->password = '';
        $this->selected_id = null;
        $this->createMode = true;
        $this->rolesUser = [];
    }

    public function store()
    {
        abort_if(!Auth::user()->can('users.create'), 401);
        //validar informacion
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'dni' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:5', 'max:255']
        ]);
        
        User::create([
            'name' => $this->name,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'dni' => $this->dni,
            'password' => Hash::make($this->password)
        ]);

        $this->resetInputFields();
        $this->emit('userUpdateStore','Usuario creado correctamente.'); // Close model to using to jquery
    }

    public function edit($id)
    {
        abort_if(!Auth::user()->can('users.edit'), 401);
        $user = User::findOrFail($id,['id','name','lastname','email','dni']);
        $this->name = $user->name;
        $this->lastname = $user->lastname;
        $this->email = $user->email;
        $this->dni = $user->dni;
        
        $this->selected_id = $user->id;
        $this->createMode = false;
        $this->emit('userEdit');
    }

    
    public function update()
    {   
        abort_if(!Auth::user()->can('users.edit'), 401);
        $validatedDate = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->selected_id)],
            'dni' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($this->selected_id)],
        ]);
      
        if ($this->selected_id) {
            $user = User::findOrFail($this->selected_id);

            $user->update([
                'name' => $this->name,
                'lastname' => $this->lastname,
                'email' => $this->email,
                'dni' => $this->dni
            ]);

            if($this->clave!=''){
                $this->validate(['clave' => ['required', 'string', 'min:5', 'max:255']]);
                $user->update([
                    'password' => Hash::make($this->clave)
                ]);
            }

            $this->createMode = false;
            $this->emit('userUpdateStore','Usuario actualizado correctamente.');
            $this->resetInputFields();
        }
    }

    public function destroy($id)
    {
        abort_if(!Auth::user()->can('users.destroy'), 401);
        $user = User::findOrFail($id);
        $user->delete();
        $this->resetInputFields();
        $this->emit('userDelete');
    }

    public function editRole($id)
    {   
        abort_if(!Auth::user()->can('users.editrole'), 401);
        $this->editRoleMode = true;
        $this->selected_id = $id;
        $user = User::findOrFail($id);
        $this->user = $user;

        $selectPermissions = $user->roles()->pluck('id')->toArray();
  
        foreach($selectPermissions as $selectPermission) {
            $this->rolesUser[$selectPermission] = $selectPermission;
        }

        $this->rolesAll = Role::all();
        $this->emit('roleUserEdit', 'Roles usuario: '.$user->name_complete);
    }

    public function updateRole()
    {
        abort_if(!Auth::user()->can('users.editrole'), 401);
        $user = User::findOrFail($this->selected_id);
        $this->rolesUser = array_filter($this->rolesUser);
        $user->roles()->sync($this->rolesUser);
        $this->resetInputFields();
        $this->emit('roleUserUpdate','Roles asignados correctamente');
    }

    //listeners / escuchar eventos js o php y ejecutar acciones livewire
    protected $listeners = [
        'onDeleteRow' =>'destroy'
    ];

}
