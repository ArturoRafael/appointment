<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Appointment
 * 
 * @property int $id
 * @property int $id_webinars
 * @property string $date_begin
 * @property string $emails
 * @property string $time
 * @property string $timezone
 * 
 * @property \App\Models\Genero $genero
 *
 * @package App\Models
 */
class Appointment extends Eloquent
{
	protected $table = 'appointment';
	
	protected $casts = [
		'id_webinars' => 'int'
	];

	protected $fillable = [
		'id_webinars',
		'date_begin',
		'emails',
		'time',
		'timezone',
		'reason',
		'organizer',
		'created_at',
		'updated_at'
	];

	public function webinar()
	{
		return $this->belongsTo(\App\Http\Models\Webinars::class, 'id_webinars');
	}
	
}
