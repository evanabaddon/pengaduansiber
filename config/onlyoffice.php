<?php

return [
    'jwt_secret' => env('ONLYOFFICE_JWT_SECRET'),
    'shard_key'  => env('ONLYOFFICE_SHARD_KEY'),
    'server_url' => env('ONLYOFFICE_SERVER_URL', 'https://office.kiwkiw.biz.id'),
];
