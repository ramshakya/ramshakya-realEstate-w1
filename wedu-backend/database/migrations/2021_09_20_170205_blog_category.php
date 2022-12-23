<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BlogCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('BlogCategory', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Name',50);
            $table->integer('ParentId')->default(0);
            $table->text('Alias')->nullable();
            $table->timestamps();
        });
        \Illuminate\Support\Facades\DB::table('BlogCategory')->insert(
            [[
                'Name' => 'Default',
                'Alias' => "",
            ]]
        );
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('BlogCategory');
    }
}
