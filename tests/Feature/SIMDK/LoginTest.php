<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{

    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    

    public function test_r1fgs1_login_with_no_have_account(): void
    {
        // $this->define_account(true, false, true);
        $response = $this->post(route('login.store'), [
            'email_institusi' => 'x@y',
            'password' => 'gdshjkda',
        ]);
        // dd($response);
        $this->assertNotEquals(200, $response->getStatusCode());

    }



    public function test_r1scr2_login_with_wrong_variable_format(): void
    {
        // $this->define_account(true, false, true);
        $response = $this->post(route('login.store'), [
            'email_institusi' => 'xxx',
            'password' => 'gdshjkda',
        ]);
        $response->assertSessionHasErrors([
            'email_institusi' => 'The email institusi field must be a valid email address.',
        ]);

    }

    public function test_r1scr3_login_with_wrong_email_or_password_but_true_format(): void
    {
        // $this->define_account(true, false, true);
        $response = $this->post(route('login.store'), [
            'email_institusi' => 'xxx@xxx',
            'password' => 'gdshjkda',
        ]);
        $response->assertSessionHasErrors();
        $this->assertContains(
            'These credentials do not match our records.',
            session('errors')->all()
        );
    }

    public function test_r1scr4_login_with_corret_email_and_password_also_correct_format_but_email_not_yet_verified(): void
    {
        $email = 'tes@yyy.com';
        $this->define_account(true, false, false, $email);
        $response = $this->post(route('login.store'), [
            'email_institusi' => $email,
            'password' => 'password123',
        ]);
        // dd($response);
        $response->assertSessionHasErrors();
        $this->assertContains(
            'email pribadi belum terverifikasi',
            session('errors')->all()
        );
    }

    public function test_r1scr5_login_with_corret_email_and_password_also_correct_format_and_email_already_verified_but_nonactive(): void
    {
        $email = 'nonaktif@yyy.com';
        $this->define_account(true, false, true, $email, false);
        $response = $this->post(route('login.store'), [
            'email_institusi' => $email,
            'password' => 'password123',
        ]);
        // dd($response);
        // $response->assertSessionHasErrors();
        $response->assertSessionHas('error_alert');
        $this->assertEquals(
            'Akun anda sudah dinonaktifkan! Silahkan menghubungi pihak SDM apabila ada yang dibutuhkan!.',
            session('error_alert')
        );
        // $this->assertContains(
        //     'email pribadi belum terverifikasi',
        //     session('errors')->all()
        // );
    }

    public function test_r1fgs6_login_success(): void
    {
        $this->define_account(true, false, true);
        $response = $this->post(route('login.store'), [
            'email_institusi' => 'admin@telkomuniversity.ac.id',
            'password' => 'password123',
        ]);
        // dd($response);
        $response->AssertStatus(302);

    }
}
