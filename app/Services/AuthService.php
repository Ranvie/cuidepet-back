<?php

namespace App\Services;

use App\DTO\User\SafeUserDTO;
use App\DTO\User\UserDTO;
use App\Events\RecoverPasswordEvent;
use App\Exceptions\BusinessException;
use App\Models\UserModel;
use App\Utils\PARSE_MODE;
use App\Utils\ParseConvention;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use stdClass;

class AuthService
{
    public function __construct(
        private UserService $userService,
        private ParseConvention $parseConvention
    ){}

    //TODO: Falta colocar o token para expirar
    //TODO: Fazer uma espécie de refreshToken
    public function login($data){
        $user = $this->validateUser($data);
        $this->deleteTokens($user);
        $abilities = [];

        foreach ($user->roles as $role) {
            $abilities[] = $role->name;
        }

        $response = new stdClass();
        $expiresAt = now()->addMinutes(env('TOKEN_LOGIN_EXPIRE_MINUTES'));
        $response->token = $user->createToken($user->username.'-AuthToken', $abilities, $expiresAt->toDateTime())->plainTextToken;
        $response->user = ParseConvention::parse($user->getOriginal(), PARSE_MODE::snakeToCamel, SafeUserDTO::class);
        return $response;
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
        try{
            $user = $this->userService->getByEmail($data['email'], false);
        }catch (BusinessException $e){
            return;
        }

        $userDto = $this->parseConvention->parse($user->getOriginal(), PARSE_MODE::snakeToCamel, UserDTO::class);
        $this->deleteResetPasswordTokens($user->id);

        $expiresAt = now()->addMinutes(env('TOKEN_RESETPASSWORD_EXPIRE_MINUTES'));
        $token = $user->createToken($userDto->username . '-ResetToken', ['reset-password'], $expiresAt->toDateTime())->plainTextToken;

        //TODO: APP_URL não vai funcionar, ele vai literalmente escrever isso na URL
        RecoverPasswordEvent::dispatch($userDto, env('APP_URL_FRONT', 'APP_URL').'/reset-password?token='.$token);
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

    public function resetPassword($pwdData){
        $userId = auth()->user()->getOriginal()['id'];
        $this->userService->edit($userId, $pwdData);

        $this->deleteResetPasswordTokens($userId);
        return true;
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
