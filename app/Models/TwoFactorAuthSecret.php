<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class TwoFactorAuthSecret
 * @package App\Models
 * @property string $secret
 * @property string $status
 * @property integer $user_id
 */
class TwoFactorAuthSecret extends Eloquent
{
    const enabled = 'enabled';
    const disabled = 'disabled';

    protected $fillable = [
        'status', 'user_id', 'secret'
    ];

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(){
        return $this->belongsTo(\App\User::class);
    }
}