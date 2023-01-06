<?php

use Tests\TestCase;
use App\Models\User;
use App\Models\Post;

class PostTest extends TestCase
{

    private $token;

    /**
     * @param mixed $user
     */
    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $token = \JWTAuth::fromUser($user);
        $this->token = "Bearer " . $token;
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ]);
    }

    public function test_create_post(){
        $post = Post::factory()->definition();
        $response = $this->post('api/posts' , $post)->json();
        $this->assertArrayHasKey("success" , $response);
        $this->assertEquals($response['data']['title'] , $post['title']);

    }

    public function test_show_all_posts(){
        $total = Post::all()->count();
        $response = $this->get('api/posts/')->json();
        $this->assertArrayHasKey("data" , $response);
        $this->assertEquals($response['total'] , $total);
    }

    public function test_show_one_posts(){
        $post = Post::all()->random();
        $response = $this->get("api/posts/{$post->id}")->json();
        $this->assertArrayHasKey("id" , $response);
        $this->assertEquals($response['id'] , $post->id);
    }


}
