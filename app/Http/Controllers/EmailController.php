<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessUserMail;
use App\Mail\UserMail;
use App\Models\User;
use App\Utilities\Helpers\ElasticsearchHelper;

class EmailController extends Controller
{
    // TODO: finish implementing send method
    public function send(User $user)
    {

        //validate request as array of object with body, subject, email attributes
        validator(request()->except('api_token'), [
            '*.body' => 'required|string',
            '*.subject' => 'required|string',
            '*.email' => 'required|email',
        ])->validate();

        /*request()->validate([
            '*.body' => 'required|string',
            '*.subject' => 'required|string',
            '*.email' => 'required|email',
        ]);*/

        $queued = 0;
        $errors = [];

        foreach (request()->except('api_token') as $email) {
            try {
                $mailInstance = new UserMail($email['subject'], $email['body'], $user);
                ProcessUserMail::dispatch($email['email'], $mailInstance);
                $queued++;
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return response()->json([
            'message' => 'Emails queued successfully',
            'queued' => $queued,
            'received' => count(request()->except('api_token')),
            'errors' => $errors,
        ]);
    }

    //  TODO - BONUS: implement list method
    //todo unsecure: anyone can see emails, enumerate user etc. Need be admin route or user get its own emails only if not admin
    //todo fractal for response
    public function list()
    {
        //validate request if user parameter is set (not required), integer, and user exist in db
        request()->validate([
            'user' => 'integer|exists:users,id',
        ]);

        $data = app()->make(ElasticsearchHelper::class)->getLastEmails(request()->input('page', 1), 20, (int)request()->input('user', null));
        return response()->json([
            'message' => 'ES query successful',
            'data' => $data,
            'meta' => [
                'page' => abs((int)request()->input('page', 1)),
                'limit' => 20,
                'total' => count($data),
                'order_by' => 'at',
                'order' => 'desc',
                'user' => (int)request()->input('user', null),
            ]
        ]);
    }
}
