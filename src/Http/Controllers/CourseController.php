<?php

namespace Anthonygore\Courses\Http\Controllers;

use Anthonygore\Courses\Services\CourseService;
use Illuminate\Http\Request;
use Statamic\Facades\User;

class CourseController
{
    /*
     * As it is, the enroll method will enroll a user in a course for free.
     *
     * You should customize this method to suit your desired method of enrollment.
     *
     */
    public function enroll(Request $request, string $course_id)
    {
        $user = User::current();
        if (! $user) {
            /*
             * If it is a guest user, we invite them to create an account and enroll them
             * once they've done that.
             */
            $request->session()->flash('error', 'You\'ll need to log in or register before you enroll.');
            $request->session()->put('redirect_url', '/enroll/'.$course_id);

            return redirect('/login');
        }
        /*
         * Otherwise we enroll them immediately.
         */
        $courseService = new CourseService($user, $course_id);
        $courseService->enroll();
        $course = $courseService->getCourse();
        $request->session()->flash('success', 'You\'ve successfully enrolled in '.$course->title.'!');

        return redirect($course->permalink);

    }
}
