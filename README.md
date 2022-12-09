## WALRUS

Walrus is my custom MVC framework that is used for building microservices with PHP. Unlike a traditional MVC framework, Walrus only contains the model and the controller (as the view will be the seperated frontend that is connected to the Walrus microservices through some kind of API gateway).

After cloning this repo you will find a basic demo microservice for saving pets and animals (and mapping a pet to an animal).
All of the framework logic is found in the app/src directory.

You should be able to get started using Walrus fairly quickly after understanding the 4 components explained below (containers, routing, controllers & models).

### Containers

Walrus uses docker to containerise each aspect of the microservice.
There are 3 containers:
* nginx-container
* php81-container
* mysql8-container

To start a walrus app just run the following docker command:
```
docker compose up -d --build
```

To enter a bash terminal in a container:
```
docker exec -it <container name> bash
```

### Routing

To create a new endpoint and map it to a controller method, just add a new route to the array passed into the Router contructor on line 8 of app/index.php.
Each route expects an array containing the controller class as the first parameter and the name of the method as the second.
E.g. 
```
new Router([
	'/animal/save' => [AnimalController::class, 'save'],
	'/animal/get' => [AnimalController::class, 'get'],
	'/pet/save' => [PetController::class, 'save'],
	'/pet/get' => [PetController::class, 'get'],
	'/animal/pets' => [AnimalController::class, 'pets'],
]);
```

The arguments on the specified method are used as required paremeters for the endpoint.
E.g.
A route mapped to this method will expect a request parmeter with a key of id (if this is not found then an error will be thrown as a response).
```
public function get($id)
{
	$animal = new Animal($id);
	return $this->return($animal->columns);
}
```

### Controllers

Unlike other MVC frameworks, the controller is where the application logic is written.
However the controller should not communicate with the database directly but instead through the correct model.
Controllers inherit from the Response class and when sending a resposne the return method can be called.
E.g.
```
return $this->return($animal->columns);
```

### Models

Each database table should have its own model class.
To setup a modle all you need to do is create a class that inherited from Model and set the protected property $table to the name of the table.
E.g.
```
class Pet extends Model
{
	protected $table = "pet";
}
```

The model will automatically detect all the table columns and generate a method for each one where the value can be changed.
After calling column methods, make sure to call the save method to save the changes.
E.g. changing a tables values from the controller:
```
public function save($name, $family)
{
	$animal = new Animal();
	$animal->name($name)->family($family)->save();
	return $this->return("Animal saved successfully!");
}
```

For more complicated queries, you can create a custom method in the model class which calls Model::query().
The query method just expects raw SQL with a few changes:
* When refering to the model's table in the query you can just use the @table placeholder.
* When passing dynamic values to a query just use a PDO placeholder (with a : appended on the front e.g. :name). Then make sure to include this placeholder with the value (as a key value pair) in the array passed as the second parameter.

E.g.
```
public function pets($name)
{
	return $this->query("SELECT * FROM `pet`
		LEFT JOIN @table ON `pet`.`animal_id` = @table.`id`
		WHERE @table.`name` = :name", [
			":name" => $name,
		]);
}
```

## Contributing

If you have any issues/suggestions regarding the framework, please open an issue where it can be discussed further.