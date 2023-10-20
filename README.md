<p align="center"><a href="https://autoklose.com" target="_blank"><img src="https://app.autoklose.com/images/svg/autoklose-logo-white.svg" width="400"></a></p>

## @mtx-z implementation
> Notes
- this is of course a PoC
- lot of things could be improved, standardized, documented, etc
- feel free to contact me for any question

> Install
- composer install
- php artisan migrate (also migrate the test database, using a .env.testing file
- php artisan db:seed (to create some users)

> Run tests
- php artisan test

> Have an endpoint as described above that accepts an array of emails, each of them having a subject, body, and the email address where is the email going to
- use the /api/{user}/send endpoint (?api_token=1234)
- data format is 
`[
  {
  "email": "test@g.fr",
  "subject" : "subject 1",
  "body": "<p>456</p>"
  },
  {
  "email": "test2@g.fr",
  "subject" : "subject 2",
  "body": "<p>123</p>"
  }
  ]`

>Build a mail using a standard set of Laravel functions for it and the default email provider (the one that is easiest for you to setup)
- UserEmail class 

>Build a job to dispatch email, use the default Redis/Horizon setup
- ProcessUserMail class

>Store information about the sent email in Elasticsearch using a class that implements the ElasticsearchHelperInterface provided. (This interface can be modified however you see fit.)

> Cache the stored information in Redis using a class that implements the RedisHelperInterface provided. (This interface can be modified however you see fit.)
- see ProcessUserMail class

> Write a unit test that makes sure that the job is dispatched correctly and also is not dispatched if there’s a validation error
- see userMailDispatchTest class 

> Have an endpoint api/list that lists all sent emails with email, subject, body
- see /api/list endpoint
- `?user=X` where X is user ID is optional
- queried from ES

> Unit test the above-mentioned route (test for expected subject/body)
- see getLastEmailsTest class 

> Upgrade the project from Laravel 9 to Laravel 10
- done (composer, Cors handler, Unit dependencies, etc)

> Make sure that emails are sent asynchronously, i.e. not blocking the send request
- Mail are sent using a dispatchable queued job, see ProcessUserMail class

> The token is used as a URI parameter in the request api_token={{your_api_token}}
- not much detail about this. As the send route take a user ID parameter, and a proper API key auth system should be able to authenticate/query the user based on the API key token
- API key is stored in config api.key (default: "1234")
- authenticate using GET parameter ?api_token=1234

## Instructions
The repository for the assignment is public and Github does not allow the creation of private forks for public repositories.

The correct way of creating a private fork by duplicating the repo is documented here.

For this assignment the commands are:

Create a bare clone of the repository.

git clone --bare git@github.com:autoklose/laravel-9.git
Create a new private repository on Github and name it laravel-9.

Mirror-push your bare clone to your new repository.

Replace <your_username> with your actual Github username in the url below.

cd laravel-9.git
git push --mirror git@github.com:<your_username>/laravel-9.git
Remove the temporary local repository you created in step 1.

cd ..
rm -rf laravel-9.git
You can now clone your laravel-9 repository on your machine (in my case in the code folder).

cd ~/code
git clone git@github.com:<your_username>/laravel-9.git
