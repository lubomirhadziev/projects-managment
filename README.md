# Projects managment system
The project simulate API and Consumer (working with API endpoints)

## Requirements
```
installed docker
installed docker-compose
php version >= 7.2.5
```

## Example request to API endpoints
```
use Insomnia Rest Client and import ./Insomnia-rest-client.json file
```

## 1. Install dependencies
```
composer install
```

## 2. Create and Run mysql database
```
docker-compose up -d
```

## 3. Configure API endpoint url
```
by default API endpoint is http://127.0.0.1:8001 if you need to change this change API_ENDPOINT_URL from .env
```

## 4. Start server on port 8001
```
symfony server:start
```
