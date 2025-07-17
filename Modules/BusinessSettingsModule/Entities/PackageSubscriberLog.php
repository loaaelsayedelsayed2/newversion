<?php

namespace Modules\BusinessSettingsModule\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\PaymentModule\Entities\PaymentRequest;
use Modules\ProviderManagement\Entities\Provider;

class PackageSubscriberLog extends Model
{
    use HasFactory;
    use HasUuid;

<<<<<<< HEAD
    //protected $fillable = [];
    protected $guarded = ['id'];
=======
    protected $fillable = [];
>>>>>>> newversion/main


    public function payment(): BelongsTo
    {
<<<<<<< HEAD
        return $this->belongsTo(PaymentRequest::class,'payment_id');
=======
        return $this->belongsTo(PaymentRequest::class,'payment_id', );
>>>>>>> newversion/main
    }
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class,'provider_id', );
    }
<<<<<<< HEAD
    public function packageSubscriber(): BelongsTo
    {
        return $this->belongsTo(PackageSubscriber::class, 'subscription_package_id');
    }
=======
>>>>>>> newversion/main

    protected static function newFactory()
    {
        return \Modules\BusinessSettingsModule\Database\factories\PackageSubscriberLogFactory::new();
    }
}
