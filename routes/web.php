<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::get('/robots.txt', 'SeoController@robots');
Route::get('/sitemap.xml', 'SeoController@sitemap');

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
    ],
], function () {
    Route::group(
        ['namespace' => 'Frontend', 'middleware' => ['metas']],
        function () {
            Route::get('/', 'FrontendController@index')->name('home');
            Route::get('/about', 'FrontendController@about')->name('about');
            Route::match(['GET', 'POST'], '/contact',
                'FrontendController@contact')
                ->name('contact');
            Route::get('/contact-sent', 'FrontendController@contactSent')->name(
                'contact-sent'
            );
            Route::get('/legal-mentions', 'FrontendController@legalMentions')
                ->name(
                    'legal-mentions'
                );
        }
    );

    Route::group(
        ['namespace' => 'Auth'],
        function () {
            // Authentication Routes...
            Route::get('admin', 'LoginController@showLoginForm')->name('login');
            Route::post('admin', 'LoginController@login');
            Route::get('logout', 'LoginController@logout')->name('logout');

            // Password Reset Routes...
            Route::get(
                'password/reset',
                'ForgotPasswordController@showLinkRequestForm'
            );
            Route::post(
                'password/email',
                'ForgotPasswordController@sendResetLinkEmail'
            );
            Route::get(
                'password/reset/{token}',
                'ResetPasswordController@showResetForm'
            );
            Route::post('password/reset', 'ResetPasswordController@reset');
        }
    );

    // Admin Routes
    Route::group(
        [
            'namespace' => 'Backend',
            'prefix' => 'admin',
            'as' => 'admin.',
            'middleware' => ['auth'],
        ],
        function () {
            Route::get('dashboard', 'DashboardController@index')
                ->name('dashboard');

            Route::group(
                ['middleware' => ['can:manage-users']],
                function () {
                    Route::resource('user', 'UserController',
                        ['except' => ['show']]);
                    Route::post('user/search', 'UserController@search')->name(
                        'user.search'
                    );
                    Route::get('user/{user}/login-as', 'UserController@loginAs')
                        ->name(
                            'user.login-as'
                        );
                }
            );

            Route::get('profile/edit', 'UserController@profileEdit')->name(
                'profile.edit'
            );
            Route::patch('profile/update', 'UserController@profileUpdate')
                ->name(
                    'profile.update'
                );
            Route::get('logout-as', 'UserController@logoutAs')->name(
                'user.logout-as'
            );
        }
    );
});
