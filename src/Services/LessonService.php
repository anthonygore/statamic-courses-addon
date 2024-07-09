<?php

namespace Anthonygore\Courses\Services;

use Statamic\Contracts\Auth\User;
use Statamic\Entries\Entry;

class LessonService
{
    private $lesson;

    private $user;

    public function __construct(User $user, string $lesson_id)
    {
        $this->lesson = Entry::find($lesson_id);
        $this->user = $user;
    }

    public function complete()
    {
        $completed = $this->user->completed_lessons->pluck('id')->all();
        if (! in_array($this->lesson->id, $completed)) {
            $completed[] = $this->lesson->id;
            $this->user->set('completed_lessons', $completed);
            $this->user->save();
        }
    }

    public function getLesson(): Entry
    {
        return $this->lesson;
    }
}
