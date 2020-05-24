<?php

use Illuminate\Support\Str;

function convertFileName($file)
{
    if ($file != NULL) {
        $file_info = pathinfo($file);
        if (isset($file_info['extension']))
            return $file_name = Str::slug($file_info['filename']) . '.' . $file_info['extension'];
        else
            return $file_name = Str::slug($file_info['filename']);
    } else {
        return null;
    }
}
