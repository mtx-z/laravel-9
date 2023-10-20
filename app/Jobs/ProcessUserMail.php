<?php

namespace App\Jobs;

use App\Mail\UserMail;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use App\Utilities\Helpers\ElasticsearchHelper;
use App\Utilities\Helpers\RedisHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\SentMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessUserMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $to, $mail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $to, UserMail $mail)
    {
        $this->mail = $mail;
        $this->to = $to;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sent = false;
        $error = false;

        try {
            //the job is queued, no need to queue the mail. And so we can easily track the mail sending status for logging
            $result = Mail::to($this->to)
                ->send($this->mail);

            if(!$result || !$result instanceof SentMessage) {
                throw new \ErrorException('Mail sending failed' . print_r($result, true));
            }

            $sent = true;
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $elasticsearchHelper = app()->make(ElasticsearchHelper::class);
         $elasticsearchHelper->storeEmail(
                $this->mail,
                $this->mail->sender,
                $sent,
                $error
         );

        $redisHelper = app()->make(RedisHelper::class);
        $redisHelper->storeRecentMessage(
            $this->mail,
            $this->mail->sender,
            $sent,
            $error
        );
    }
}
