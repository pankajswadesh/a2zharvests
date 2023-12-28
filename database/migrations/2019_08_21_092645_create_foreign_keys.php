<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('categories', function(Blueprint $table) {
			$table->foreign('parent_id')->references('id')->on('categories')
						->onDelete('restrict')
						->onUpdate('cascade');
		});
		Schema::table('products', function(Blueprint $table) {
			$table->foreign('category_id')->references('id')->on('categories')
						->onDelete('restrict')
						->onUpdate('cascade');
		});
		Schema::table('products', function(Blueprint $table) {
			$table->foreign('brand_id')->references('id')->on('brands')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('products', function(Blueprint $table) {
			$table->foreign('unit_id')->references('id')->on('units')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('products', function(Blueprint $table) {
			$table->foreign('department_id')->references('id')->on('departments')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('products', function(Blueprint $table) {
			$table->foreign('tax_id')->references('id')->on('taxs')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('product_images', function(Blueprint $table) {
			$table->foreign('product_id')->references('id')->on('products')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('supplier_products', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('cascade');
		});
		Schema::table('supplier_products', function(Blueprint $table) {
			$table->foreign('product_id')->references('id')->on('products')
						->onDelete('restrict')
						->onUpdate('cascade');
		});
		Schema::table('supplier_products', function(Blueprint $table) {
			$table->foreign('discount_id')->references('id')->on('discounts')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('tax_values', function(Blueprint $table) {
			$table->foreign('tax_id')->references('id')->on('taxs')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
	}

	public function down()
	{
		Schema::table('categories', function(Blueprint $table) {
			$table->dropForeign('categories_parent_id_foreign');
		});
		Schema::table('products', function(Blueprint $table) {
			$table->dropForeign('products_category_id_foreign');
		});
		Schema::table('products', function(Blueprint $table) {
			$table->dropForeign('products_brand_id_foreign');
		});
		Schema::table('products', function(Blueprint $table) {
			$table->dropForeign('products_unit_id_foreign');
		});
		Schema::table('products', function(Blueprint $table) {
			$table->dropForeign('products_department_id_foreign');
		});
		Schema::table('products', function(Blueprint $table) {
			$table->dropForeign('products_tax_id_foreign');
		});
		Schema::table('product_images', function(Blueprint $table) {
			$table->dropForeign('product_images_product_id_foreign');
		});
		Schema::table('supplier_products', function(Blueprint $table) {
			$table->dropForeign('supplier_products_user_id_foreign');
		});
		Schema::table('supplier_products', function(Blueprint $table) {
			$table->dropForeign('supplier_products_product_id_foreign');
		});
		Schema::table('supplier_products', function(Blueprint $table) {
			$table->dropForeign('supplier_products_discount_id_foreign');
		});
		Schema::table('tax_values', function(Blueprint $table) {
			$table->dropForeign('tax_values_tax_id_foreign');
		});
	}
}