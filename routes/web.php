<?php

$router->group(['namespace' => 'Api', 'prefix' => 'api'], function ($router) {
    $router->get('/navigation', 'NavigationController@index');
    $router->get('/date', 'DateController@index');

    $router->group(['prefix' => 'user'], function ($router) {
        $router->post('/auth', 'UserController@auth');

        // Needs token for request
        $router->group(['middleware' => 'token'], function ($router) {
            $router->post('/delete', 'UserController@delete');

            $router->post('/calendar[/{timestamp}]', 'CalendarController@index');

            $router->post('/activities', 'ActivitiesController@index');
            $router->post('/activities/join', 'ActivitiesController@join');
            $router->post('/activities/leave', 'ActivitiesController@leave');
        });
    });
});

$router->get('/{route:.*}/', 'VueController@index');
