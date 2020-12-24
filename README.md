# The Gilded Rose Online Store test

This is a simple application built on Laravel 8 with a partially decoupled frontend built in VueJs. It's built as a database application and 
views have lower priority based on the task at hand. The approach I took serves to show my strengths in application architecture in both frontend and backend.

## Installation
The only requirement for this application is that you have Docker Desktop (Windows and MacOS) or Docker (*nix) installed. Do also make sure to shut-off 
your local installation of mysql, apache / Nginx and redis as the ports would clash with the exposed ports from docker. Also, on Windows, make sure you have
Windows Subsystem for Linux 2 (WSL2) installed and enabled.

- Pull the repo onto your machine (works on Linux, MacOS and Windows).
- In a terminal window, enter the project folder `cd ecommerce_app`.
- Run `./vendor/bin/sail up -d` to initialize the docker environment with PHP8, MySQL and Redis. This step will take a while first time around, but subsequently gets better.
- Once installation is done, you should run `./vendor/bin/sail npm i && ./vendor/bin/sail npm run dev`. This should install all JS dependencies.
- Run `./vendor/bin/sail composer install` to install all composer dependencies for PHP.
- Finally, run `./vendor/bin/sail artisan setup` which is a singular command to migrate the DB, seed and setup Passport keys.

Once all is done without errors, your may access the application via http://0.0.0.0. 

## Tests
I implemented three tests that were relevant to this implementation. 
- Authentication test
- Purchase test when not authenticated
- Purchase test when authenticated

All tests are done over the http call. To run the test, in the root folder type `./vendor/bin/sail tests`.

# Reason for my decoupled architecture in front-end.
Truly it would be easy to build an application using simple tools Laravel already provides, JetStream being one. I wanted to appeal more to the 
mobile first nature of the company and built an API centric application instead. I like to think ahead and noticed that based on the requests it might 
be possible that we are looking to integrate the build into a mobile devices as a mobile app. Building this way gives us two possibilities:
- PWA (Progressive Web Applications)
- API's integration into native or cross platformed mobile apps

## API's architecture
I wanted to show my strengths in designing an API for the store, but also show that the main component that makes the application work may also be independent
of the knowledge that it's a Laravel application. The only part of the application that should be fully be controlled by Laravel is the authentication (user access).

Going forward, instead of informing Laravel of every single controller based routes in the api.php file, we could use resource based mapping to build a set of 
Objects to be used in processing the request. For example:

GET /api/users would map to a definition 

`[
    'use_case' => 'App/UseCase/Users',
    'model' => 'User',
    'resource' => 'App/Resources/User' // not added but you get the point
]`

This way we totally reduce the number of lines we can have in the routes/api.php file while maintaining a standard as far as code is concerned. 

All that's really needed is the HTTP methods
GET collection
GET model item
POST model item addition
PUT model item update
DELETE model item delete

Adding to this, I added checks along the way to review a request and look for methods within the use_case class for the defined resource and execute them. This would help in 
executing pre-defined methods that are not part of the standard API calls.

I implemented a sample encapsulation of the Eloquent ORM in repositories again envisaging that the app should be dumb and not know it's a Laravel app from within the logic files.

## Shortcuts
Ideally I would want to build the frontend to be a little uniquer than it is now. I decided to go with Vuetify as the component based framework of choice to save time in building 
custom frontend controls like form fields and cards. Vuetify hands me the tools to simply build and not worry much about interactions.

## Authentication
I went with Laravel Passport package as far as authentication is concerned. Again seeing that the application may require integration with several third party services (as most online shops
these days do), having the ability to programmatically and visually manage Oauth access comes as an important aspect to the app. Implementing it now would save the team a lot of time trying to migrate in the future.


