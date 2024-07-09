<?php

namespace Anthonygore\Courses\Tags;

use Anthonygore\Courses\Services\CourseService;
use Statamic\Facades\User;
use Statamic\Tags\Tags;

class CourseTag extends Tags
{
    protected static $handle = 'course_info';

    public function nextLessonUrl(): string
    {
        $user = User::current();
        $course_id = $this->params->get('id');
        if (! $user || ! $course_id) {
            return false;
        }
        $courseService = new CourseService($user, $course_id);

        $nextLesson = $courseService->getNextLesson();

        if (! $nextLesson) {
            return '#';
        }

        return $nextLesson->permalink;
    }

    public function nextLessonRank(): int
    {
        $user = User::current();
        $course_id = $this->params->get('id');
        if (! $user || ! $course_id) {
            return -1;
        }
        $courseService = new CourseService($user, $course_id);
        $nextLesson = $courseService->getNextLesson();
        if (! $nextLesson) {
            return -1;
        }

        return $courseService->getCourse()->lessons->search($nextLesson) + 1;
    }

    public function progress(): float
    {
        $user = User::current();
        $course_id = $this->params->get('id');
        if (! $user || ! $course_id) {
            return 0;
        }
        $courseService = new CourseService($user, $course_id);
        $numLessons = $courseService->getCourse()->lessons->count();
        $numCompletedLessons = $courseService->getCompletedLessons()->count();

        if (! $numLessons || ! $numCompletedLessons) {
            return 0;
        }

        return $numCompletedLessons / $numLessons;
    }

    public function isEnrolled(): bool
    {
        $user = User::current();
        $course_id = $this->params->get('id');
        if (! $user || ! $course_id) {
            return false;
        }
        $enrollments = $user->get('enrollments');
        if ($enrollments) {
            return in_array($course_id, $enrollments);
        } else {
            return false;
        }
    }
}
