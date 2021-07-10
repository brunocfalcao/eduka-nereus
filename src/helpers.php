<?php

function course_config(string $path)
{
    return config(config('eduka-nereus.course').'.'.$path);
}
