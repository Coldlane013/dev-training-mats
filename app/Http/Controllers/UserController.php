<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Service\UserService;


class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Paginate results, showing parent::$show_per_page items per page
        $users = User::paginate(parent::$show_per_page)->withQueryString();
        $loggedInStats = $this->userService->getLoggedInStats();

        if($users){
            return response()->json([
                'message' => 'User lists successfully fetched.',
                'user' => $users,
                'data' => $users,
                'logged_in_stats' => $loggedInStats
            ]);
        }else{
            return response()->json([
                'message' => 'No users found.',
            ], 403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required',
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'user_roles' => $request->user_roles ?? 'guest',
        ]);

        return response()->json([
            'message' => 'User Created Successful',
            'user' => $user
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if($user){
            return response()->json([
                'message' => 'User Fetched Successful',
                'user' => $user
            ]);
        }else{
            return response()->json([
                'message' => 'User not found',
            ], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required',
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required'
        ]);

        $user = User::find($id)
                    ->update([
                        'name' => $request->name,
                        'password' => Hash::make($request->password),
                        'email' => $request->email,
                        'first_name' => $request->first_name,
                        'middle_name' => $request->middle_name,
                        'last_name' => $request->last_name,
                        'user_roles' => $request->user_roles ?? 'guest',
                    ]);
        if($user){
            return response()->json([
                'message' => 'User Successful Updated',
                'user' => $user
            ]);
        }else{
            return response()->json([
                'message' => 'User not found',
            ], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find(1);

        if ($user) {
            // Permanently deletes the record from the database
            $user->delete();
            return response()->json([
                'message' => 'User Successful Deleted',
                'user' => $user
            ]);
        }
    }
}
