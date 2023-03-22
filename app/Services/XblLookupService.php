<?php

namespace App\Services;

use App\Contracts\LookupServiceInterface;
use GuzzleHttp\Client;



class XblLookupService implements LookupServiceInterface
{
    protected $guzzle;

    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }
    public function lookup($username, $id)
    {
        $url = $username
            ? "https://ident.tebex.io/usernameservices/3/username/" . $username . "?type=username"
            : "https://ident.tebex.io/usernameservices/3/username/" . $id;

        $response = $this->guzzle->get($url);
        $match = json_decode($response->getBody()->getContents());

        return [
            'username' => $match->username,
            'id' => $match->id,
            'avatar' => $match->meta->avatar
        ];
    }
}