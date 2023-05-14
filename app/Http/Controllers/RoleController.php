<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class RoleController extends Controller
{
    public function index(){
        return Role::all();
    }
    public function show(int $id){
        return Role::find($id);
    }

    public function create(){
        return 1;
    }

    public function store(Request $request){

        try{

            $roleCheck = Role::where('name',$request->input('name'))->count();
            if($roleCheck==0){
                $validatedData = $request->validate([
                    'name' => 'required|string|max:20'
                ]);
                $role = new Role([
                    'name' => $validatedData['name'],
                ]);

                $role->save();
            }
            else{
                return response()->json(['error'=> true,'message' => 'vec ima u bazi sorry'], 500);

            }

        }catch(\Exception $exception){
            Log::error("Exception: " . $exception->getMessage());
            return response()->json(['error'=> true,'message' => $exception->getMessage()], 500);

        }
    }

    public function edit( $id){
        if(is_numeric($id)){
            $role = Role::find($id);
            if ($role != null){
                return $role;
            }else{
                return response()->json(['message'=>'Role not found'], 404);
            }
        }else{
            return response()->json(['message'=>'Bad request :)'], 400);

        }

    }
    public function update(Request $request, $id){
        if(is_numeric($id)){
            $role = Role::find($id);
            if ($role != null){
                $validatedData = $request->validate([
                    'name' => 'required|string|max:20'
                ]);
                $roleUpdated = [
                    'name' => $validatedData['name']
                ];
                $role->update($roleUpdated);

                return response()->json(['message'=>'Updated role :)']);

            }else{
                return response()->json(['message'=>'Role not found'], 404);
            }
        }else{
            return response()->json(['message'=>'Bad request :('], 400);

        }
    }
    public function destroy(int $id){
        try {
            $role = Role::find($id);
            if($role == null){
                return response()->json(['message'=>'Rolle not found'], 404);
            }else{

                $role->delete();
                return response()->json(['message'=>'Role deleted'], 200);
            }
        }catch (\Exception $exception){
            Log::error();
            return response()->json(['message'=>$exception->getMessage()]);
        }
    }
}
