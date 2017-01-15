<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Entities\User;
use App\Entities\MovieList;
use Auth;
use Socialite;

class SocialController extends Controller
{

	public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }


    //Redirige a la red social
    public function redirectToProvider($provider=null)
    {
        if(!config('services.' . $provider)) abort('404');
        return Socialite::driver($provider)->redirect();
    }


    //Maneja la respuesta desde la red social
    public function handleProviderCallback($provider=null)
    {
        try {
            if ($provider == 'facebook') {
                $user = Socialite::driver($provider)->fields(['first_name', 'last_name', 'email', 'name'])->user();
            } else {
                $user = Socialite::driver($provider)->user();
            }
        } catch (Exception $e) {
            return redirect('login');
        }
 
        $authUser = $this->findOrCreateUser($user, $provider);
 
        Auth::login($authUser, true);
 
        return redirect()->route('home');
    }


    //Si existe el usuario lo retorna, si no lo crea
    private function findOrCreateUser($user, $provider)
    {
        $authUser = User::where($provider . '_id', $user->id)->first();
 
        if ($authUser){
            return $authUser;
        }

        $newUser = New User;
        $newUser->name = $user->name;
        $newUser->email = $user->email;
        if ($provider == 'facebook') {
            $newUser->facebook_id = $user->id;
            //si el nombre es mas largo de 12 lo cortamos
            $nick = strlen($user->user['first_name']) > 10 ? substr($user->user['first_name'],0,10)."..." : $user->user['first_name'];
            $newUser->nick = $nick;
        }
        if ($provider == 'google') {
            $newUser->google_id = $user->id;
            //si el nombre es mas largo de 12 lo cortamos
            $nick = strlen($user->user['name']['givenName']) > 10 ? substr($user->user['name']['givenName'],0,10).".." : $user->user['name']['givenName'];
            $newUser->nick = $nick;
        }
        $newUser->avatar = $user->avatar;
        $newUser->admin = 0;
        $newUser->save();

        $newUserLists2 = New MovieList;
        $newUserLists2->name = 'Vistas';
        $newUserLists2->user_id = $newUser->id;
        $newUserLists2->save();

        $newUserLists3 = New MovieList;
        $newUserLists3->name = 'Para ver';
        $newUserLists3->user_id = $newUser->id;
        $newUserLists3->save();

        $newUserLists = New MovieList;
        $newUserLists->name = 'Mi top 100';
        $newUserLists->ordered = 1;
        $newUserLists->user_id = $newUser->id;
        $newUserLists->save();

        return $newUser;
    }


    public function login()
    {
        /*Auth::loginUsingId(4);*/
/*        $newUserLists2 = New MovieList;
        $newUserLists2->name = 'Vistas';
        $newUserLists2->user_id = 1;
        $newUserLists2->save();

        $newUserLists3 = New MovieList;
        $newUserLists3->name = 'Pendientes de ver';
        $newUserLists3->user_id = 1;
        $newUserLists3->save();

        $newUserLists = New MovieList;
        $newUserLists->name = 'Mi top 100';
        $newUserLists->ordered = 1;
        $newUserLists->user_id = 1;
        $newUserLists->save();*/
        return view('pages.login');
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }

}
