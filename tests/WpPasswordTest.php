<?php

use MikeMcLin\WpPassword\WpPassword;

class WpPasswordTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_make_method_calls_HashPassword_and_returns_result()
    {
        $wp_hasher = Mockery::mock('Hautelook\Phpass\PasswordHash');
        $wp_hasher->shouldReceive('HashPassword')
            ->once()
            ->withArgs(['foo'])
            ->andReturn('bar');

        WpPassword::getInstance($wp_hasher);

        $response = WpPassword::make('foo');

        $this->assertEquals('bar', $response);
    }

    public function test_make_method_trims_password_before_hashing()
    {
        $wp_hasher = Mockery::mock('Hautelook\Phpass\PasswordHash');
        $wp_hasher->shouldReceive('HashPassword')
            ->once()
            ->withArgs(['foo']);

        WpPassword::getInstance($wp_hasher);

        WpPassword::make('           foo     ');
    }

    public function test_check_method_calls_CheckPassword_and_returns_result()
    {
        $wp_hasher = Mockery::mock('Hautelook\Phpass\PasswordHash');
        $wp_hasher->shouldReceive('CheckPassword')
            ->once()
            ->withArgs(['plain-text-password', 'hashed-password-longer-than-32-chars'])
            ->andReturn('foo');

        WpPassword::getInstance($wp_hasher);

        $response = WpPassword::check('plain-text-password', 'hashed-password-longer-than-32-chars');

        $this->assertEquals('foo', $response);
    }

    public function test_check_method_detects_md5_passwords()
    {
        $password = 'plain-text-password';

        $validates = WpPassword::check('wrong-password', md5($password));
        $this->assertFalse($validates, 'incorrect password and md5 hash password should not pass check');

        $validates = WpPassword::check($password, md5($password));
        $this->assertTrue($validates, 'password and md5 hash password should pass check');

    }

}
