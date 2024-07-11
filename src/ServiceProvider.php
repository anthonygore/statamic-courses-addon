<?php

namespace Anthonygore\Courses;

use Anthonygore\Courses\Http\Controllers\LessonController;
use Anthonygore\Courses\Listeners\UserRegistrationListener;
use Anthonygore\Courses\Tags\CourseTag;
use Anthonygore\Courses\Tags\LessonTag;
use Illuminate\Support\Facades\Route;
use Statamic\Auth\UserGroup;
use Statamic\Events\UserRegistered;
use Statamic\Facades\Role;
use Statamic\Providers\AddonServiceProvider;
use Stillat\Relationships\Support\Facades\Relate;

class ServiceProvider extends AddonServiceProvider
{
    protected $tags = [
        CourseTag::class,
        LessonTag::class,
    ];

    protected $listen = [
        UserRegistered::class => [
            UserRegistrationListener::class,
        ],
    ];

    protected function registerRoutes()
    {
        $this->registerWebRoutes(function () {
            Route::statamic('/login', 'auth.login', [])->name('courses.auth.login')->middleware('guest');
            Route::statamic('/register', 'auth.register', [])->name('courses.auth.register')->middleware('guest');
            Route::statamic('/password/reset', 'auth.forgot_password', [])->name('courses.auth.password_forgot')->middleware('guest');
            Route::statamic('/password/reset/{token}', 'auth.reset_password', [])->name('courses.auth.password_reset')->middleware('guest');
            Route::get('/complete_lesson/{lesson_id}', [LessonController::class, 'complete'])->name('courses.lesson.complete');
        });
    }

    protected function registerRolesAndGroups()
    {
        if (! Role::find('student')) {
            Role::make()
                ->handle('student')
                ->title('Student')
                ->permissions([
                    'view courses entries',
                    'view lessons entries',
                ])
                ->save();
        }

        if (! UserGroup::find('students')) {
            UserGroup::make()
                ->handle('students')
                ->title('Students')
                ->roles(['student'])
                ->save();
        }
    }

    public function bootAddon()
    {
        Relate::manyToOne(
            'courses.lessons',
            'lessons.course'
        );

        $this->registerRoutes();
        $this->registerRolesAndGroups();
    }
}
