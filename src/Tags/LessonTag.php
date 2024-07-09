<?php

namespace Anthonygore\Courses\Tags;

use Statamic\Facades\User;
use Statamic\Tags\Tags;

class LessonTag extends Tags
{
    protected static $handle = 'lesson_info';

    public function isCompleted(): bool
    {
        $user = User::current();
        $lesson_id = $this->params->get('id');
        if (! $user || ! $lesson_id) {
            return false;
        }
        $completed = $user->get('completed_lessons');
        if (! $completed) {
            return false;
        }

        return in_array($lesson_id, $completed);
    }
}
