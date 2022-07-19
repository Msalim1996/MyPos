---
title: API Reference

language_tabs:
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.
[Get Postman Collection](http://localhost/docs/collection.json)

<!-- END_INFO -->

#Item CRUD


<!-- START_67fe09171dc7eeb285ae2e11d6b66460 -->
## Restore

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL("http://localhost/api/items/1/restore");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": {
        "id": null,
        "name": null,
        "sku": null,
        "price": null,
        "type": null,
        "category": null,
        "description": null,
        "uom": null,
        "image": null,
        "deleted_at": null,
        "created_at": "",
        "updated_at": ""
    }
}
```

### HTTP Request
`GET api/items/{id}/restore`


<!-- END_67fe09171dc7eeb285ae2e11d6b66460 -->

<!-- START_2d89b427b331f35cdded42a87b6e4acc -->
## GET all

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL("http://localhost/api/items");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": [
        {
            "id": 1,
            "name": "Test Item qty",
            "sku": null,
            "price": "100.00",
            "type": null,
            "category": null,
            "description": null,
            "uom": null,
            "image": null,
            "deleted_at": null,
            "created_at": "2019-09-05 22:52:23",
            "updated_at": "2019-09-05 22:52:23"
        },
        {
            "id": 2,
            "name": "Test Item qty",
            "sku": null,
            "price": "100.00",
            "type": null,
            "category": null,
            "description": null,
            "uom": null,
            "image": null,
            "deleted_at": null,
            "created_at": "2019-09-05 22:53:37",
            "updated_at": "2019-09-05 22:53:37"
        }
    ]
}
```

### HTTP Request
`GET api/items`


<!-- END_2d89b427b331f35cdded42a87b6e4acc -->

<!-- START_07fb85e5d8610027392f9f49c33a97c1 -->
## POST (New Item)

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
Item receive several data such as item and stocks information

bodyParam:
{
  item: {
    name: "value",
    sku: "value",
    price: 0,
    type: "value",
    category: "value",
    description: "value",
    uom: "value",
    image: "value:,
  },
  stocks: [
    {
      location_id: 1,
      quantity: 100,
    }
  ]
}

> Example request:

```javascript
const url = new URL("http://localhost/api/items");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/items`


<!-- END_07fb85e5d8610027392f9f49c33a97c1 -->

<!-- START_1f8988f8b514fb2127ba9ed8e2499f98 -->
## GET

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL("http://localhost/api/items/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": {
        "id": 1,
        "name": "Test Item qty",
        "sku": null,
        "price": "100.00",
        "type": null,
        "category": null,
        "description": null,
        "uom": null,
        "image": null,
        "deleted_at": null,
        "created_at": "2019-09-05 22:52:23",
        "updated_at": "2019-09-05 22:52:23"
    }
}
```

### HTTP Request
`GET api/items/{item}`


<!-- END_1f8988f8b514fb2127ba9ed8e2499f98 -->

<!-- START_5720c5ba9db8be8b03e436d5f2db2bf1 -->
## PUT

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL("http://localhost/api/items/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/items/{item}`

`PATCH api/items/{item}`


<!-- END_5720c5ba9db8be8b03e436d5f2db2bf1 -->

<!-- START_4ba7e871e55098b0081507ac0b4e478b -->
## DELETE

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL("http://localhost/api/items/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/items/{item}`


<!-- END_4ba7e871e55098b0081507ac0b4e478b -->

#Location CRUD


<!-- START_fcffe09fd436bb24ba38cb5c297dd584 -->
## Restore

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL("http://localhost/api/locations/1/restore");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": {
        "id": null,
        "name": null,
        "deleted_at": null,
        "created_at": "",
        "updated_at": ""
    }
}
```

### HTTP Request
`GET api/locations/{id}/restore`


<!-- END_fcffe09fd436bb24ba38cb5c297dd584 -->

<!-- START_7fb4739b1e26173b78c06ed910857f37 -->
## GET all

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL("http://localhost/api/locations");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": [
        {
            "id": 1,
            "name": "Location1",
            "deleted_at": null,
            "created_at": "2019-09-05 22:52:01",
            "updated_at": "2019-09-05 22:52:01"
        },
        {
            "id": 2,
            "name": "Location2",
            "deleted_at": null,
            "created_at": "2019-09-05 22:52:09",
            "updated_at": "2019-09-05 22:52:09"
        }
    ]
}
```

