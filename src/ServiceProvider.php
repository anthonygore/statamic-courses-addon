<?php

namespace Anthonygore\Courses;

use Anthonygore\Courses\Http\Controllers\CourseController;
use Anthonygore\Courses\Http\Controllers\LessonController;
use Statamic\Providers\AddonServiceProvider;
use Stillat\Relationships\Support\Facades\Relate;
use Illuminate\Support\Facades\Route;

class ServiceProvider extends AddonServiceProvider
{
    protected $tags = [
        \Anthonygore\Courses\Tags\CourseTag::class,
        \Anthonygore\Courses\Tags\LessonTag::class
    ];

    public function bootAddon()
    {
        Relate::manyToOne(
            'courses.lessons',
            'lessons.course'
        );

        $this->registerWebRoutes(function () {
            Route::statamic('/login', 'auth.login', [])->middleware('guest');
            Route::statamic('/register', 'auth.register', [])->middleware('guest');
            Route::statamic('/password/reset', 'auth.forgot_password', [])->middleware('guest');
            Route::statamic('/password/reset/{token}', 'auth.reset_password', [])->middleware('guest');
            Route::get('/complete_lesson/{lesson_id}', [LessonController::class, 'complete']);
            Route::get('/enroll/{course_id}', [CourseController::class, 'enroll']);
        });
    }
}
