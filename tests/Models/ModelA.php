<?php

namespace Oguzhankrcb\DataMigrator\Tests\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelA extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'data' => 'json',
    ];
}
