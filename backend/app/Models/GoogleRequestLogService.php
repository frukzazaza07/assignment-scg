<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleRequestLogService extends Model
{
    use HasFactory;
    protected $table = 'google_map_log';
    protected $primaryKey = 'gml_id';
    public $incrementing = true;
    public $timestamps = false;
    const CREATED_AT = 'gml_created_at';
    const UPDATED_AT = 'gml_updated_at';
    protected $fillable = [
        'gml_user_ip',
        'gml_request_url',
        'gml_request_payload',
        'gml_google_key',
        'gml_google_url',
        'gml_google_request',
        'gml_request_payload',
        'gml_google_request_method',
        'gml_google_response',
    ];
}
