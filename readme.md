# RESTish/HTTP API Boilerplate

__This is a Work-in-progress__

The idea here is to add a REST_Model on top of the excellent [philsturgeon/codeigniter-restserver](https://github.com/philsturgeon/codeigniter-restserver) 
and tie it together with a REST_Resource class, thus building a foundation for
auto-generating RESTish/HTTP APIs.

Currently the abstract class, REST_Resource, only supports GET, POST, PUT and DELETE 

USAGE:
  1. create a contoller that extends REST_Resource (v1, v2, ect...)
  2. create a model/ for the resource ([resource]_model.php) that extends REST_Model
  3. add 2 properties to your resource model
   - protected $_table = '';  //Database Table
   - protected $_id    = '';  //Database Table Primary Key

>This will give you a nice `/api/v1/resource url`

  _Try it out!_
 
Application Program Interface (for free):
```
 GET    /resource           return all resource records
 GET    /resource/count     return number of resource records
 GET    /resource/:id       return one resource record
 POST   /resource           create new resource record  
 POST   /resource/search    search all resource records
 PUT    /resource/:id       update existing resource record
 DELETE /resource/:id       delete existing resource record
```

> opeartions that have the word 'all' in them can also pass query parameters for
 - limit
 - offset
to manage paging.

### TODO
- Add Examples
- REST_Model should have an exceptable `_fields` array, to limit what is saved and returned
- REST_Model should provide the ability to exclude fields returned to the client, to prevent private data leaking (such as passwords)
- REST_Model should have the ability to delete records as a soft-delete
- REST_Model should have `_before` and `_after` hooks
- Lots more!  

