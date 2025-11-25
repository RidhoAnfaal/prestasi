<?php

namespace Tests\Unit\Services;

use App\Services\UserService;
use App\Repositories\UserRepositoryInterface;
use App\Models\User;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private $userRepository;
    private $userService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        
        $this->userService = new UserService($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_throws_exception_when_name_is_too_short()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Name must be at least 3 characters long');

        $this->userService->register('ab', 'test@example.com', 'password');
    }

    /** @test */
    public function it_registers_a_user_with_valid_data()
    {
        $expectedUser = new User([
            'user_id' => 777,
            'user_name' => 'Ridho',
            'user_username' => 'ridho@gmail.com',
            'role_id' => 3
        ]);

        $this->userRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function($data) {
                return is_array($data) && 
                       $data['user_name'] === 'Ridho' && 
                       $data['user_username'] === 'ridho@gmail.com' &&
                       $data['role_id'] === 3;
            }))
            ->andReturn($expectedUser);

        $user = $this->userService->register('Ridho', 'ridho@gmail.com', 'password');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Ridho', $user->user_name);
        $this->assertEquals('ridho@gmail.com', $user->user_username);
    }
}
