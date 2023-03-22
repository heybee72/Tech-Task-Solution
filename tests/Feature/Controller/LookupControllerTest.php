<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Summary of LookupControllerTest
 */
class LookupControllerTest extends TestCase
{
    use WithFaker;
    
    /**
     * Summary of testMinecraftTestingForUserIdAndUsernameInLookupController
     * @return void
     */
    public function testMinecraftTestingForUserIdAndUsernameInLookupController()
    {
        $username = "Notch";
        $userId = "d8d5a9237b2043d8883b1150148d6955";
        $type = 'minecraft';

        $payload = [
            'username' => $username,
            'id' => $userId,
            'type' => $type,
        ];
        $response = $this->json('GET', '/lookup', $payload);
        $response->assertOk();

        $response->assertJson([
            "username"=>"Notch",
            "id"=>"069a79f444e94726a5befca90e38aaf5",
            "avatar"=>"https://crafatar.com/avatars069a79f444e94726a5befca90e38aaf5"
        ])->assertStatus(200);
    }

    /**
     * Summary of testSteamTestingForUserIdInLookupController
     * @return void
     */
    public function testSteamTestingForUserIdInLookupController()
    {
        $userId = "76561198806141009";
        $type = 'steam';

        $payload = [
            'id' => $userId,
            'type' => $type,
        ];
        $response = $this->json('GET', '/lookup', $payload);
        $response->assertOk();

        $response->assertJson([
            "username"=>"Tebex",
            "id"=>"76561198806141009",
            "avatar"=>"https://avatars.akamai.steamstatic.com/c86f94b0515600e8f6ff869d13394e05cfa0cd6a.jpg"
        ])->assertStatus(200);
    }

    /**
     * Summary of testSteamTestingForUsernameInLookupControllerErrorReturned
     * @return void
     */
    public function testSteamTestingForUsernameInLookupControllerErrorReturned()
    {
        $username = "test";
        $type = 'steam';

        $payload = [
            'username' => $username,
            'type' => $type,
        ];
        $response = $this->json('GET', '/lookup', $payload);
        $response->assertStatus(500);
        $response->assertJson([
            'message' => 'Steam only supports IDs',
        ]);
    }

    /**
     * Summary of testXblTestingForUserIdAndUsernameInLookupController
     * @return void
     */
    public function testXblTestingForUserIdAndUsernameInLookupController()
    {
        $username = "tebex";
        $userId = "2533274884045330";
        $type = 'xbl';

        $payload = [
            'username' => $username,
            'id' => $userId,
            'type' => $type,
        ];
        $response = $this->json('GET', '/lookup', $payload);
        $response->assertOk();

        $response->assertJson([
            "username"=>"Tebex",
            "id"=>"2533274844413377",
            "avatar"=>"https://avatar-ssl.xboxlive.com/avatar/2533274844413377/avatarpic-l.png"
        ])->assertStatus(200);
    }

    /**
     * Test the endpoint with an invalid type parameter
     * @test
     * @return void
     */
    public function testCheckingForInvalidParameter()
    {

        $username = $this->faker->userName;
        $userId = $this->faker->randomAscii;
        $type = $this->faker->randomLetter;

        $payload = [
            'username' => $username,
            'id' => $userId,
            'type' => $type,
        ];
        $response = $this->json('GET', '/lookup', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            "errors"=> [
                "type"=> [
                    "The selected type is invalid."
                ]
            ]
        ]);
    }
}
