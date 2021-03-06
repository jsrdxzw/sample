<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
class UsersController extends Controller
{
    public function create(){
        return view('users.create');
    }

    public function show($id){
        $user = User::findOrFail($id);
        return view('users.show',compact('user'));
    }

    public function store(Request $request){
        $this->validate($request, [
        'name' => 'required|max:50',
        'email' => 'required|email|unique:users|max:255',
        'password' => 'required'
    ]);
      $user = User::create(['name'=>$request->name,'email'=>$request->email,'password'=>$request->password]);
      Auth::login($user);
      session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
      return redirect()->route('users.show', [$user]);
    }
    public function edit($id){
        $user = User::findOrFail($id);
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }
    public function update($id,Request $request){
        $this->validate($request,[
            'name'=>'required|max:50',
            'password'=>'required|confirmed|min:6'
        ]);
        $user = User::findOrFail($id);
        $this->authorize('update',$user);
        $user->update([
            'name'=>$request->name,
            'password'=>$request->password
        ]);

        $data = array_filter([
            'name' => $request->name,
            'password' => $request->password,
        ]);
        $user->update($data);
        session()->flash('success', '个人资料更新成功！');
        return redirect()->route('users.show',$id);
    }
    public function __construct(){
        $this->middleware('auth',[
            'only' => ['edit', 'update']
        ]);
    }

    public function index()
    {
        $users = User::paginate(5);
        return view('users.index', compact('users'));
    }
}
