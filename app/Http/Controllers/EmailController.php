<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessUserMail;
use App\Mail\UserMail;
use App\Models\User;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use App\Utilities\Helpers\ElasticsearchHelper;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    // TODO: finish implementing send method
    public function send(User $user)
    {

        //validate request as array of object with body, subject, email attributes
        request()->validate([
            '*.body' => 'required|string',
            '*.subject' => 'required|string',
            '*.email' => 'required|email',
        ]);

        $queued = 0;
        $errors = [];

        foreach (request()->all() as $email) {
            try {
                $mailInstance = new UserMail($email['subject'], $email['body'], $user);
                ProcessUserMail::dispatch($email['email'], $mailInstance);
                $queued++;
            } catch (\Exception $e) {
                dd($e);
                $errors[] = $e->getMessage();
            }
        }

        return response()->json([
            'message' => 'Emails queued successfully',
            'queued' => $queued,
            'received' => count(request()->all()),
            'errors' => $errors,
        ]);
    }

    //  TODO - BONUS: implement list method
    public function list()
    {
        //validate request if user parameter is set (not required), integer, and user exist in db
        request()->validate([
            'user' => 'integer|exists:users,id',
        ]);

        return response()->json([
            'message' => 'Emails queued successfully',
            'data' => app()->make(ElasticsearchHelper::class)->getLastEmails(request()->input('page', 1), 20, (int)request()->input('user', null)),
            'meta' => [
                'page' => abs((int)request()->input('page', 1)),
                'limit' => 20,
                'user' => (int)request()->input('user', null),
            ]
        ]);
    }
}
