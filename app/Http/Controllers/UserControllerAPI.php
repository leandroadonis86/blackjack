<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;

use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\DB;

use App\User;
use App\StoreUserRequest;
use Hash;

class UserControllerAPI extends Controller
{
    public function getUsers(Request $request) {
        if ($request->has('page')) {
            return UserResource::collection(User::paginate(5));
        } else {
            return UserResource::collection(User::all());
        }
    }

    public function getUser($id) {
        return new UserResource(User::find($id));
    }

    public function getUserByEmail($email) {
        $result = DB::select('select * from users where email = ?', [$email]);
        return new UserResource(User::find($result[0]->id));
    }

    public function loginUser(Request $request) {
        $usingMail = false;
        if (strpos($request->email, '@')) {
            $usingMail = true;
        } else {
            $request->nickname = $request->email;
        }
        if($usingMail) {
            $result = DB::select('select * from users where email = ?', [$request->email]);
            if($result) {
                $result = DB::table('users')->
                    where('email', $request->email)->
                    where('password', $request->password)->count();
                if($result){
                    $result = DB::table('users')->
                        where('email', $request->email)->
                        where('password', $request->password)->get();
                    return response()->json($result);
                } else {
                    return response()->json(0);
                }
            } else {
                return response()->json(-1);
            }
        } else {
            $result = DB::select('select * from users where nickname = ?', [$request->nickname]);
            if($result) {
                $result = DB::table('users')->
                    where('nickname', $request->nickname)->
                    where('password', $request->password)->count();
                if($result){
                    $result = DB::table('users')->
                        where('nickname', $request->nickname)->
                        where('password', $request->password)->get();
                    return response()->json($result);
                } else {
                    return response()->json(0);
                }
            } else {
                return response()->json(-1);
            }           
        }
        return response()->json(0);
    }

    public function store(Request $request) {
        $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'min:6',
                'nickname' => 'required',
            ]);
        $user = new User();
        $user->fill($request->all());
        $user->password = Hash::make($user->password);
        $user->save();

        return response()->json(new UserResource($user), 201);
    }

    public function addUser(Request $request) {

        $result = DB::table('users')->where('email', $request->email)->count();
        if($result) 
            return response()->json(-1, 201);
        $result = DB::table('users')->where('nickname', $request->nickname)->count();
        if($result) 
            return response()->json(1, 201);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->nickname = $request->nickname;
        $user->save();

        /*
        DB::table('users')->insert(
            [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'nickname' => $request->nickname,
            'blocked' => 0,
            'total_points' => 0,
            'total_games_played' => 0
            ]
        );
        */

        return response()->json(0, 201);
    }

    public function update(Request $request, $id) {
        //update geral
        $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email,'.$id,
                'nickname' => 'required|min:3',
                'blocked' => 'integer|max:1',
                'reason_blocked' => 'nullable|string|max:255',
                'reason_reactivated' => 'nullable|string|max:255',
                'total_points' => 'integer|max:11',
                'total_games_played' => 'integer|max:11',
            ]); 
        $user = User::findOrFail($id);
        //$user->fill($request->all());
        $user['name'] = $request->name;
        $user['email'] = $request->email;
        $user['nickname'] = $request->nickname;
        $user['blocked'] = $request->blocked;
        $user['reason_blocked'] = $request->reason_blocked;
        $user['reason_reactivated'] = $request->reason_reactivated;
        $user['total_points'] = $request->total_points;
        $user['total_games_played'] = $request->total_games_played;
        $user->update();

        return new UserResource($user);
    }

    public function updateScore(Request $request, $id) {
        //update do score final de jogo 
        $user = User::findOrFail($id);
        $user['nickname'] = $request->nickname;
        $user['total_points'] = $request->total_points;
        $user['total_games_played'] = $request->total_games_played;
        $user->update();

        return new UserResource($user);
    }

    public function updateManagement(Request $request, $id) {
        //update feito pelo gestor
        $request->validate([
                'name' => 'required|string|min:6',
                //'password' => 'required|string|min:6',
                'email' => 'required|email|unique:users,email,'.$id,
                'nickname' => 'required|min:3',
            ]); 
        $user = User::findOrFail($id);
        $user['name'] = $request->name;
        $user['email'] = $request->email;
        $user['password'] = $request->password;
        $user['nickname'] = $request->nickname;
        $user->update();

        return new UserResource($user);
    }

    public function admRstPassManagement(Request $request, $id) {
        $user = User::findOrFail($id);
        $user['password'] = $request->password;
        $user->update();
        return new UserResource($user);
    }

    public function blockUser($id) {
        $user = User::findOrFail($id);
        $user['blocked'] = 1;
        $user->update();
        return new UserResource($user);
    }

    public function unblockUser($id) {
        $user = User::findOrFail($id);
        $user['blocked'] = 0;
        $user->update();
        return new UserResource($user);
    }

    public function delete($id) {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }
    
    public function emailAvailable(Request $request) {
        $totalEmail = 1;
        if ($request->has('email') && $request->has('id')) {
            $totalEmail = DB::table('users')->where('email', '=', $request->email)->where('id', '<>', $request->id)->count();
        } else if ($request->has('email')) {
            $totalEmail = DB::table('users')->where('email', '=', $request->email)->count();
        }
        return response()->json($totalEmail == 0);
    }

    public function nicknameAvailable(Request $request) {
        $totalNickname = 1;
        if ($request->has('nickname') && $request->has('id')) {
            $totalNickname = DB::table('users')->where('nickname', '=', $request->nickname)->where('id', '<>', $request->id)->count();
        } else if ($request->has('nickname')) {
            $totalNickname = DB::table('users')->where('nickname', '=', $request->nickname)->count();
        }
        return response()->json($totalNickname == 0);
    }
}