### HTTP Request
`GET api/locations`


<!-- END_7fb4739b1e26173b78c06ed910857f37 -->

<!-- START_6ac6759cab929b9077bddc6d56416b5c -->
## POST

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL("http://localhost/api/locations");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/locations`


<!-- END_6ac6759cab929b9077bddc6d56416b5c -->

<!-- START_f71771a70af5f8dad2212b1b5a2258d5 -->
## GET

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL("http://localhost/api/locations/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": {
        "id": 1,
        "name": "Location1",
        "deleted_at": null,
        "created_at": "2019-09-05 22:52:01",
        "updated_at": "2019-09-05 22:52:01"
    }
}
```

### HTTP Request
`GET api/locations/{location}`


<!-- END_f71771a70af5f8dad2212b1b5a2258d5 -->

<!-- START_ddb58ef8759801169efb409d19aa45da -->
## PUT

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL("http://localhost/api/locations/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/locations/{location}`

`PATCH api/locations/{location}`


<!-- END_ddb58ef8759801169efb409d19aa45da -->

<!-- START_fa28b5e8dd2e79a38ee29df19a80f037 -->
## DELETE

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL("http://localhost/api/locations/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/locations/{location}`


<!-- END_fa28b5e8dd2e79a38ee29df19a80f037 -->

#Spatie Permission API


<!-- START_26b8f26974ea8512ea46f4199c9b9844 -->
## Get current authenticated user information

> Example request:

```javascript
const url = new URL("http://localhost/api/current-user");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": {
        "id": 1,
        "name": "IT support admin",
        "status": null,
        "username": "itadmin",
        "remark": "Default user for IT Support",
        "phone": null,
        "email": null
    }
}
```

### HTTP Request
`GET api/current-user`


<!-- END_26b8f26974ea8512ea46f4199c9b9844 -->

<!-- START_ee24cf7e1c711ca2cf169d36b47b5eb8 -->
## get current authenticated user role

> Example request:

```javascript
const url = new URL("http://localhost/api/current-user/role");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": [
        "Super admin"
    ]
}
```

### HTTP Request
`GET api/current-user/role`


<!-- END_ee24cf7e1c711ca2cf169d36b47b5eb8 -->

<!-- START_7b382da9c6168e578adfc58bb2e83796 -->
## get current authenticated user permission

> Example request:

```javascript
const url = new URL("http://localhost/api/current-user/permission");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": [
        {
            "id": 1,
            "name": "Manage user",
            "guard_name": "web",
            "created_at": "2019-09-05 22:41:31",
            "updated_at": "2019-09-05 22:41:31",
            "pivot": {
                "role_id": 1,
                "permission_id": 1
            }
        },
        {
            "id": 2,
            "name": "Manage inventory",
            "guard_name": "web",
            "created_at": "2019-09-05 22:41:32",
            "updated_at": "2019-09-05 22:41:32",
            "pivot": {
                "role_id": 1,
                "permission_id": 2
            }
        },
        {
            "id": 3,
            "name": "Manage customer",
            "guard_name": "web",
            "created_at": "2019-09-05 22:41:32",
            "updated_at": "2019-09-05 22:41:32",
            "pivot": {
                "role_id": 1,
                "permission_id": 3
            }
        },
        {
            "id": 4,
            "name": "Manage sales",
            "guard_name": "web",
            "created_at": "2019-09-05 22:41:32",
            "updated_at": "2019-09-05 22:41:32",
            "pivot": {
                "role_id": 1,
                "permission_id": 4
            }
        },
        {
            "id": 5,
            "name": "Manage store",
            "guard_name": "web",
            "created_at": "2019-09-05 22:41:32",
            "updated_at": "2019-09-05 22:41:32",
            "pivot": {
                "role_id": 1,
                "permission_id": 5
            }
        }
    ]
}
```

### HTTP Request
`GET api/current-user/permission`


<!-- END_7b382da9c6168e578adfc58bb2e83796 -->

<!-- START_6470e6b987921f5c45bf7a2d8e674f57 -->
## Display a listing of the resource.

> Example request:

```javascript
const url = new URL("http://localhost/api/roles");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET api/roles`


<!-- END_6470e6b987921f5c45bf7a2d8e674f57 -->

<!-- START_90c780acaefab9740431579512d07101 -->
## Store a newly created resource in storage.

> Example request:

```javascript
const url = new URL("http://localhost/api/roles");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/roles`


<!-- END_90c780acaefab9740431579512d07101 -->

<!-- START_eb37fe1fa9305b4b78850dd87031670b -->
## Display the specified resource.

> Example request:

```javascript
const url = new URL("http://localhost/api/roles/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET api/roles/{role}`


<!-- END_eb37fe1fa9305b4b78850dd87031670b -->

<!-- START_cccebfff0074c9c5f499e215eee84e86 -->
## Update the specified resource in storage.

> Example request:

```javascript
const url = new URL("http://localhost/api/roles/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/roles/{role}`

`PATCH api/roles/{role}`


<!-- END_cccebfff0074c9c5f499e215eee84e86 -->

<!-- START_9aab750214722ffceebef64f24a2e175 -->
## Remove the specified resource from storage.

> Example request:

```javascript
const url = new URL("http://localhost/api/roles/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/roles/{role}`


<!-- END_9aab750214722ffceebef64f24a2e175 -->

<!-- START_fc1e4f6a697e3c48257de845299b71d5 -->
## Get all user information

> Example request:

```javascript
const url = new URL("http://localhost/api/users");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": [
        {
            "id": 1,
            "name": "IT support admin",
            "status": null,
            "username": "itadmin",
            "remark": "Default user for IT Support",
            "phone": null,
            "email": null
        }
    ]
}
```

### HTTP Request
`GET api/users`


<!-- END_fc1e4f6a697e3c48257de845299b71d5 -->

<!-- START_12e37982cc5398c7100e59625ebb5514 -->
## Add new User

> Example request:

```javascript
const url = new URL("http://localhost/api/users");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/users`


<!-- END_12e37982cc5398c7100e59625ebb5514 -->

<!-- START_8653614346cb0e3d444d164579a0a0a2 -->
## Display the specified resource.

> Example request:

```javascript
const url = new URL("http://localhost/api/users/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": {
        "id": 1,
        "name": "IT support admin",
        "status": null,
        "username": "itadmin",
        "remark": "Default user for IT Support",
        "phone": null,
        "email": null
    }
}
```

### HTTP Request
`GET api/users/{user}`


<!-- END_8653614346cb0e3d444d164579a0a0a2 -->

<!-- START_48a3115be98493a3c866eb0e23347262 -->
## Update User

> Example request:

```javascript
const url = new URL("http://localhost/api/users/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/users/{user}`

`PATCH api/users/{user}`


<!-- END_48a3115be98493a3c866eb0e23347262 -->

<!-- START_d2db7a9fe3abd141d5adbc367a88e969 -->
## Remove the specified resource from storage.

> Example request:

```javascript
const url = new URL("http://localhost/api/users/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/users/{user}`


<!-- END_d2db7a9fe3abd141d5adbc367a88e969 -->

<!-- START_42db014707f615cd5cafb5ad1b6d0675 -->
## Display a listing of the resource.

> Example request:

```javascript
const url = new URL("http://localhost/api/permissions");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET api/permissions`


<!-- END_42db014707f615cd5cafb5ad1b6d0675 -->

<!-- START_d513e82f79d47649a14d2e59fda93073 -->
## Store a newly created resource in storage.

> Example request:

```javascript
const url = new URL("http://localhost/api/permissions");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/permissions`


<!-- END_d513e82f79d47649a14d2e59fda93073 -->

<!-- START_29ec1a9c6f20445dcd75bf6a4cc63e2a -->
## Display the specified resource.

> Example request:

```javascript
const url = new URL("http://localhost/api/permissions/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET api/permissions/{permission}`


<!-- END_29ec1a9c6f20445dcd75bf6a4cc63e2a -->

<!-- START_cbdd1fce06181b5d5d8d0f3ae85ed0ee -->
## Update the specified resource in storage.

> Example request:

```javascript
const url = new URL("http://localhost/api/permissions/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/permissions/{permission}`

`PATCH api/permissions/{permission}`


<!-- END_cbdd1fce06181b5d5d8d0f3ae85ed0ee -->

<!-- START_58309983000c47ce901812498144165a -->
## Remove the specified resource from storage.

> Example request:

```javascript
const url = new URL("http://localhost/api/permissions/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/permissions/{permission}`


<!-- END_58309983000c47ce901812498144165a -->

<!-- START_4a38658ff958e7684755414e61cab1db -->
## api/model-has-role
> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-role");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET api/model-has-role`


<!-- END_4a38658ff958e7684755414e61cab1db -->

<!-- START_e42e86ee76ad63351a07e171d801397c -->
## Display the specified resource.

> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-role/1/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET api/model-has-role/{modelid}/{roleid}`


<!-- END_e42e86ee76ad63351a07e171d801397c -->

<!-- START_3a22bfd54635988aaa72cc7ce9de3d83 -->
## Display the specified resource.

> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-role/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET api/model-has-role/{modelid}`


<!-- END_3a22bfd54635988aaa72cc7ce9de3d83 -->

<!-- START_196b123e19f232cb1704cae374254aef -->
## api/model-has-role
> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-role");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/model-has-role`


<!-- END_196b123e19f232cb1704cae374254aef -->

<!-- START_81b37775ef2e778173ec92f1ed8e53eb -->
## api/model-has-role/{modelid}
> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-role/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/model-has-role/{modelid}`


<!-- END_81b37775ef2e778173ec92f1ed8e53eb -->

<!-- START_a0ddec80d620d3e3f3b01dca2f0a3e9e -->
## api/model-has-role/{modelid}
> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-role/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/model-has-role/{modelid}`


<!-- END_a0ddec80d620d3e3f3b01dca2f0a3e9e -->

<!-- START_db76340a0d8f6cf82709f740853f591c -->
## api/model-has-role/{modelid}/{roleid}
> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-role/1/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/model-has-role/{modelid}/{roleid}`


<!-- END_db76340a0d8f6cf82709f740853f591c -->

<!-- START_042c06c983b613d040015ef356e4e64d -->
## api/model-has-role/{modelid}
> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-role/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/model-has-role/{modelid}`


<!-- END_042c06c983b613d040015ef356e4e64d -->

<!-- START_92a160d32d603573d5f9eec1a42e674f -->
## api/model-has-role-multiple/{modelid}
> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-role-multiple/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/model-has-role-multiple/{modelid}`


<!-- END_92a160d32d603573d5f9eec1a42e674f -->

<!-- START_53317fe6859b0aac7272aba26f5f29bc -->
## api/model-has-permission
> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-permission");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET api/model-has-permission`


<!-- END_53317fe6859b0aac7272aba26f5f29bc -->

<!-- START_0e87f318294f243cce89e66160c0dbdc -->
## Display the specified resource.

> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-permission/1/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET api/model-has-permission/{modelid}/{permissionid}`


<!-- END_0e87f318294f243cce89e66160c0dbdc -->

<!-- START_fde4d2057d74b7d04ab9655e270bce5a -->
## api/model-has-permission
> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-permission");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/model-has-permission`


<!-- END_fde4d2057d74b7d04ab9655e270bce5a -->

<!-- START_b1b22bc679f2361a270f6add01247fe6 -->
## api/model-has-permission/{modelid}
> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-permission/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/model-has-permission/{modelid}`


<!-- END_b1b22bc679f2361a270f6add01247fe6 -->

<!-- START_87ede813de05ac5cedd108938b9ca48a -->
## api/model-has-permission/{modelid}
> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-permission/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/model-has-permission/{modelid}`


<!-- END_87ede813de05ac5cedd108938b9ca48a -->

<!-- START_99ba298a1edb286674ee68bd5ac4ea3f -->
## api/model-has-permission/{modelid}/{permissionid}
> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-permission/1/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/model-has-permission/{modelid}/{permissionid}`


<!-- END_99ba298a1edb286674ee68bd5ac4ea3f -->

<!-- START_be518a383b3318be083f53e0c434513e -->
## api/model-has-permission/{modelid}
> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-permission/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/model-has-permission/{modelid}`


<!-- END_be518a383b3318be083f53e0c434513e -->

<!-- START_afbfc1b67799ecfb8c286f1ff94dfe78 -->
## api/model-has-permission-multiple/{modelid}
> Example request:

```javascript
const url = new URL("http://localhost/api/model-has-permission-multiple/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/model-has-permission-multiple/{modelid}`


<!-- END_afbfc1b67799ecfb8c286f1ff94dfe78 -->

<!-- START_3189b35b6a8d92a3ca479d4eb0cfb98a -->
## api/role-has-permission
> Example request:

```javascript
const url = new URL("http://localhost/api/role-has-permission");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET api/role-has-permission`


<!-- END_3189b35b6a8d92a3ca479d4eb0cfb98a -->

<!-- START_2008b79381a8d7fb0c0e38f736fc2f22 -->
## Display the specified resource.

> Example request:

```javascript
const url = new URL("http://localhost/api/role-has-permission/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET api/role-has-permission/{roleid}`


<!-- END_2008b79381a8d7fb0c0e38f736fc2f22 -->

<!-- START_a04ef47cefe4f8b45760d4dd1bcb68ce -->
## api/role-has-permission
> Example request:

```javascript
const url = new URL("http://localhost/api/role-has-permission");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/role-has-permission`


<!-- END_a04ef47cefe4f8b45760d4dd1bcb68ce -->

<!-- START_e6c8fe494f767024778c5ce046e1cce0 -->
## api/role-has-permission/{roleId}
> Example request:

```javascript
const url = new URL("http://localhost/api/role-has-permission/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/role-has-permission/{roleId}`


<!-- END_e6c8fe494f767024778c5ce046e1cce0 -->

<!-- START_deee354481e0a2e216d579bd06b34adf -->
## api/role-has-permission/{roleId}
> Example request:

```javascript
const url = new URL("http://localhost/api/role-has-permission/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/role-has-permission/{roleId}`


<!-- END_deee354481e0a2e216d579bd06b34adf -->

<!-- START_362e59e5c67854945b972ab494d7af0d -->
## api/role-has-permission/{roleid}/{permissionid}
> Example request:

```javascript
const url = new URL("http://localhost/api/role-has-permission/1/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/role-has-permission/{roleid}/{permissionid}`


<!-- END_362e59e5c67854945b972ab494d7af0d -->

<!-- START_9bce5206318e25b8239ea1799547b79f -->
## api/role-has-permission/{roleid}
> Example request:

```javascript
const url = new URL("http://localhost/api/role-has-permission/1");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/role-has-permission/{roleid}`


<!-- END_9bce5206318e25b8239ea1799547b79f -->

#Stock CRUD


<!-- START_2ec1d42f10af712dfa16a2aea8d3f762 -->
## GET all

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL("http://localhost/api/stocks");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": [
        {
            "qty": 10,
            "item_id": 2,
            "location_id": 1,
            "created_at": "2019-09-05 22:53:37",
            "updated_at": "2019-09-05 22:53:37"
        },
        {
            "qty": 20,
            "item_id": 2,
            "location_id": 2,
            "created_at": "2019-09-05 22:53:37",
            "updated_at": "2019-09-05 22:53:37"
        }
    ]
}
```

### HTTP Request
`GET api/stocks`


<!-- END_2ec1d42f10af712dfa16a2aea8d3f762 -->

#User Authentication


<!-- START_c3fa189a6c95ca36ad6ac4791a873d23 -->
## Login user

> Example request:

```javascript
const url = new URL("http://localhost/api/login");

let headers = {
    "Authorization": "Bearer {token}",
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "username": "nemo",
    "password": "et"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/login`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    username | string |  required  | 
    password | string |  required  | 

<!-- END_c3fa189a6c95ca36ad6ac4791a873d23 -->

<!-- START_00e7e21641f05de650dbe13f242c6f2c -->
## Logout user

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL("http://localhost/api/logout");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "message": "Log out berhasil"
}
```

### HTTP Request
`GET api/logout`


<!-- END_00e7e21641f05de650dbe13f242c6f2c -->


