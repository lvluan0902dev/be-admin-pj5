<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('product_category_id');
            $table->integer('product_brand_id');
            $table->string('name');
            $table->string('option_name');
            $table->integer('option_price');
            $table->integer('option_stock');
            $table->string('url');
            $table->text('short_description');
            $table->text('product_detail');
            $table->text('how_to_use');
            $table->text('ingredients');
            $table->string('image_name');
            $table->string('image_path');
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('order_count')->default(0);
            $table->tinyInteger('status')->comment('0: Inactive, 1: Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
