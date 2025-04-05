<?php

namespace Modules\CustomerModule\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ServiceManagement\Entities\RecentSearch;
use Modules\ServiceManagement\Entities\Service;
use Modules\UserManagement\Entities\User;
use Modules\ZoneManagement\Entities\Zone;

class UserBankCard extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'user_bankcards';

    protected $fillable = ['user_id','name','type',	'number','company','token','create_token_date','update_token_date'	];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
