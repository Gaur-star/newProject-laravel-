<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListOfSites extends Model
{
    protected $table = "list_of_sites";

    protected $fillable = [
        	'site_name',		
            'site_user',		
            'site_pass',			
            'site_App_pass',		
            'site_status',
    ];
}
