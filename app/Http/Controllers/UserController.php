<?php

namespace App\Http\Controllers;

use App\Services\IUserService;

class UserController extends Controller
{
    public function __construct(
        private readonly IUserService $userService
    ){}

    public function index()
    {
        return $this->userService->getUsers();
    }
}
