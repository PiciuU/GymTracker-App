<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Http\Requests\UserRequest;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    /**
     * Checks if the user has administrator privileges.
     *
     * @return bool
     */
    public function hasAccess()
    {
        return auth()->user()->hasAdminPrivileges();
    }

    /**
     * ADMIN SECTION
     * Methods accessible only to administrators.
     */

    /**
     * Display a listing of the user accounts.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function index()
    {
        if (!$this->hasAccess()) return $this->errorResponse('You do not have access to this resource!', 403);

        $users = new UserCollection(User::all());
        return $this->successResponse('User data list found', $users);
    }

    /**
     * Display the specified user account
     *
     * @param int $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function show($id)
    {
        if (!$this->hasAccess()) return $this->errorResponse('You do not have access to this resource!', 403);

        $user = new UserResource(User::find($id));
        return $this->successResponse('User data found', $user);
    }

    /**
     * Update the specified user account in storage.
     *
     * @param int $id
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function update($id, UserRequest $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse('You do not have access to this resource!', 403);

        $user = new UserResource(User::find($id));

        $validatedData = $request->validated();

        if (Arr::has($validatedData, 'password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        if(!$user->update($request->validated())) return $this->errorResponse('An error occurred while updating the user data, try again later', 500);

        return $this->successResponse('User data has been successfully updated', new UserResource($user));
    }


    /**
     * USER SECTION
     * Methods accessible to regular users.
     */

    /**
     * Store a newly created user resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function register(UserRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = new UserResource(User::create($validatedData));

        if (!$user) return $this->errorResponse('An error occurred while creating the user, try again later', 500);

        $authToken = $user->createToken('basic-token', ['basic']);

        return $this->successResponse('User has been created successfully', ['user' => $user, 'token' => $authToken->plainTextToken]);
    }

    /**
     * Log in the user.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function login(UserRequest $request) {
        if (Auth::attempt($request->validated())) {
            $user = new UserResource(Auth::user());

            if ($user->isAdmin()) $authToken = $user->createToken('admin-token', ['admin']);
            else  $authToken = $user->createToken('basic-token', ['basic']);

            return $this->successResponse('Login successful', ['user' => $user, 'token' => $authToken->plainTextToken]);
        }

        return $this->errorResponse('Invalid login credentials', 401);
    }

    /**
     * Log out the user.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function logout() {
        auth()->user()->currentAccessToken()->delete();
        return $this->successResponse('Logout successful');
    }

    /**
     * Get the data of the authenticated user.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function userData()
    {
        $user = new UserResource(auth()->user());
        return $this->successResponse('User data found', $user);
    }

    /**
     * Update the name of the authenticated user.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function updateName(UserRequest $request)
    {
        $user = auth()->user();

        if(!$user->update($request->validated())) return $this->errorResponse('An error occurred while updating the user data, try again later', 500);

        return $this->successResponse('User data has been successfully updated', new UserResource($user));
    }

    /**
     * Update the email of the authenticated user.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function updateMail(UserRequest $request)
    {
       // Implement a similar function to updateName, but also send an email to the old email address notifying about the change.
    }

    /**
     * Update the password of the authenticated user.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function updatePassword(UserRequest $request)
    {
        $user = auth()->user();

        if (!Hash::check($request->validated()['password_current'], $user->password)) {
            return $this->errorResponse('Invalid current password', 400);
        }

        if ($request->validated()['password'] === $request->validated()['password_current']) {
            return $this->errorResponse('New password must be different from the current password', 400);
        }

        if(!$user->update([
            'password' => Hash::make($request->validated()['password'])
        ])) return $this->errorResponse('An error occurred while updating the password, try again later', 500);


        // Implement sending an email about the password change

        return $this->successResponse('Password updated successfully');
    }
}
