<?php

namespace Tests\Feature\Http\Controllers\Api;

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

        /**
     * @test
     */
    public function non_authenticated_users_cannot_access_the_following_endpoints_for_the_products_api(){

        $index = $this->json('GET','/api/products/');
        $index->assertStatus(401);

        $store = $this->json('POST','/api/products/');
        $store->assertStatus(401);

        $show = $this->json('GET','/api/products/show/1');
        $show->assertStatus(401);

        $update = $this->json('PUT','/api/products/update/1');
        $update->assertStatus(401);

        $destroy = $this->json('DELETE','/api/products/delete/1');
        $destroy->assertStatus(401);

    }

    /**
     * @test
     */
    public function can_return_a_collection_of_paginated_products(){

        $product0 = $this->create('Models\\Product\\Product');
        $product1 = $this->create('Models\\Product\\Product');
        $product2 = $this->create('Models\\Product\\Product');

        $response = $this->actingAs($this->create('User', [], false),'api')
        ->json('GET','/api/products/');

        $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'data'=>[
                '*' => ['id','name','slug','price','created_at']
            ],
            'links'=> ['first','last','prev','next'],
            'meta' => ['current_page', 'last_page', 'from', 'to' ,'path', 'per_page','total']
        ]);

        \Log::info($response->getContent());
    }

    /**
     * @test
     */
    public function can_create_a_product(){

        $faker = Factory::create();

        $response = $this->actingAs($this->create('User', [], false),'api')->json('POST','/api/products',[
            'name' => $name = $faker->company,
            'slug' => str_slug($name),
            'price' => $price = random_int(10,100),
        ]);

        \Log::info(1,[$response->getContent()]);

        $response
        ->assertJsonStructure([
            'id','name','slug','price','created_at'
        ])->assertJson([
            'name' => $name,
            'slug' => str_slug($name),
            'price' => $price,
        ])
        ->assertStatus(201);

        $this->assertDatabaseHas('products',[
            'name'=> $name,
            'slug'=> str_slug($name),
            'price' => $price,
        ]);
    }

    /**
     * @test
     */
    public function will_fail_with_a_404_if_products_not_found(){
        $response = $this->actingAs($this->create('User', [], false),'api')->json('GET','/api/products/show/-1');
        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_return_a_product(){
        //Given
        $product = $this->create('Models\\Product\\Product');
        //When
        $response = $this->actingAs($this->create('User', [], false),'api')->json('GET',"/api/products/show/$product->id");
        //Then
        $response
        ->assertExactJson([
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
            'created_at' => (string)$product->created_at
        ])
        ->assertStatus(200);
    }

    /**
     * @test
     */
    public function will_fail_with_a_404_if_product_we_want_to_update_is_not_found(){

        $response = $this->actingAs($this->create('User', [], false),'api')->json('PUT','/api/products/update/-1');
        $response->assertStatus(404);

    }

    /**
     * @test
     */
    public function can_update_a_product(){

        $product = $this->create('Models\\Product\\Product');

        $response = $this->actingAs($this->create('User', [], false),'api')->json('PUT', "/api/products/update/$product->id",[
            'name' => 'Nombre de la persona',
            'slug' => 'Nombre de mi perosns',
            'price' => $product->price + 10,
        ]);

        $response
        ->assertExactJson([
            'id' =>  $product->id,
            'name' => 'Nombre de la persona',
            'slug' => 'Nombre de mi perosns',
            'price' => $product->price + 10,
            'created_at' => (string) $product->created_at
        ])
        ->assertStatus(200);

        $this->assertDatabaseHas('products',[
            'id' =>  $product->id,
            'name' => 'Nombre de la persona',
            'slug' => 'Nombre de mi perosns',
            'price' => $product->price + 10,
            'created_at' => (string) $product->created_at
        ]);
    }

      /**
     * @test
     */
    public function will_fail_with_a_404_if_product_we_want_to_delete_is_not_found(){

        $response = $this->actingAs($this->create('User', [], false),'api')->json('DELETE','/api/products/delete/-1');
        $response->assertStatus(404);

    }

    /**
     * @test
     */
    public function can_delete_a_product(){

        $product = $this->create('Models\\Product\\Product');

        $response = $this->actingAs($this->create('User', [], false),'api')->json('DELETE', "/api/products/delete/$product->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products',['id'=> $product->id]);
    }


}
