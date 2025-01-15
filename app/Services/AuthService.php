<?php

namespace App\Services;

use App\DTO\User\UserDTO;
use App\Events\RecoverPasswordEvent;
use App\Exceptions\BusinessException;
use App\Http\Response\BusinessResponse;
use App\Models\UserModel;
use App\Utils\PARSE_MODE;
use App\Utils\ParseConvention;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use stdClass;

class AuthService
{
    public function __construct(
        private UserService $userService,
        private ParseConvention $parseConvention
    ){}

    public function login($data){
        $user = $this->validateUser($data);
        $this->deleteTokens($user);
        $abilities = null;

        foreach ($user->roles as $role) {
            $abilities[] = $role->name;
        }

        $token = new stdClass();
        $token->token = $user->createToken($user->username.'-AuthToken', $abilities)->plainTextToken;

        return $token;
    }

    private function validateUser($data): UserModel{
        $user = null;

        try{
            $user = $this->userService->getByEmail($data['email'], false);

            if(!Hash::check($data['password'], $user->password))
                throw new BusinessException();

        }catch (BusinessException $e){
            throw new BusinessException('Verifique suas credenciais e tente novamente', 400);
        }

        return $user;
    }

    public function register($data){
        return $this->userService->create($data);
    }

    public function recoveryPassword($data){
        $user = $this->userService->getByEmail($data['email'], false);
        $userDto = $this->parseConvention->parse($user->getOriginal(), PARSE_MODE::snakeToCamel, UserDTO::class);
        $this->deleteResetPasswordTokens($user->id);

        $expiresAt = now()->addMinutes(30);
        $token = $user->createToken($userDto->username . '-resetToken', ['reset-password'], $expiresAt->toDateTime())->plainTextToken;

        RecoverPasswordEvent::dispatch($userDto, config('app.url') . '/api/reset-password?token='.$token);
    }

    private function deleteResetPasswordTokens($userId){
        $tokens = DB::table('personal_access_tokens')
            ->where('tokenable_id', $userId)
            ->whereJsonContains('abilities', ['reset-password'])
            ->get();

        foreach ($tokens as $token) {
            DB::table('personal_access_tokens')->where('id', $token->id)->delete();
        }
    }

    public function resetPassword(string $tokenString, $pwdData){
        $token = PersonalAccessToken::findToken($tokenString);

        dd($token);
    }

    public function useTerms(){
        dd("UseTherms");
    }

    public function acceptTerms(){
        dd("AcceptTherms");
    }

    public function logout(int $userId){
        $user = $this->userService->getById($userId, [], false);

        $this->deleteTokens($user);
        return true;
    }

    private function deleteTokens($user){
        $user->tokens()->delete();
    }
}
