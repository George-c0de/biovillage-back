<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ReferralTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('DELETE FROM clients WHERE phone LIKE \'+79999999%\'');
    }


    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Bonus for first order
     *
     * @return void
     */
    public function testFirstClientReg()
    {

        // Send sms
        $phone = '+7 999 999-99-99';
        $resp = $this->post('/api/v1/client/register/request', [
            'phone' => $phone
        ]);
        $resp->assertStatus(200);
        $resp->assertJsonFragment([
            'success' => true,
        ]);

        // Check sms
        $resp = $this->post('/api/v1/client/register/verify', [
            'code' => '11111',
            'platform' => 'iOS',
            'phone' => $phone
        ]);
        $resp->assertStatus(200);
        $resp->assertJsonFragment([
            'success' => true,
        ]);

        // Got token
        $token = $resp->json('result.token');
        $this->assertNotEmpty($token);


    }

}
