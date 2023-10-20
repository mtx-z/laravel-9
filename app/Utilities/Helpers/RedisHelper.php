<?php

namespace App\Utilities\Helpers;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Cache;

class RedisHelper implements \App\Utilities\Contracts\RedisHelperInterface
{
    /**
     * @param mixed $id
     * @param string $messageSubject
     * @param string $toEmailAddress
     * @return void
     * todo handle many recipients, cc, bcc
     */
    public function storeRecentMessage(Mailable $message, User $from, bool $sent, string $errors = null): mixed
    {

        return Cache::store('redis')->put($message->id, [
            'mail_id' => $message->id,
            'subject' => $message->subject,
            'to' => $message->to[0]['address'],
            'sent' => $sent,
            'errors' => $errors,
            'at' => now()->toIso8601String(),
            'from' => isset($message->from[0]) ? $message->from[0]['address'] : null,
            'from_user_id' => $message->sender ? $message->sender->id : null,
        ], now()->addMonth());
    }
}
