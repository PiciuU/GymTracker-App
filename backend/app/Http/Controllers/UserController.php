<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetToken;

use App\Mail\ResetPasswordRequestMail;
use App\Mail\ResetPasswordConfirmationMail;
use App\Mail\ResetEmailConfirmationMail;

use Carbon\Carbon;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Http\Requests\UserRequest;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
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
        if (!auth()->user()->hasAdminPrivileges()) return $this->errorResponse("You do not have access to this resource!", 403);

        $users = new UserCollection(User::all());
        return $this->successResponse("Users has been successfully found.", $users);
    }

    /**
     * Display the specified user account
     *
     * @param int $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function show($id)
    {
        if (!auth()->user()->hasAdminPrivileges()) return $this->errorResponse("You do not have access to this resource!", 403);

        $user = User::find($id);

        if (!$user) return $this->errorResponse("User not found.", 404);

        return $this->successResponse("User has been successfully found.", new UserResource($user));
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

        if (!$user) return $this->errorResponse("An error occurred while creating the user, try again later.", 500);

        $authToken = $user->createToken('basic-token', ['basic']);

        return $this->successResponse("User has been successfully created.", ['user' => $user, 'token' => $authToken->plainTextToken]);
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
            else $authToken = $user->createToken('basic-token', ['basic']);

            return $this->successResponse('Login successful.', ['user' => $user, 'token' => $authToken->plainTextToken]);
        }

        return $this->errorResponse("Invalid username or password.", 401);
    }

    /**
     * Log out the user.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function logout() {
        auth()->user()->currentAccessToken()->delete();
        return $this->successResponse("Logout successful.");
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

        if (!$user) return $this->successResponse("If an account is associated with the provided email address, we have sent a message to it.");

        $resetPassword = PasswordResetToken::where('user_id', $user->id)->first();
        if ($resetPassword) $resetPassword->delete();

        $resetPasswordHash = md5(rand().time());

        PasswordResetToken::create([
            'user_id' => $user->id,
            'hash' => $resetPasswordHash,
            'valid_until' => Carbon::now()->addDay()->format('Y-m-d H:i:s')
        ]);

        $mailSent = Mail::to($user->email)->send(new ResetPasswordRequestMail($resetPasswordHash));

        if (!$mailSent) return $this->errorResponse("An error occurred while sending email, try again later.", 500);

        return $this->successResponse("If an account is associated with the provided email address, we have sent a message to it.");
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

        if (!$resetPassword) return $this->errorResponse("Invalid or expired password reset token.");

        return $this->successResponse("Recover token has been successfully validated.");
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

        if (!$resetPassword) return $this->errorResponse("Invalid or expired password reset token.");

        $user = User::where('id', $resetPassword->user_id)->first();

        if (!$user) return $this->errorResponse("Invalid or expired password reset token.");

        $user->update([
            'password' => Hash::make($validatedData['password'])
        ]);

        $resetPassword->delete();

        Mail::to($user->email)->send(new ResetPasswordConfirmationMail());

        return $this->successResponse("Password has been successfully reset.");
    }

    /**
     * Get the data of the authenticated user.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function userData()
    {
        $user = new UserResource(auth()->user());
        return $this->successResponse("User has been successfully found.", $user);
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

        if(!$user->update($request->validated())) return $this->errorResponse("An error occurred while updating the user, try again later.", 500);

        return $this->successResponse("User has been successfully updated", new UserResource($user));
    }

    /**
     * Update the email of the authenticated user.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function updateMail(UserRequest $request)
    {
       $user = auth()->user();

        if(!$user->update($request->validated())) return $this->errorResponse("An error occurred while updating the user, try again later.", 500);

        Mail::to($user->email)->send(new ResetEmailConfirmationMail());

        return $this->successResponse("User has been successfully updated.", new UserResource($user));
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
            return $this->errorResponse("Invalid current password.", 400);
        }

        if ($request->validated()['password'] === $request->validated()['password_current']) {
            return $this->errorResponse("New password must be different from the current password.", 400);
        }

        if(!$user->update([
            'password' => Hash::make($request->validated()['password'])
        ])) return $this->errorResponse("An error occurred while updating the password, try again later.", 500);


        Mail::to($user->email)->send(new ResetPasswordConfirmationMail());

        return $this->successResponse("Password has been successfully updated.");
    }
}
