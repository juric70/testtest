<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(){
        $users = User::with('role')->get();
        return response()->json($users, 200);
    }

    public function show(int $id){
        $user = User::with('role')->find($id);
        if($user != null){
            return response()->json(['data'=>$user], 200);
        }else{
            return response()->json(['message'=>'User not found'], 404);
        }
    }

    public function store(Request $request){
        try {
            $valoidateData = $request->validate([
                'username'=>'unique:users|required',
                'name' => 'required|string',
                'email' => 'required|email',
                'password'=>'required|min:8',
                'role_id' => 'required|exists:roles,id'
            ]);
            $valoidateData['password'] = Hash::make($valoidateData['password']);

            $user = new User([
                'username' => $valoidateData['username'],
                'name' => $valoidateData['name'],
                'email' => $valoidateData['email'],
                'password' => $valoidateData['password'],
                'role_id' => $valoidateData['role_id'],
            ]);
            $user->save();
            return response()->json(['message'=> 'User created successfully'], 200);

        }catch (\Exception $exception){
            return response()->json(['message'=>$exception->getMessage()]);
        }
    }

    public function register(Request $request){
        try {

            $valoidateData = $request->validate([
                'username'=>'unique:users|required',
                'name' => 'required|string',
                'email' => 'required|email',
                'password'=>'required|min:8'
            ]);

            $valoidateData['password'] = Hash::make($valoidateData['password']);

            $role = Role::where('name', 'user')->first();

            $user = new User([
                'username' => $valoidateData['username'],
                'name' => $valoidateData['name'],
                'email' => $valoidateData['email'],
                'password' => $valoidateData['password'],
                'role_id' => $role->id,
            ]);
            $user->save();

            return response()->json(['message'=> 'registered successfully'], 200);

        }catch (\Exception $exception){
            return response()->json(['message'=>$exception->getMessage()]);
        }
    }

    public function edit(int $id){
        $user = User::with('role')->find($id);
        $roles = Role::all();
        return response()->json(['user'=>$user, 'roles' => $roles]);
    }
    public function update(Request $request, int $id){
        try{
            $valoidateData = $request->validate([
                'username'=>['unique:users', Rule::unique('users')->ignore($id)],
                'name' => 'string',
                'email' => ['email', Rule::unique('users')->ignore($id)],

                'role_id' => 'exists:roles,id'
            ]);

            $updatedData = [
                'username' => $valoidateData['username'],
                'name' => $valoidateData['name'],
                'email' => $valoidateData['email'],
                'role_id' => $valoidateData['role_id'],
            ];
            $user = User::find($id);
            $user->update($updatedData);
        }catch (\Exception $exception){
            return response()->json(['message'=>$exception->getMessage()]);
        }
    }

    public function updateRole(Request $request, int $id){
        try{
            $valoidateData = $request->validate([
                'role_id' => 'exists:roles,id'
            ]);

            $updatedData = [
                'role_id' => $valoidateData['role_id'],
            ];
            $user = User::find($id);
            $user->update($updatedData);
        }catch (\Exception $exception){
            return response()->json(['message'=>$exception->getMessage()]);
        }
    }
    public function destroy(int $id){
        $user = User::find($id);
        $user->delete();
        return response()->json(['message'=> 'no more that data in base :)'],204);
    }

    //korisnik za prijavu unosi username(nameOrEmail) i lozinku
    public function login(Request $request){
        $validatedData = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if(!Auth::attempt($validatedData)){
            return response()->json(['message'=>'nevaazeći podatci za prijavu :)'], 401);
        }
        $user = $request->user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['token' => $token], 200);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfulyy']);
    }


}
