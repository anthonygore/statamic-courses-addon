<?php

namespace Anthonygore\Courses\Services;

use Statamic\Contracts\Auth\User;
use Statamic\Entries\Entry;
use Statamic\Entries\EntryCollection;

class CourseService
{
    private $user;

    private $course;

    public function __construct(User $user, string $course_id)
    {
        $this->course = Entry::find($course_id);
        $this->user = $user;
    }

    public function getNextLesson(): ?Entry
    {
        $completed = $this->getCompletedLessons()->pluck('id')->all();

        $nextLesson = $this->course->lessons->first(function ($lesson) use ($completed) {
            return ! in_array($lesson->id, $completed);
        });

        if (! $nextLesson) {
            $this->course->lessons->first();
        }

        return $nextLesson;
    }

    public function getCourse(): Entry
    {
        return $this->course;
    }

    public function getCompletedLessons(): EntryCollection
    {
        return $this->user->completed_lessons->filter(function ($lesson) {
            return $lesson->course->id === $this->course->id;
        });
    }

    public function enroll()
    {
        $enrollments = $this->user->enrollments->pluck('id')->all();
        if (! in_array($this->course->id, $enrollments)) {
            $enrollments[] = $this->course->id;
            $this->user->set('enrollments', $enrollments);
            $this->user->save();
        }
    }
}
