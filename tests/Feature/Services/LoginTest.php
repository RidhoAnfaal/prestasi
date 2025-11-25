<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\DetailStudent;
use App\Models\DetailSupervisor;
use App\Models\Role;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LoginTest extends TestCase
{

    use RefreshDatabase;

    protected User $adminUser;
    protected User $supervisorUser;
    protected User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();

        $department = Department::create(['department_name' => 'Department Test']);

        $studyProgram = StudyProgram::create([
            'study_program_name' => 'TI',
            'department_id' => $department->department_id
        ]);


        $adminRole = Role::create(['role_name' => 'admin']);
        $supervisorRole = Role::create(['role_name' => 'supervisor']);
        $studentRole = Role::create(['role_name' => 'student']);


        $this->adminUser = User::create([
            'role_id' => $adminRole->role_id,
            'user_name' => 'Admin User',
            'user_username' => 'admin_test',
            'user_password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ]);


        $this->supervisorUser = User::create([
            'role_id' => $supervisorRole->role_id,
            'user_name' => 'Supervisor User',
            'user_username' => 'supervisor_test',
            'user_password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ]);


        DetailSupervisor::create([
            'user_id' => $this->supervisorUser->user_id,
            'detail_supervisor_nip' => '12345678',
            'department_id' => $department->department_id,
            'detail_supervisor_dob' => '1980-01-01',
            'detail_supervisor_gender' => 'male',
            'detail_supervisor_address' => 'Test Address Supervisor',
            'detail_supervisor_phone_no' => '081111111111',
            'detail_supervisor_email' => 'supervisor@test.com',
            'detail_supervisor_photo' => null,
        ]);


        $this->studentUser = User::create([
            'role_id' => $studentRole->role_id,
            'user_name' => 'Student User',
            'user_username' => 'student_test',
            'user_password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ]);


        DetailStudent::create([
            'user_id' => $this->studentUser->user_id,
            'study_program_id' => $studyProgram->study_program_id,
            'detail_student_nim' => '1941720000',
            'detail_student_gender' => 'male',
            'detail_student_dob' => '2000-01-01',
            'detail_student_address' => 'Test Address Student',
            'detail_student_phone_no' => '082222222222',
            'detail_student_email' => 'student@test.com',
            'detail_student_photo' => null,
        ]);
    }

    /** @test */
    public function user_can_login_with_username_and_be_redirected_to_dashboard()
    {
        $response = $this->post(route('login.post'), [
            'login_id' => $this->adminUser->user_username,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($this->adminUser);
        $response->assertRedirect(route('admin.dashboard'));
    }

    /** @test */
    public function supervisor_can_login_with_nip_and_be_redirected_to_dashboard()
    {
        $response = $this->post(route('login.post'), [
            'login_id' => '12345678',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($this->supervisorUser);
        $response->assertRedirect(route('supervisor.dashboard'));
    }

    /** @test */
    public function student_can_login_with_nim_and_be_redirected_to_dashboard()
    {
        $response = $this->post(route('login.post'), [
            'login_id' => '1941720000',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($this->studentUser);
        $response->assertRedirect(route('student.dashboard'));
    }


    /** @test */
    public function it_returns_validation_error_for_empty_credentials()
    {
        $response = $this->post(route('login.post'), [
            'login_id' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['login_id', 'password']);
        $this->assertGuest();
    }

    /** @test */
    public function it_returns_error_for_invalid_password()
    {
        $response = $this->post(route('login.post'), [
            'login_id' => $this->adminUser->user_username,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['login_id' => 'Login credentials are incorrect.']);
        $this->assertGuest();
    }

    /** @test */
    public function it_returns_error_for_non_existent_user()
    {
        $response = $this->post(route('login.post'), [
            'login_id' => 'non_existent_id',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['login_id' => 'Login credentials are incorrect.']);
        $this->assertGuest();
    }

    /** @test */
    // public function it_returns_error_if_email_is_not_verified()
    // {

    //     $unverifiedUser = User::create([
    //         'role_id' => Role::where('role_name', 'admin')->first()->role_id,
    //         'user_name' => 'Unverified User',
    //         'user_username' => 'unverified',
    //         'user_password' => Hash::make('password'),
    //         'email_verified_at' => null,
    //     ]);

    //     $response = $this->post(route('login.post'), [
    //         'login_id' => 'unverified',
    //         'password' => 'password',
    //     ]);

    //     $response->assertSessionHasErrors(['login_id' => 'Your email has not been verified. Please check your email for verification.']);
    //     $this->assertGuest();
    // }
}
