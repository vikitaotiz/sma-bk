<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAuth;
use App\Models\Role;
use App\Models\Department;
use App\Http\Resources\Users\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index() {

        return UserResource::collection(User::orderBy('created_at', 'desc')->get());

        // $user = auth()->user();
        // if(in_array("Administrator", $user->roles->pluck("name")->toArray())){
        // return UserResource::collection(User::orderBy('created_at', 'desc')->get());
        // } else {
        //     return UserResource::collection(
        //         User::orderBy('created_at', 'desc')
        //         ->where("id", $user->id)
        //         ->orWhere("creator_id", $user->id)
        //         ->get()
        //     );
        // }
    }

    public function store(Request $request) {

        try {
            /***********************************************************/
            DB::beginTransaction(); // Begining of a laravel transaction
            /***********************************************************/

            $userExist = User::where('name', $request->name)
                    ->where('email', $request->email)
                    ->first();

            if($userExist) return response([
                'status' => 'error',
                'message' => 'User already exists, try a different one.'
            ]);

            $roles = array();

            $fields = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string'
            ]);

            foreach($request->role_uuids as $role_uuid){
                $role_id = Role::where('uuid', $role_uuid)->first()->id;
                array_push($roles, $role_id);
            };

            $department = Department::where('uuid', $request->department_uuid)->first();

            $user = User::create([
                'uuid' => Str::uuid()->toString(),
                'name' => $fields['name'],
                'active' => $request->active,
                'email' => $fields['email'],
                'phone' => $request->phone,
                'creator_id' => auth()->user()->id,
                'password' => bcrypt($fields['password'])
            ]);

            if($user){

                UserAuth::create([
                    'uuid' => Str::uuid()->toString(),
                    'user_id' => $user->id,
                    'authenticated' => false
                ]);

                $user->roles()->attach($roles);
                $user->departments()->attach([$department->id]);
            } else {
                return response([
                    'status' => 'error',
                    'message' => 'User creation failed.'
                ]);
            };

            /******************************************/
            DB::commit(); // End of database transactions (Success)
            /******************************************/

            return response([
                'status' => 'success',
                'message' => 'User created successfully.'
            ]);


        } catch(\Exception $exp) {

            /*****************************************/
            DB::rollBack(); // Rollback
            /*****************************************/

            return response([
                'message' => $exp->getMessage(),
                'status' => 'error'
            ], 400);
        }
    }

    public function update(Request $request)
    {
        try {
            /***********************************************************/
            DB::beginTransaction(); // Begining of a laravel transaction
            /***********************************************************/

            $user = User::where("uuid", $request->user_uuid)->first();
            $department = Department::where('uuid', $request->department_uuid)->first();
            $roles = array();

            if($user){
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'active' => $request->active,
                    'email_notify' => $request->email_notify,
                    'whatsapp_notify' => $request->whatsapp_notify,
                    'phone' => $request->phone,
                    'creator_id' => auth()->user()->id
                ]);

                if($request->password) $user->update(['password' => bcrypt($request->password)]);

                foreach($request->role_uuids as $role_uuid){
                    $role_id = Role::where('uuid', $role_uuid)->first()->id;
                    array_push($roles, $role_id);
                };

                $user->roles()->sync($roles);
                $user->departments()->sync([$department->id]);

                /******************************************/
                DB::commit(); // End of database transactions (Success)
                /******************************************/

                return response()->json([
                    "status" => "success",
                    "message" => "User updated successfully.",
                ]);

            } else {
                return response()->json([
                    "status" => "error",
                    "message" => "User not found."
                ]);
            }

        } catch(\Exception $exp) {

            /*****************************************/
            DB::rollBack(); // Rollback
            /*****************************************/

            return response([
                'message' => $exp->getMessage(),
                'status' => 'error'
            ], 400);
        }
    }

    public function show($uuid) {
        return new UserResource(User::where('uuid', $uuid)->first());
    }

    private function userLog($day, $status) {
        return UserAuth::whereDate('created_at', Carbon::now()->subDays($day)->toDateString())
                    ->where('authenticated', $status)
                    ->pluck('created_at');
    }

    private function loggedUsers($day) {
        $arr = array();

        $auth_user = UserAuth::groupBy('user_id')
                ->selectRaw('user_id')
                ->whereDate('created_at', Carbon::now()->subDays($day)->toDateString())
                ->get()->toArray();

        foreach ($auth_user as $user) {
            array_push($arr, [
                'user' => User::findOrFail($user['user_id'])->name ?? "No User",
                // 'login_data' => $this->userLog($day, 1),
                'login_count' => $this->userLog($day, 1)->count(),
                // 'logout_data' => $this->userLog($day, 0),
                'logout_count' => $this->userLog($day, 0)->count(),

                'last_logged_in' => $this->userLog($day, 1)->last(),
                'last_logged_out' => $this->userLog($day, 0)->last(),
                'log' => Carbon::parse($this->userLog($day, 0)->last())->diffInSeconds(Carbon::parse($this->userLog($day, 1)->last()))
            ]);
        }

        return $arr;
    }

    public function logged_in_users() {
        $user_array = array();

        for ($i=0; $i < 7; $i++) {
            array_push($user_array, [
                "day" => Carbon::now()->subDays($i)->toDateString(),
                "data" => $this->loggedUsers($i)
            ]);
        }

        return $user_array;
    }
}
