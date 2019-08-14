# Cmpsr

Cmpsr is an online bundler I made for composer.

#### Why?

When developing on my iPad Pro locally using DraftCode, I noticed it was very difficult because, although it supports offline PHP running, you cannot run composer commands to get packages from developers. To solve this, I built this application.

#### How it works

It works by generating a zipped vendor directory based on the composer file you give to it. Then when the generation is complete, it returns the hash and URL of the ZIP file.

For now this works for me, though it has some limitations which could be addressed later.

## Installation

This project was built with Laravel and installation is similar. Nothing specific to have really. You just need your usual things:

-   An environment file (.env) with the correct database settings
-   Run `composer install` in the root of the project
-   Run `php artisan migrate` to migrate the database

## Usage

To generate a package send a POST request to `/install` with `data` as the body of the request. The value of `data` should be the contents of the `composer.json` file. For example:

```php
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://cmpsr.co/install",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"data\": \"{\\\"name\\\": \\\"neo\\/dumpr\\\",\\\"description\\\": \\\"a project\\\",\\\"require\\\":{\\\"symfony\\/var-dumper\\\": \\\"^4.3\\\"}}\"}",
  CURLOPT_HTTPHEADER => array(
    "Accept: application/json",
    "Content-Type: application/json",
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if (! $err) {
  echo $response;
}
```

## Known limitations

I suspect this list will grow but for now, the known limitations are:

-   [ ] `autoload.classmap` is not supported
-   [ ] `autoload.files` is not supported
-   [ ] `autoload-dev.classmap` is not supported
-   [ ] `autoload-dev.files` is not supported

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)
