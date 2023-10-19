<?php

namespace App\Http\Controllers;

use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;

class EmailController extends Controller
{
    // TODO: finish implementing send method
    public function send()
    {


        /** @var ElasticsearchHelperInterface $elasticsearchHelper */
        $elasticsearchHelper = app()->make(ElasticsearchHelperInterface::class);
        // TODO: Create implementation for storeEmail and uncomment the following line
        // $elasticsearchHelper->storeEmail(...);

        /** @var RedisHelperInterface $redisHelper */
        $redisHelper = app()->make(RedisHelperInterface::class);
        // TODO: Create implementation for storeRecentMessage and uncomment the following line
        // $redisHelper->storeRecentMessage(...);
    }

    //  TODO - BONUS: implement list method
    public function list()
    {

    }
}
