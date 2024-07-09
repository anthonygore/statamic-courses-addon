<?php

namespace Anthonygore\Courses\Http\Controllers;

use Anthonygore\Courses\Services\CourseService;
use Anthonygore\Courses\Services\LessonService;
use Statamic\Entries\Entry;
use Statamic\Facades\User;

class LessonController
{
    public function complete($id)
    {
        $user = User::current();
        if (! $user) {
            return back();
        }

        $lessonService = new LessonService($user, $id);
        $lessonService->complete();

        $lesson = Entry::find($id);
        $course = $lesson->course;

        $courseService = new CourseService($user, $course->id);
        $nextLesson = $courseService->getNextLesson();

        if (! $nextLesson) {
            return redirect($course->permalink);
        }

        return redirect($nextLesson->permalink);
    }
}
