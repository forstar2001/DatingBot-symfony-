<?php


namespace App\Models;


use Reliese\Database\Eloquent\Model;

/**
 * Class SourcesBotProfiles
 * @package App\Models
 * @property integer $id
 * @property integer $source_id
 * @property integer $bot_profile_id
 * @property integer $status_id
 * @property string $link
 * @property \DateTime $last_check_date
 */
class SourcesBotProfiles extends Model
{
    public $timestamps = false;

    public function status()
    {
        return $this->belongsTo(SourceBotProfileStatuses::class);
    }

    public function botProfile()
    {
        return $this->belongsTo(BotProfile::class);
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}