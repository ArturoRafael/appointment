<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Webinars
 * 
 * @property int $id
 * @property string $description
 *
 * @property \Illuminate\Database\Eloquent\Collection $appointments
 *
 * @package App\Models
 */
class Webinars extends Eloquent
{
	protected $table = 'webinars';
	public $timestamps = false;

	protected $fillable = [
		'description'		
	];

	public function appointments()
	{
		return $this->hasMany(\App\Http\Models\Appointment::class, 'id_webinars');
	}
}
