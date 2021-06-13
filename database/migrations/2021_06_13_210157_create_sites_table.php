<?php

use App\Models\Site;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('domain', 253)->unique()->index()->unique();
            $table->string('company_name',)->nullable()->index();
            $table->string('identifier', 255)->index()->unique()->comment('Unique identifier for code');
            $table->timestamps();
            $table->softDeletes();
        });


        if (app()->environment('local')) {
            Site::query()->create([
                'domain' => 'demo.localhost',
                'company_name' => 'Demo',
                'identifier' => 'demo'
            ]);

            Site::query()->create([
                'domain' => 'trenchdevs.localhost',
                'company_name' => 'Demo',
                'identifier' => 'trenchdevs'
            ]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sites');
    }
}
