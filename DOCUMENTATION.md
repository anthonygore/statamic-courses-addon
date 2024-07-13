# Statamic Courses

## Installation

You'll need *both* of these packages in your Statamic project:

- Statamic Courses Starter Kit (a free starter kit)
- Statamic Courses Addon (a paid addon)

> Note: by installing the starter kit you will automatically install the addon.

### Installing from scratch

To create a Statamic project from scratch using the Courses starter kit and Statamic CLI:

```shell
statamic new my-site anthonygore/courses-starter-kit
```

### Add to an existing project

To add the starter kit to an existing Statamic project:

```shell
php please starter-kit:install anthonygore/courses-starter-kit
```

## Key content types

### Courses

Courses are a collection type. You can manage courses exactly like any other Statamic collection from the control panel or via YAML files.

A notable field in the courses collection is `lessons`. This is an ordered array of lesson entries.

### Lessons

Like courses, lessons are a collection type that you can manage from the control panel or via YAML files. 

> A common customization of the lessons content type is to add a `video` field where you can add a Youtube URL or Vimeo IDs, etc.

A notable field in the lessons collection is `course`. You need to select one (and only one) course for the lesson to belong to.

### Students

Students are simply Statamic users with the `student` role. 

There are two notable fields on the user model for students:

- `enrollments` an array of courses the student is enrolled in
- `completed_lessons` an array of lessons the student has completed

## Enrollment

Students must enroll in a course to be able to see lesson content, complete lessons, etc.

Out of the box, students will be enrolled in a course for free after they click the "Enroll" button on a course.

However, you can customize the enrollment flow to allow your courses to be paid, part of a membership, etc.

### Free enrollment (default)

In the starter kit, you'll see we've provided a controller called `CourseController` (found in `app/Http/Controllers`).

When a student clicks the "Enroll" button on a course, the `enroll` method of this controller is evoked.

The boilerplate logic in this method will enroll a student directly in a course. This is done by calling the `enroll` method of the `CourseService` class:

```php
$courseService = new CourseService($user, $course_id);
$courseService->enroll();
```

### Paid enrollment

If you want students to pay for your courses, you'll need to edit the `enroll` method in `CourseController`. 

For example, you might store the user's ID in a session token, redirect to Stripe, then enroll the student once they're redirected back to the site after a successful purchase.

This part is up to you!

## Student authentication

Your admin users will log in or out via the control panel. But users with the `student` role must instead use the auth pages we provide in the frontend.

You will find the templates for these auth views in the starter kit frontend.

To see a list of the auth routes we provide for students run this command: 

```php
artisan route:list --name=courses.auth
```

## Views

The starter kit contains Antlers templates for the key aspects of your online course. You can, of course, customize these to your own unique design and UX.

See `resources/views` in the starter kit.

## Tags

One of the core features of this package is the custom tags we provide so you can add course functionality to your frontend.

### course_info tag

The `course_info` tag provides dynamic functionality to your course pages.

#### course_info:is_enrolled

Returns true or false if the user is enrolled in the specified course.

Return: `bool`

Parameters:
- **id** `string` the course ID.

Example:

```
{{ if {course_info:is_enrolled id="{{ id }}"} }}
  <p>Only enrolled students can see this!</p>
{{ else }}
  <p>Please enroll in this course</p>
{{ /if }}
```

#### course_info:progress

Returns a value between 0 and 1 representing course progress (i.e. completed lessons divided by total lessons).

Return: `float`

Parameters:
- **id** `string` the course ID.

Example:

```html
<p>You have completed {{ {course_info:progress id="{{ id }}"} | multiply:100 | round:0 }}% of this course.</p>
```

#### course_info:next_lesson_url

Returns the URL of the next lesson.

Return: `string`

Parameters:
- **id** `string` the course ID.

Example:

```html
<a href="{{ course_info:next_lesson_url id='{{ id }}' }}">
    Resume your course
</a>
```

#### course_info:next_lesson_rank

Returns the position of the next lesson i.e. if the next lesson is the 4th lesson of the course this will return `4`.

Return: `int`

Parameters:
- **id** `string` the course ID.

Example:

```html
<a href="{{ course_info:next_lesson_url id='{{ id }}' }}">
    Get started on lesson {{ course_info:next_lesson_rank id="{{ id }}" }}
</a>
```

### lesson_info tag

The `lesson_info` tag provides dynamic functionality to your lesson pages.

#### lesson_info:is_complete

Returns completion status of lesson.

Return: `bool`

Parameters:
- **id** `string` the lesson ID.

Example:

```html
<p>This lesson is {{ {lesson_info:is_complete id="{{ id }}"} ? 'complete' : 'incomplete' }}</p>
```

## Endpoints

The following endpoints will trigger a specific action when the endpoint is requested.

### Begin enrollment

Route: `GET /enroll/{course_id}`

Handled by the `enroll` method of `CourseController`.

Example:

```html
<form action="/enroll/{{ id }}" method="GET">
    <button type="submit">Enroll now</button>
</form>
```

### Mark lesson complete

Route: `GET /complete_lesson/{lesson_id}`

Will mark the lesson complete and redirect the user to the next lesson.

Example:

```html
<form action="/complete_lesson/{{ id }}" method="GET">
    <button type="submit">Complete this lesson and continue</button>
</form>
```

