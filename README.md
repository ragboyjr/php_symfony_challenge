# SG PHP Engineering Challenge

This is a small engineering challenge that will test the skill and experience of an engineer's ability to write quality features and unit tests for a common engineering task of taking data from one place and storing it another place.

We strive to build quality software that is well tested and thoroughly documented. We'll be reviewing the submissions of this challenge with the following set of criteria:

- Code Design
  - Defensive coding principles
  - Are we handling edge cases?
  - Is the code self-documenting where possible?
  - Domain Modeling & Layered Application Structure
- Feature Completeness
  - Did you read the instructions carefully and properly implement the feature according to the specification
- Quality Tests
  - Do our tests cover the functionality of this feature
  - Are the tests built robustly and can withstand refactorings
  - Are the tests mapping to our features or to our code structure?
- Documentation
  - Do we have feature documentation?
  - Is the documentation organized and written in a readable manner?
  - Are any non-obvious pieces of our code documented?

## The Challenge

SG needs to build a new integration with one of our marketing partners to send over our product data so they can build affiliate feeds. 

The integration requires that we send over the style number, product name, price, and product images for every product. Anytime there are changes to any of these fields for a product, we'll need to send an update over the integration.

We'll need to integrate by sending a CSV file of the product information and placing it on an SFTP server on an inbound folder.

It's important that we have visibility on the state of the product, when it's received changes from SG's end, when it's been successfully sent to the SFTP server.

### Acceptance Criteria

**Product Importing (Required)**

We need build and maintain a model for the Product and build a process that will import product data from JSON files. This will be how we simulate creating or updating products as one would from an admin, but instead of its from a data file import.

The import file will adhere to the following schema:

```json
{
	"definitions": {},
	"$schema": "http://json-schema.org/draft-07/schema#", 
	"$id": "https://example.com/object1609878110.json", 
	"title": "Products", 
	"type": "array",
	"default": [],
	"items":{
		"$id": "#root/items", 
		"title": "Product", 
		"type": "object",
		"required": [
			"styleNumber",
			"name",
			"price",
			"images"
		],
		"properties": {
			"styleNumber": {
				"$id": "#root/items/styleNumber", 
				"title": "StyleNumber", 
				"type": "string",
				"default": "",
				"examples": [
					"ABC|123"
				],
				"pattern": "^.*$"
			},
			"name": {
				"$id": "#root/items/name", 
				"title": "Name", 
				"type": "string",
				"default": "",
				"examples": [
					"T-Shirt"
				],
				"pattern": "^.*$"
			},
			"price": {
				"$id": "#root/items/price", 
				"title": "Price", 
				"type": "object",
				"required": [
					"amount",
					"currency"
				],
				"properties": {
					"amount": {
						"$id": "#root/items/price/amount", 
						"title": "Amount", 
						"type": "integer",
						"examples": [
							1500
						],
						"default": 0
					},
					"currency": {
						"$id": "#root/items/price/currency", 
						"title": "Currency", 
						"type": "string",
						"default": "",
						"examples": [
							"USD"
						],
						"pattern": "^.*$"
					}
				}
			}
,
			"images": {
				"$id": "#root/items/images", 
				"title": "Images", 
				"type": "array",
				"default": [],
				"items":{
					"$id": "#root/items/images/items", 
					"title": "Image URL", 
					"type": "string",
					"default": "",
					"examples": [
						"https://via.placeholder.com/400x300/4b0082?id=1"
					],
					"pattern": "^.*$"
				}
			}
		}
	}
}
```

Here's an example:

```json
[
  {"styleNumber": "ABC|123", "name": "T-Shirt", "price": {"amount": 1500, "currency": "USD"}, "images": ["https://via.placeholder.com/400x300/4b0082?id=1", "https://via.placeholder.com/400x300/4b0082?id=2"]}
]
```

- The style number of the product is the unique identifier
- This import will be an upsert process, if no product exists at the style number, we do an insert, otherwise, we modify the content and update.
- Products that are newly created or have received changed since their last update should be in a special state that represents that they need to be synced to the marketing partner integration.
- If a product is being updated, and there were no changes to the product, then the product should NOT go into a state that will sync to the marketing partner.
- Do not assume all prices will be in USD, but it's fine to allow your code to only handle USD and report errors otherwise.

**Pushing to Marketing Partner (Extra Credit)**

- Build a process that will find all products that need to sync to the marketing partner, build a CSV and then drop the CSV onto their SFTP server. All products involved will need to go into a state to ensure that they don't sync again unless they receive further changes from the import tool.
- For the sake of this challenge, we don't need to actually connect to an SFTP server, we can just build the file and export it to a path on the local filesystem.

The export CSV needs to contain the following fields:

```
"Product Id","Product Name","Price","Image 1","Image 2","Image 3","Image 4","Image 5","Image 6","Image 7","Image 8","Image 9"
ABC|123,"T-Shirt",$150,"https://via.placeholder.com/400x300/4b0082?id=1","https://via.placeholder.com/400x300/4b0082?id=2",,,,,,,
```

**Design for Scale (Required)**

For the sake of the challenge, assume the entire catalog is around 50k and that any import or push to the marketing partner could handle all 50k products.

This means that all operations need to be batched, and reading/writing of files needs to be streamed instead of managed all in memory at once.

For extra credit, you could see about parallelizing the import process so that we could have multiple workers managing the import process from a single file.

### Development Requirements/Notes

- You need to fill the Installation, Usage, and Testing sections of this README with all of the information necessary to allow us to quickly setup the library, run the script, and execute the test suite.
- The entire process needs to be covered in automated tests. The main point of this exercise is see the quality of the tests around this feature.
- The packages already included in composer.json should enable all of these features, but feel free to add or modify the required packages to fit your needs/preferences.
- The application layout structure in src folder is just the default provided from symfony when doing a clean install, if you are familiar with DDD principles, you are encouraged to modify the application layout structure to reflect a DDD layered architecture.

## Installation

This project requires `mysql` database with `admin` user and `admin` password to running in the system. If you want other database engine just modify .env file (`DATABASE_URL`)

`php-amqp` extension is required for using `amqp` transport for handling messenger bus. If you want to configure another transport just change `MESSENGER_TRANSPORT_DSN` env variable (check user, password and host, for docs visit `https://symfony.com/doc/current/messenger.html#messenger-transports-config`)
If you're on ubuntu run these for amqp:

```
sudo apt-get install php-amqp
sudo apt-get install -y rabbitmq-server 
sudo service apache2 restart
```

The sftp folder is `public/downloads/catalogs`, you can change the path at `config/services.yaml` parameter `ftp_dir`.

Clone the repository and access to folder created, run composer and create database, load migrations and fixtures 

```
git clone https://github.com/belenalmazan/php_symfony_challenge.git
cd php_symfony_challenge
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

## Usage

Start the built-in server, open messenger bus to consume async requests, go to admin dashboard and create a new Catalog

```
php bin/console server:start
php bin/console messenger:consume async -vv
```

- visit http://127.0.0.1:8000
- Go to Catalogs list Add/remove catalogs
- Add Catalog: this fires an event to messenger queue, to handle catalog file
- Reload Catalogs list page, check catalog is synced, and products are created (by count products table or you can go to Products list view and check it), and csv exported to a self contained file inside `ftp_dir`
- Remove catalog, check file is removed from `public/uploads/catalogs`, but products continue persisted 

## Testing

Can run testing to check all works well
Messenger bus must be opened (`php bin/console messenger:consume async -vv`)

```
php bin/phpunit
```

## Author

Maria Belen Almazan 05/02/2021