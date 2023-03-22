# Tebex Tech Test

### Author: Abraham Bossey

# Documentation of my Journey and process::

-   I cloned the repo.
-   `run composer update`
-   `run composer install`

## Step 1:

-   i cloned the .env.example file and create a new .env file.
-   then Setup the laravel Encryption key:
    -   `php artisan key:generate`
-   Cleared any cached configuration.
    -   `php artisan config:cache`

## step 2: Service Generators

-   installed a service generator that adds a `php artisan make:service {serviceName}` cmd
    and allow us create services.

## Step 3: Modifications to the phpunit file

-   i uncommented the `<server name="DB_CONNECTION" value="sqlite"/> && <server name="DB_DATABASE" value=":memory:"/>` to
    allow in-memory SQLite database for my tests since RefreshDatabase was used during the test.

## Step 4: Tests

-   Unit test:
-   using the code below as a case study:

```php
    public function testLookupWithValidMinecraftUsername(){
        // some code here...
    }
```

-   first i created a mock object of the `getMockBuilder`.
-   i setup an expectation on the mock object, i nulled the userId since it wasn't used in this case.
-   then i specified the value that should be returned by the method when it is called with the arguments passed.
-   i created a new instance of the controller so i can access the lookup function. i also injected the services.
-   then i ran the test. Using the asertEquals i was able to compare the response.
-   i used `Cache::flush();` in my setup to flush any existing cache before callling my tests.

*   Feature Test:
*   using the code below as a case study:

```php
    public function testMinecraftTestingForUserIdAndUsernameInLookupController(){
        // some code here...
    }
```

-   First i set the default data given to the parameters then i passed it as a payload to the endpoint.
-   Then i checked for a 200 response using `$response->assertOk()`
-   I also `response->assertJson([])` to ensure the response contains all the right values.

# NOTE:

-   Service Containers was splited to prepare it for scalability. and also to help organize my code.

-   I added a constructor method to the Service containers and the controller to perform Dependency Injection. Instead of creating a new Client object, This makes the class depend on the Client object, which makes the class more flexible and easier to test.

-   For caching, i used laravel's Facade ```Illuminate\Support\Facades\Cache;```. In this case, in the controller, i checked to see if data is already cached using the `Cache::has()`, If the data is found in the cache, i then retrieve it using `Cache::get()` and then returned response. if the data was not found in the cache, i then ran the switch statement and then stored the result in the cache using the `Cache::put()` with an expiry time of 5 minutes. This ensures that the multiple api calls does not exceed the rate limit and also improve the performance of the application.

-   One major flaw i observed was there was no input validation. for my defensive programming skills, i ensured there is an input validation for the lookup function in the controller. and also i used the `try{}catch(){}` block to check any validation errors that might occur. Lastly, i catched any general error that might occur with the ```\Exception``` method.

-   Error/fail is communicated back to the user in the event any aspect of the codes failed. With the ```\Exception``` facade. and also using the proper status code.

## Example Requests and expected Results:

(Note: This assumes the code is running on http://localhost:8000) - e.g. having been started using the built-in php server:

`php -S localhost:8000 -t public`

http://localhost:8000/lookup?type=xbl&username=tebex

```json
{
    "username": "Tebex",
    "id": "2533274844413377",
    "avatar": "https://avatar-ssl.xboxlive.com/avatar/2533274844413377/avatarpic-l.png"
}
```

http://localhost:8000/lookup?type=xbl&id=2533274884045330

```json
{
    "username": "d34dmanwalkin",
    "id": "2533274884045330",
    "avatar": "https://avatar-ssl.xboxlive.com/avatar/2533274884045330/avatarpic-l.png"
}
```

http://localhost:8000/lookup?type=steam&username=test
Should return an error "Steam only supports IDs"

http://localhost:8000/lookup?type=steam&id=76561198806141009

```json
{
    "username": "Tebex",
    "id": "76561198806141009",
    "avatar": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/c8/c86f94b0515600e8f6ff869d13394e05cfa0cd6a.jpg"
}
```

http://localhost:8000/lookup?type=minecraft&id=d8d5a9237b2043d8883b1150148d6955

```json
{
    "username": "Test",
    "id": "d8d5a9237b2043d8883b1150148d6955",
    "avatar": "https://crafatar.com/avatarsd8d5a9237b2043d8883b1150148d6955"
}
```

http://localhost:8000/lookup?type=minecraft&username=Notch

```json
{
    "username": "Notch",
    "id": "069a79f444e94726a5befca90e38aaf5",
    "avatar": "https://crafatar.com/avatars069a79f444e94726a5befca90e38aaf5"
}
```
