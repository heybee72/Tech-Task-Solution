<?php

namespace App\Services;

use App\Contracts\LookupServiceInterface;
use GuzzleHttp\Client;

class MinecraftLookupService implements LookupServiceInterface
{
    protected $guzzle;

    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }
    public function lookup($username, $userId)
    {
        $url = $username
            ? "https://api.mojang.com/users/profiles/minecraft/" . $username
            : "https://sessionserver.mojang.com/session/minecraft/profile/" . $userId;

        $response = $this->guzzle->get($url);
        $match = json_decode($response->getBody()->getContents());

        return [
            'username' => $match->name,
            'id' => $match->id,
            'avatar' => "https://crafatar.com/avatars" . $match->id
        ];
    }
}
