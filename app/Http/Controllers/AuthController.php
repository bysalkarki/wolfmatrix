<?php
namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Repositories\UserRepository;
use App\Services\JwtService;
use App\Services\LoginAttemptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $userRepository;
    protected $jwtService;
    protected $loginAttemptService;

    public function __construct(
        UserRepository $userRepository,
        LoginAttemptService $loginAttemptService
    ) {
        $this->userRepository = $userRepository;
        $this->jwtService = JwtService::getInstance();
        $this->loginAttemptService = $loginAttemptService;
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->userRepository->create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "phone" => $request->phone,
        ]);

        $token = $this->jwtService->generateToken($user);

        return response()->json(
            [
                "user" => $user,
                "token" => $token,
            ],
            201
        );
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required|string",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $email = $request->email;
        $delay = $this->loginAttemptService->getDelay($email);

        if ($delay > 0) {
            return response()->json(
                [
                    "message" => "Too many attempts. Please wait.",
                    "retry_after" => $delay,
                ],
                429
            );
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->loginAttemptService->recordFailedAttempt($email);
            return response()->json(["message" => "Invalid credentials"], 401);
        }

        $this->loginAttemptService->clearAttempts($email);
        $token = $this->jwtService->generateToken($user);

        return response()->json([
            "user" => $user,
            "token" => $token,
        ]);
    }

    public function refresh()
    {
        $token = $this->jwtService->refreshToken();
        return response()->json(["token" => $token]);
    }

    public function logout()
    {
        $this->jwtService->invalidateToken();
        return response()->json(["message" => "Successfully logged out"]);
    }
}
