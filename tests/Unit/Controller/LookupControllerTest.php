<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\LookupController;
use App\Services\MinecraftLookupService;
use App\Services\SteamLookupService;
use App\Services\XblLookupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use GuzzleHttp\Client;

/**
 * Summary of LookupControllerTest
 */
class LookupControllerTest extends TestCase
{
    use RefreshDatabase;
    protected $guzzle;

    /**
     * Summary of setUp
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->guzzle = new Client();
        Cache::flush();
    }

    /**
     *
     * @return void
     */
    public function testLookupWithValidMinecraftUsername()
    {
        $mockLookupService = $this->getMockBuilder(MinecraftLookupService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockLookupService->expects($this->once())
            ->method('lookup')
            ->with('username', null)
            ->willReturn(['result' => 'minecraft data']);

        $lookup = new LookupController(
            $mockLookupService,
            new SteamLookupService($this->guzzle),
            new XblLookupService($this->guzzle)
        );

        $response = $lookup->lookup(new Request([
            'username' => 'username', 
            'type' => 'minecraft'
        ]));
        $this->assertEquals(
            ['result' => 'minecraft data'], 
            json_decode($response->getContent(), 
            true
        ));
    }
    public function testLookupWithValidSteamUserId()
    {
        $mockLookupService = $this->getMockBuilder(SteamLookupService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockLookupService->expects($this->once())
            ->method('lookup')
            ->with(null, 'steamid')
            ->willReturn(['result' => 'steam data']);

        $lookup = new LookupController(
            new MinecraftLookupService($this->guzzle),
            $mockLookupService,
            new XblLookupService($this->guzzle)
        );

        $response = $lookup->lookup(new Request([
            'id' => 'steamid', 
            'type' => 'steam'
        ]));
        $this->assertEquals(
            ['result' => 'steam data'], 
            json_decode($response->getContent(), 
            true
        ));
    }

    public function testLookupWithValidXblUsername()
    {
        $mockLookupService = $this->getMockBuilder(XblLookupService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockLookupService->expects($this->once())
            ->method('lookup')
            ->with('username', null)
            ->willReturn(['result' => 'xbl data']);

        $lookup = new LookupController(
            new MinecraftLookupService($this->guzzle),
            new SteamLookupService($this->guzzle),
            $mockLookupService
        );

        $response = $lookup->lookup(new Request([
            'username' => 'username', 
            'type' => 'xbl'
        ]));
        $this->assertEquals(
            ['result' => 'xbl data'], 
            json_decode($response->getContent(), 
            true
        ));
    }

    public function testLookupWithInvalidTypeParameter()
    {
        $lookup = new LookupController(
            new MinecraftLookupService($this->guzzle),
            new SteamLookupService($this->guzzle),
            new XblLookupService($this->guzzle)
        );

        $response = $lookup->lookup(new Request(['type' => 'invalid']));
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('The given data was invalid.', json_decode($response->getContent())->message);
    }


    /**
     * Summary of testLookupWithInvalidDataShouldReturnValidationError
     * @return void
     */
    public function testLookupWithInvalidDataShouldReturnValidationError()
    {   
        $payload = [
            'username' => '',
            'id' => '',
            'type' => 'invalid_type',
        ];
        $response = $this->get('/lookup', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            "errors"=> [
                "type"=> [
                    "The type field is required."
                ]
            ]
        ]);
    }
}
