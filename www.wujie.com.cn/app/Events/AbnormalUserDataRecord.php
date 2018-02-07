<?php namespace App\Events;

use Illuminate\Queue\SerializesModels;

class AbnormalUserDataRecord extends Event
{
    use SerializesModels;
}