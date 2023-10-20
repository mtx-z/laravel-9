<?php

namespace App\Utilities\Contracts;

use App\Models\User;
use Illuminate\Mail\Mailable;

interface ElasticsearchHelperInterface {
    /**
     * @param string $messageBody
     * @param string $messageSubject
     * @param string $toEmailAddress
     * @param bool $sent
     * @param string|null $errors
     * @return mixed
     */
    public function storeEmail(Mailable $message, User $from, bool $sent, string $errors = null): mixed;
}
