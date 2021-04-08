<?php

namespace App\Policies;

use App\Course;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    public function create($user)
    {
        if (auth()->guard('admin')->check())
            return true;
        else if(auth()->guard('instructor')->check())
            return $user->is_verified;
        else return false;
    }

    public function modify($user, Course $course)
    {
        if (auth()->guard('admin')->check())
            return true;
        else if(auth()->guard('instructor')->check())
            return $course->instructors->contains($user);
        else return false;
    }

    public function assignInstitution()
    {
        if (auth()->guard('admin')->check())
            return true;
    }

    public function leaveCourse($user, Course $course)
    {
        if(auth()->guard('instructor')->check())
            return $course->instructors->contains($user);
    }

    public function enroll($user, Course $course)
    {
        if(auth()->guard('student')->check())
        {
            if (!$course->students->contains($user))
                return true;
        }
    }

    public function access($user, Course $course)
    {
        if(auth()->guard('student')->check())
            return $course->students->contains($user);
    }

    public function wishlist($user, Course $course)
    {
        if ($this->enroll($user, $course))
        {
            if (!($course->wishlists()->where('student_id', $user->id)->first()))
                return true;
        }
    }

    public function removeWishlist($user, Course $course)
    {
        if ($this->enroll($user, $course))
        {
            if ($course->wishlists()->where('student_id', $user->id)->first())
                return true;
        }
    }
}