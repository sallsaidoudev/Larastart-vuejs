<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\UserRequest;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function __construct()
    {
    $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
       // $this->authorize('isAdmin');
        if (Gate::allows('isAdmin')|| Gate::allows('isUser')){
            return User::latest()->paginate(10);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
       return User::Create([
          'name'=>$request['name'],
          'email'=>$request['email'],
          'type'=>$request['type'],
          'bio'=>$request['bio'],
          'photo'=>$request['photo'],
          'password'=>Hash::make($request['password']),
       ]);
    }

    public function profile()
    {
        return auth('api')->user();
    }

    public function updateProfile(UserRequest $request){
        $user=auth('api')->user();
        $currentPhoto=$user->photo;
        if($request->photo !=$currentPhoto) {
            $name = time() . '.' . explode('/', explode(':', substr($request->photo, 0,
                    strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('img/profile/').$name);

            $request->merge(['photo' => $name]);
            //je recupère la photo courant de l'utilisateur , si elle existe on remplace la current par la nouvelle photo
            $userPhoto = public_path('img/profile/').$currentPhoto;
            if(file_exists($userPhoto)){
                @unlink($userPhoto);
            }
        }

        if(!empty($request->password)){
            $request->merge(['password' => Hash::make($request['password'])]);
        }

        $user->update($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user=User::findOrFail($id);
        $user->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');

        $user=User::findOrFail($id);
        $user->delete();

       return ['message'=>'user deleted'];
    }
}
