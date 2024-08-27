<?php

namespace App\Enums;

enum DegreeEnum : string
{
    case ILLITERATE = 'بدون مدرک';
    case DIPLOMA = 'دیپلم';
    case ASSOCIATES = 'فوق دیپلم';
    case BACHELOR = 'کارشناسی';
    case MASTER = 'کارشناسی ارشد';
    case DOCTORAL = 'دکترا';
    case OTHER = 'غیره';
}
