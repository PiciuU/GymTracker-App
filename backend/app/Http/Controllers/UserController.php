<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use App\Models\PasswordResetToken;

use Carbon\Carbon;

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

        return $this->errorResponse('Invalid username or password.', 401);
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
     * Recover password for the user by generating a reset password hash
     *
     * This method generates a reset password hash for the user, which allows them to recover their password.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function recover(UserRequest $request) {
        $user = User::where('email', $request->validated()['email'])->first();

        if (!$user) return $this->successResponse('If an account is associated with the provided email address, we have sent a message to it.');

        $resetPassword = PasswordResetToken::where('user_id', $user->id)->first();
        if ($resetPassword) $resetPassword->delete();

        $resetPasswordHash = md5(rand().time());

        PasswordResetToken::create([
            'user_id' => $user->id,
            'hash' => $resetPasswordHash,
            'valid_until' => Carbon::now()->addDay()->format('Y-m-d H:i:s')
        ]);

        // Mail::to($user->email)->send(new ResetPasswordRequestMail($resetPasswordHash));

        // if (Mail::failures()) return $this->errorResponse('An error occurred while sending email, try again later', 500);

        return $this->successResponse('If an account is associated with the provided email address, we have sent a message to it.');
    }

    /**
     * Verify password recovery.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function recoverToken($hash) {
        $resetPassword = PasswordResetToken::where('hash', $hash)
                        ->whereDate('valid_until', '>', now())
                        ->first();

        if (!$resetPassword) return $this->errorResponse('Invalid or expired password reset token.');

        return $this->successResponse('Valid password reset token.');
    }

    /**
     * Update user password with password recovery.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function resetPassword(UserRequest $request) {
        $validatedData = $request->validated();

        $resetPassword = PasswordResetToken::where('hash', $validatedData['hash'])
                        ->whereDate('valid_until', '>', now())
                        ->first();

        if (!$resetPassword) return $this->errorResponse('Invalid or expired password reset token.');

        $user = User::where('id', $resetPassword->user_id)->first();

        if (!$user) return $this->errorResponse('Invalid or expired password reset token.');

        $user->update([
            'password' => Hash::make($validatedData['password'])
        ]);

        $resetPassword->delete();

        // Mail::to($advertiser['email'])->send(new ResetPasswordConfirmationMail());

        return $this->successResponse('Your password has been successfully reset.');
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
