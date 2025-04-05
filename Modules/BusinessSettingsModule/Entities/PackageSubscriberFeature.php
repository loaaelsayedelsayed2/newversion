<?php

namespace Modules\BusinessSettingsModule\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageSubscriberFeature extends Model
{
    use HasFactory;

    protected $fillable = ['provider_id','package_subscriber_log_id','feature'];
    //protected $guarded = ['id'];

    use HasUuid;

    protected static function newFactory()
    {
        return \Modules\BusinessSettingsModule\Database\factories\PackageSubscriberFeatureFactory::new();
    }
}
