<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Gate;
use Hash;

use App\User;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        // $this->authorize('is_admin');
    }

    public function index()
    {
        // $this->authorize('is_admin');
        if (Gate::allows('is_admin') || Gate::allows('is_author')) {
            $users = User::latest()->paginate(2);
            return $users;
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:users|max:191',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
            'bio' => 'required|max:255|min:10',
            'type' => 'required',
        ]);
        $create_user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'type'      => $request->type,
            'bio'       => $request->bio,
            'password'  => Hash::make($request->password)
        ]);

        return $create_user;
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validatedData = $request->validate([
            'name' => 'required|max:191',
            'email' => 'required|unique:users,email,'.$user->id,
            'password' => 'sometimes|min:6',
            'bio' => 'required|max:255|min:10',
            'type' => 'required',
        ]);
        $user->update($request->all());

        return ["message" => "user updated"];
    }

    public function destroy($id)
    {
        $this->authorize('is_admin');

        $user = User::findOrFail($id);
        $user->delete();
        return ['message' => 'user deleted'];
    }

    public function profile(){
        return auth('api')->user(); //authenticated user
    }

    public function updateProfileInfo (Request $request) {
        $user = auth('api')->user();

        $validatedData = $request->validate([
            'name' => 'required|max:191',
            'email' => 'required|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|min:6',
            'bio' => 'required|max:255|min:10',
            'type' => 'required',
        ]);

        $current_photo = $user->photo;
        if($request->photo != $current_photo){
            $name = time().'.'.explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('img/profile/').$name);
            $request->merge(['photo' => $name]);

            $user_photo = public_path('img/profile/').$current_photo;
            if(file_exists($user_photo)) @unlink($user_photo);
        }

        if(!empty($request->password)) $request->merge(['password' => Hash::make($request->password)]);

        $user->update($request->all());
        return ['message' => '200'];
    }

    public function getUpdatedPhoto(Request $request) {
        $user = auth('api')->user();
        $current_photo = $user->photo;
        if($request->photo != $current_photo){
            $name = time().'.'.explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('img/profile/').$name);

            $user_photo = public_path('img/profile/').$current_photo;
            if(file_exists($user_photo)) @unlink($user_photo);
        }
        if(isset($name)) {
            $user->photo =$name;
            $user->save();
            return ['photo' => $name];
        }
        abort(500);
    }

    public function search() {
        if( $search = \Request::get('q') ){
            $users = User::where( function($query) use ($search){
                $query->where('name', 'LIKE', "%$search%")
                      ->orWhere('email', 'LIKE', "%$search%")
                      ->orWhere('type', 'LIKE', "%$search%");
            })->paginate(2);
        }else{
            $users = User::latest()->paginate(2);
        }

        return $users;
    }
}
