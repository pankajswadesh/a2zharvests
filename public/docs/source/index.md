---
title: API Reference

language_tabs:
- bash
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

#general
<!-- START_8ae5d428da27b2b014dc767c2f19a813 -->
## api/v1/register

> Example request:

```bash
curl -X POST "http://localhost/api/v1/register" \
    -H "Accept: application/json"
```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/register",
    "method": "POST",
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```


### HTTP Request
`POST api/v1/register`


<!-- END_8ae5d428da27b2b014dc767c2f19a813 -->

<!-- START_8c0e48cd8efa861b308fc45872ff0837 -->
## api/v1/login

> Example request:

```bash
curl -X POST "http://localhost/api/v1/login" \
    -H "Accept: application/json"
```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/login",
    "method": "POST",
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```


### HTTP Request
`POST api/v1/login`


<!-- END_8c0e48cd8efa861b308fc45872ff0837 -->

<!-- START_33cf01c53ceed84f1e23700a1952d16f -->
## api/v1/forget-password

> Example request:

```bash
curl -X POST "http://localhost/api/v1/forget-password" \
    -H "Accept: application/json"
```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/forget-password",
    "method": "POST",
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```


### HTTP Request
`POST api/v1/forget-password`


<!-- END_33cf01c53ceed84f1e23700a1952d16f -->

<!-- START_e23624e6d4fd3e678e4c89ccba2d7290 -->
## api/v1/check-otp/{token}

> Example request:

```bash
curl -X GET -G "http://localhost/api/v1/check-otp/{token}" \
    -H "Accept: application/json"
```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/check-otp/{token}",
    "method": "GET",
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```

> Example response:

```json
null
```

### HTTP Request
`GET api/v1/check-otp/{token}`


<!-- END_e23624e6d4fd3e678e4c89ccba2d7290 -->

<!-- START_642fe50220946a9535c8dae185c9b4ca -->
## api/v1/reset-password

> Example request:

```bash
curl -X POST "http://localhost/api/v1/reset-password" \
    -H "Accept: application/json"
```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/reset-password",
    "method": "POST",
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```


### HTTP Request
`POST api/v1/reset-password`


<!-- END_642fe50220946a9535c8dae185c9b4ca -->

<!-- START_b8051b5334ec540bd4b439bed1251887 -->
## api/v1/supplier-registration

> Example request:

```bash
curl -X POST "http://localhost/api/v1/supplier-registration" \
    -H "Accept: application/json"
```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/supplier-registration",
    "method": "POST",
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```


### HTTP Request
`POST api/v1/supplier-registration`


<!-- END_b8051b5334ec540bd4b439bed1251887 -->

<!-- START_8509a6dc9f7393e1d005659d4f391d20 -->
## api/v1/supplier-login

> Example request:

```bash
curl -X POST "http://localhost/api/v1/supplier-login" \
    -H "Accept: application/json"
```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/supplier-login",
    "method": "POST",
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```


### HTTP Request
`POST api/v1/supplier-login`


<!-- END_8509a6dc9f7393e1d005659d4f391d20 -->

<!-- START_8c927cfb2fa15ad654dad387bf3563ca -->
## api/v1/get-user-data

> Example request:

```bash
curl -X POST "http://localhost/api/v1/get-user-data" \
    -H "Accept: application/json"
```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/get-user-data",
    "method": "POST",
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```


### HTTP Request
`POST api/v1/get-user-data`


<!-- END_8c927cfb2fa15ad654dad387bf3563ca -->

<!-- START_0f5a58b5b66583ed58a6086702a3837b -->
## api/v1/vendor-product-mapped

> Example request:

```bash
curl -X POST "http://localhost/api/v1/vendor-product-mapped" \
    -H "Accept: application/json"
```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/vendor-product-mapped",
    "method": "POST",
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```


### HTTP Request
`POST api/v1/vendor-product-mapped`


<!-- END_0f5a58b5b66583ed58a6086702a3837b -->


