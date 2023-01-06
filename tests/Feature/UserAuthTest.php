<?php

use App\Models\User;
use App\Models\Post;
use Tests\TestCase;


class UserAuthTest extends TestCase
{

    public function test_user_register(){
        $user = User::factory()->definition();
        $response = $this->post('api/register' , $user)->json();
        $this->assertArrayHasKey("success" , $response);
    }

    public function test_user_login(){
        $user = User::factory()->create();
        $data = ["email" => $user->email , "password" => "password"];
        $response = $this->post('api/login' , $data)->json();
        $this->assertArrayHasKey("success" , $response);
    }

    public function test_user_logout(){
        $user = User::factory()->create();
        $data = ["email" => $user->email , "password" => "password"];
        $response = $this->post('api/login' , $data)->json();
        $this->get('api/logout' , ["token" => 'Bearer ' . \JWTAuth::fromUser($user)])->json();
        $this->assertArrayHasKey("success" , $response);
    }

}
