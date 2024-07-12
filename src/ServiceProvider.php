<?php

namespace Anthonygore\Courses;

use Anthonygore\Courses\Http\Controllers\LessonController;
use Anthonygore\Courses\Listeners\UserRegistrationListener;
use Anthonygore\Courses\Tags\CourseTag;
use Anthonygore\Courses\Tags\LessonTag;
use Illuminate\Support\Facades\Route;
use Statamic\Auth\UserGroup;
use Statamic\Entries\Collection;
use Statamic\Events\UserRegistered;
use Statamic\Facades\Role;
use Statamic\Fields\Blueprint;
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

    protected $middlewareGroups = [];

    protected function registerRoutes()
    {
        $this->registerWebRoutes(function () {
            Route::statamic('/login', 'statamic_courses.default.auth.login', [])->name('courses.auth.login')->middleware('guest');
            Route::statamic('/register', 'statamic_courses.default.auth.register', [])->name('courses.auth.register')->middleware('guest');
            Route::statamic('/password/reset', 'statamic_courses.default.auth.forgot_password', [])->name('courses.auth.password_forgot')->middleware('guest');
            Route::statamic('/password/reset/{token}', 'statamic_courses.default.auth.reset_password', [])->name('courses.auth.password_reset')->middleware('guest');
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

    protected function fieldsetOnBlueprint(Blueprint $blueprint, string $fieldset)
    {
        $fields = $blueprint->fields()->items();

        return $fields->contains(function ($item) use ($fieldset) {
            return array_key_exists('import', $item) && $item['import'] === 'courses::'.$fieldset;
        });
    }

    protected function fieldsetImportedByCollection(Collection $collection, string $fieldset): bool
    {
        $blueprint = $collection->entryBlueprints()->first();

        return $this->fieldsetOnBlueprint($blueprint, $fieldset);
    }

    protected function fieldsetImportedByUser(string $fieldset): bool
    {
        $userBlueprint = Blueprint::find('user');

        return $this->fieldsetOnBlueprint($userBlueprint, $fieldset);
    }

    protected function validateCollectionsAndFields()
    {
        $courseCollection = Collection::find('courses');
        if ($courseCollection) {
            if (! $this->fieldsetImportedByCollection($courseCollection, 'course')) {
                throw new \Exception("Collection 'courses' must use fieldset 'courses::course'");
            }
        } else {
            throw new \Exception("Collection 'courses' is required");
        }
        $lessonsCollection = Collection::find('lessons');
        if ($lessonsCollection) {
            if (! $this->fieldsetImportedByCollection($lessonsCollection, 'lesson')) {
                throw new \Exception("Collection 'lessons' must use fieldset 'courses::lesson'");
            }
        } else {
            throw new \Exception("Collection 'lesson' is required");
        }
        if (! $this->fieldsetImportedByUser('user')) {
            throw new \Exception("Users must use fieldset 'courses::user'");
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
        $this->validateCollectionsAndFields();
    }
}
