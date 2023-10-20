<?php

namespace App\Utilities\Helpers;

use App\Models\User;
use Illuminate\Mail\Mailable;
use MailerLite\LaravelElasticsearch\Facade as Elasticsearch;

class ElasticsearchHelper implements \App\Utilities\Contracts\ElasticsearchHelperInterface
{
    /**
     * @param mixed $id
     * @param string $messageSubject
     * @param string $toEmailAddress
     * @param bool $sent
     * @param string $error
     * @return mixed
     */
    public function storeEmail(Mailable $message, User $from, bool $sent, string $errors = null): mixed
    {

        return Elasticsearch::index([
            'body' => [
                'mail_id' => $message->id,
                'subject' => $message->subject,
                'to' => $message->to[0]['address'],
                'sent' => $sent,
                'errors' => $errors,
                'at' => now()->toIso8601String(),
                'from' => isset($message->from[0]) ? $message->from[0]['address'] : null,
                'from_user_id' => $message->sender ? $message->sender->id : null
            ],
            'index' => 'user_mail_log',
            'type' => '_doc',
            'id' => $message->id,
        ]);

    }

    public function getLastEmails(int $page = 1, int $limit = 20, $user_id = null): array
    {
        $query = [
            'match_all' => new \stdClass()
        ];

        if(!empty($user_id)) {
            $query = [
                'term' => [
                    'from_user_id' => $user_id,
                ],
            ];
        }
        $params = [
            'index' => 'user_mail_log',
            'body' => [
                'from' => ($page - 1) * $limit,
                'query' => $query,
                'size' => $limit,
                'sort' => [
                    'at' => [
                        'order' => 'desc',
                    ],
                ],
            ],
        ];

        $response = Elasticsearch::search($params);

        $hits = $response['hits']['hits'];

        $emails = [];

        foreach ($hits as $hit) {
            $emails[] = $hit['_source'];
        }

        return $emails;
    }
}
