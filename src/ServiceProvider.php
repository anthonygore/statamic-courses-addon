<?php

namespace Anthonygore\Courses;

use Anthonygore\Courses\Http\Controllers\LessonController;
use Illuminate\Support\Facades\Route;
use Statamic\Providers\AddonServiceProvider;
use Stillat\Relationships\Support\Facades\Relate;

class ServiceProvider extends AddonServiceProvider
{
    protected $tags = [
        \Anthonygore\Courses\Tags\CourseTag::class,
        \Anthonygore\Courses\Tags\LessonTag::class,
    ];

    public function bootAddon()
    {
        Relate::manyToOne(
            'courses.lessons',
            'lessons.course'
        );

        $this->registerWebRoutes(function () {
            Route::statamic('/login', 'auth.login', [])->name('courses.auth.login')->middleware('guest');
            Route::statamic('/register', 'auth.register', [])->name('courses.auth.register')->middleware('guest');
            Route::statamic('/password/reset', 'auth.forgot_password', [])->name('courses.auth.password_forgot')->middleware('guest');
            Route::statamic('/password/reset/{token}', 'auth.reset_password', [])->name('courses.auth.password_reset')->middleware('guest');
            Route::get('/complete_lesson/{lesson_id}', [LessonController::class, 'complete'])->name('courses.lesson.complete');
        });
    }
}
