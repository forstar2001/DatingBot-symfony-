<?php


namespace App\Models;


use Reliese\Database\Eloquent\Model;

/**
 * Class SourceBotProfileStatuses
 * @package App\Models
 *
 * @property int $id
 * @property string $status
 */
class SourceBotProfileStatuses extends Model
{
    const statusActive = 'Active';
    const statusPaused = 'Paused';
    const statusPending = 'Pending';
    const statusBroken = 'Broken';

    public function sourcesBotProfiles()
    {
        return $this->hasMany(SourcesBotProfiles::class);
    }
}