<?php

namespace Tests\Feature\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeUserRepository;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserServiceIntegrationTest extends TestCase
{
    use RefreshDatabase;  

    private $userService;
    private $fakeUserRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('migrate:fresh');
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\TestDatabaseSeeder']);
        
        $this->userService = new UserService(new UserRepository());
        $this->fakeUserRepository = new FakeUserRepository();
    }

    /** @test */
    public function it_can_register_a_user_with_real_repository()
    {
        $user = $this->userService->register(
            'Ridho Anfaal', 
            'ridho@gmail.com', 
            'password123',
            3 
        );

        $this->assertDatabaseHas('users', [
            'user_name' => 'Ridho Anfaal',
            'user_username' => 'ridho@gmail.com',
            'role_id' => 3
        ]);

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password123', $user->user_password));
    }

    /** @test */
    public function it_can_register_a_user_with_fake_repository()
    {
        $serviceWithFakeRepo = new UserService($this->fakeUserRepository);
        
        $user = $serviceWithFakeRepo->register(
            'Anfaal Ridho', 
            'anfaal@gmail.com', 
            'password123',
            3 
        );

        $this->assertEquals('Anfaal Ridho', $user->user_name);
        $this->assertEquals('anfaal@gmail.com', $user->user_username);
        $this->assertEquals(3, $user->role_id);
        
        $foundUser = $this->fakeUserRepository->findById($user->user_id);
        $this->assertNotNull($foundUser);
        $this->assertEquals('Anfaal Ridho', $foundUser->user_name);
    }
}
