<?php

namespace Tests\Feature\Http\Controllers\Api;

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

        /**
     * @test
     */
    public function non_authenticated_users_cannot_access_the_following_endpoints_for_the_products_api()
    {
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
    public function can_return_a_collection_of_paginated_products()
    {
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
    public function will_fail_with_validation_errors_when_creating_a_product()
    {
        $product = $this->create('Models\\Product\\Product');

        $response = $this->actingAs($this->create('User',[],false),'api')->json('post', '/api/products/',[
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => 'dsd'
        ]);

        $response->assertStatus(422)
                ->assertExactJson([
                    'message'=> 'The given data was invalid.',
                    'errors' =>[
                        'name' => ['The name has already been taken.'],
                        'slug' => ['The slug has already been taken.'],
                        'price' => ['The price must be an integer.']
                    ]
                ]);
    }

    /**
     * @test
     */
    public function can_create_a_product()
    {
        $faker = Factory::create();

        $response = $this->actingAs($this->create('User', [], false),'api')->json('POST','/api/products',[
            'name' => $name = $faker->company,
            'slug' => str_slug($name),
            'price' => $price = random_int(10,100),
        ]);

        $response
        ->assertJsonStructure([
            'id','image_id','name','slug','price','created_at'
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
    public function can_create_a_product_with_image()
    {
        $faker = Factory::create();

        Storage::fake('public');
        $image = UploadedFile::fake()->image('image.jpg');

        $response = $this->actingAs($this->create('User', [], false),'api')->json('POST','/api/products',[
            'name' => $name = $faker->company,
            'slug' => str_slug($name),
            'price' => $price = random_int(10,100),
            'image' => $image
        ]);

        $response
        ->assertJsonStructure([
            'id','image_id','name','slug','price','created_at'
        ])->assertJson([
            'name' => $name,
            'slug' => str_slug($name),
            'price' => $price,
        ])
        ->assertStatus(201);

        Storage::disk('public')->assertExists("product_images/{$image->hashName()}");

        $this->assertDatabaseHas('products',[
            'name'=> $name,
            'slug'=> str_slug($name),
            'price' => $price,
        ]);
    }

    /**
     * @test
     */
    public function will_fail_with_a_404_if_products_not_found()
    {
        $response = $this->actingAs($this->create('User', [], false),'api')->json('GET','/api/products/show/-1');
        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_return_a_product()
    {
        //Given
        $product = $this->create('Models\\Product\\Product');
        //When
        $response = $this->actingAs($this->create('User', [], false),'api')->json('GET',"/api/products/show/$product->id");
        //Then
        $response
        ->assertExactJson([
            'id' => $product->id,
            'image_id' => $product->image_id,
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
    public function will_fail_with_a_404_if_product_we_want_to_update_is_not_found()
    {
        $response = $this->actingAs($this->create('User', [], false),'api')->json('PUT','/api/products/update/-1');
        $response->assertStatus(404);
    }
    /**
     * @test
     */
    public function will_fail_with_validation_errors_when_updating_a_product()
    {
        $product0 = $this->create('Models\\Product\\Product');
        $product1 = $this->create('Models\\Product\\Product');

        $response = $this->actingAs($this->create('User',[],false),'api')->json('PUT',"/api/products/update/$product1->id",[
            'name'=> $product1->name,
            'price' => 'dsds'
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function can_update_a_product()
    {
        $product = $this->create('Models\\Product\\Product');

        $response = $this->actingAs($this->create('User', [], false),'api')->json('PUT', "/api/products/update/$product->id",[
            'name' => 'Nombre de la persona',
            'slug' => str_slug('Nombre de la persona'),
            'price' => $product->price + 10,
        ]);

        $response
        ->assertExactJson([
            'id' =>  $product->id,
            'image_id' => null,
            'name' => 'Nombre de la persona',
            'slug' => str_slug('Nombre de la persona'),
            'price' => $product->price + 10,
            'created_at' => (string) $product->created_at
        ])
        ->assertStatus(200);

        $this->assertDatabaseHas('products',[
            'id' =>  $product->id,
            'image_id' => null,
            'name' => 'Nombre de la persona',
            'slug' => str_slug('Nombre de la persona'),
            'price' => $product->price + 10,
            'created_at' => (string) $product->created_at
        ]);
    }

        /**
     * @test
     */
    public function can_update_a_product_with_image()
    {
        $product = $this->create('Models\\Product\\Product');

        Storage::fake('public');
        $image = UploadedFile::fake()->image('image.jpg');

        $response = $this->actingAs($this->create('User', [], false),'api')->json('PUT', "/api/products/update/$product->id",[
            'name' => 'Nombre de la persona',
            'slug' => str_slug('Nombre de la persona'),
            'price' => $product->price + 10,
            'image'=> $image
        ]);

        $response
        ->assertExactJson([
            'id' =>  $product->id,
            'image_id' => json_decode($response->getContent())->image_id,
            'name' => 'Nombre de la persona',
            'slug' => str_slug('Nombre de la persona'),
            'price' => $product->price + 10,
            'created_at' => (string) $product->created_at
        ])
        ->assertStatus(200);

        Storage::disk('public')->assertExists("product_images/{$image->hashName()}");

        $this->assertDatabaseHas('products',[
            'id' =>  $product->id,
            'image_id' => json_decode($response->getContent())->image_id,
            'name' => 'Nombre de la persona',
            'slug' => str_slug('Nombre de la persona'),
            'price' => $product->price + 10,
            'created_at' => (string) $product->created_at
        ]);
    }

      /**
     * @test
     */
    public function will_fail_with_a_404_if_product_we_want_to_delete_is_not_found()
    {
        $response = $this->actingAs($this->create('User', [], false),'api')->json('DELETE','/api/products/delete/-1');
        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_delete_a_product()
    {
        $product = $this->create('Models\\Product\\Product');
        $response = $this->actingAs($this->create('User', [], false),'api')->json('DELETE', "/api/products/delete/$product->id");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('products',['id'=> $product->id]);
    }


}
