<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Site
 * @package App\Models
 * @property $domain
 * @property $identifier
 */
class Site extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sites';

    protected $fillable = [
        'domain',
        'company_name',
        'identifier'
    ];


    /** @var self */
    private static $singleton;

    public static function getInstance(): ?self
    {

        if (isset(self::$singleton) && !empty(self::$singleton)) {
            return self::$singleton;
        }

        /** @var self singleton */
        self::$singleton = self::query()->where('domain', '=', get_domain())->first();
        return self::$singleton;
    }

    public static function getInstanceOrFail(){

        $instance = self::getInstance();

        if (!$instance) {
            abort(404, "Site not found");
        }

        return $instance;
    }
}
