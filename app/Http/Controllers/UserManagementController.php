<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{

    public function index()
    {
        $users = User::orderBy('id','desc')->get();

        return view('users.index', compact('users'));
    }


    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }


    public function create()
    {
        return view('users.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'username'=>'required',
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required|min:6',
            'role'=>'required'
        ]);

        User::create([
            'username'=>$request->username,
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role'=>$request->role
        ]);

        return redirect('/users')
            ->with('success','User berhasil dibuat');
    }


    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }


    public function update(Request $request, User $user)
    {
        $user->update([
            'username'=>$request->username,
            'name'=>$request->name,
            'email'=>$request->email,
            'role'=>$request->role
        ]);

        if($request->password){
            $user->update([
                'password'=>Hash::make($request->password)
            ]);
        }

        return redirect('/users')
            ->with('success','User berhasil diperbarui');
    }


    public function updatePassword(Request $request, User $user)
    {
        $user->update([
            'password'=>Hash::make($request->password)
        ]);

        return back()
            ->with('success','Password berhasil diganti');
    }


    public function destroy(User $user)
    {
        $user->delete();

        return redirect('/users')
            ->with('success','User berhasil dihapus');
    }

}
