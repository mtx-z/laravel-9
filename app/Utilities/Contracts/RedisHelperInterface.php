<?php

namespace App\Utilities\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;

interface RedisHelperInterface {
    /**
     * Store the id of a message along with a message subject in Redis.
     *
     * @param  mixed  $id
     * @param  string  $messageSubject
     * @param  string  $toEmailAddress
     * @return void
     */
    public function storeRecentMessage(Mailable $message, User $from, bool $sent, string $errors = null): mixed;
}
