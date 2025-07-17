<?php

namespace Modules\BusinessSettingsModule\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PackageSubscriberLimit extends Model
{
    use HasFactory;
    use HasUuid;

<<<<<<< HEAD
    protected $guarded = ['id'];
=======
    protected $fillable = [];
>>>>>>> newversion/main

    protected static function newFactory()
    {
        return \Modules\BusinessSettingsModule\Database\factories\PackageSubscriberLimitFactory::new();
    }
}
