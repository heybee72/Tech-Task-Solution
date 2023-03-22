<?php

namespace App\Services;

use App\Contracts\LookupServiceInterface;
use GuzzleHttp\Client;


class SteamLookupService implements LookupServiceInterface
{
    protected $guzzle;

    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }
    public function lookup($username, $id)
    {
        if ($username) {
            throw new \InvalidArgumentException('Steam only supports IDs');
        }
        
        $url = "https://ident.tebex.io/usernameservices/4/username/" . $id;
        $response = $this->guzzle->get($url);
        $match = json_decode($response->getBody()->getContents());

        return [
            'username' => $match->username,
            'id' => $match->id,
            'avatar' => $match->meta->avatar
        ];
    }
}