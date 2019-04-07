## About this repo

This repo implements a simple API in laravel that accomplishes the following requirements
- [Allow user to register on the website and receive a token for API Authentication.
- [After, using simple Bearer Token Authentication built in with Laravel, user can upload multiple pdf files (one for each HTTP Request).
- [PDF Files must have a title and can contain an optional passsword for "extra security".
- [Users can view a list of their own pdf files and the others in json.
- [User with the right token can view or download (depends where the request is made) pdf files, and, if file has a password, it has to be specified on the request.

## Important: This project uses a symmlink 'storage' at public folder. please create that before using it.

## Steps

1. **Register using /login**. You will receive a token that will be used for the next steps.
2. Upload a pdf file (it has to be a pdf file) using a **POST to /api/pdf_file** with body (key:value) title:yourTitle, pdf_file:file (if using Postman, change from text to file and attach your file), api_token:yourApiToken, pdf_password:yourPdfPassword. Response will be the PDF_File Resource associated with your file
3. List all your pdf files using **GET to /api/pdf_files** and providing api_token:yourApiToken on the body.
4. List one specific file using **GET to /api/pdf_files/{id}** replacing {id} by the file id you wanna see and providing api_token:yourApiToken on the body.
5. View (using the browser) or download (Postman, e.g.) a file using **GET to api/pdf_files/view/{id}** and passing api_token:yourApiToken, pdf_password:yourPdfPassword on the body.
6. Delete a pfd_file using **POST to api/pdf_files/delete/{id}** and passing api_token:yourApiToken, pdf_password:yourPdfPassword on the body.

### Security concerns
1. Token based would be better using Oauth2.0.
2. Because of simple authentication, it is suggested to implement using SSL (HTTPS).
3. Hashing passwords for pdf_files would also be imprtant, bu because we don't wanna loose access to a file, that was omitted.
4. Storage Folder may be accessible using a regular shared folder or it can be easily hijacked. Best case scenario would be to store it on a VPS or even using a cloud sotrage solution such as S3 and Google Storage and using SSL with OAuth2.0 to upload/fetch files.
5. User authentication could also be improved.

## API Endpoints from php artisan

+--------+----------+---------------------------+------------------+------------------------------------------------------------------------+--------------+
| Domain | Method   | URI                       | Name             | Action                                                                 | Middleware   |
+--------+----------+---------------------------+------------------+------------------------------------------------------------------------+--------------+
|        | GET|HEAD | /                         |                  | Closure                                                                | web          |
|        | POST     | api/pdf_file              |                  | App\Http\Controllers\PDFFileController@store                           | api,auth:api |
|        | GET|HEAD | api/pdf_files             |                  | App\Http\Controllers\PDFFileController@index                           | api,auth:api |
|        | POST     | api/pdf_files/delete/{id} |                  | App\Http\Controllers\PDFFileController@destroy                         | api,auth:api |
|        | GET|HEAD | api/pdf_files/view/{id}   |                  | App\Http\Controllers\PDFFileController@view                            | api,auth:api |
|        | GET|HEAD | api/pdf_files/{id}        |                  | App\Http\Controllers\PDFFileController@show                            | api,auth:api |
|        | GET|HEAD | api/user                  |                  | Closure                                                                | api,auth:api |
|        | GET|HEAD | home                      | home             | App\Http\Controllers\HomeController@index                              | web,auth     |
|        | POST     | login                     |                  | App\Http\Controllers\Auth\LoginController@login                        | web,guest    |
|        | GET|HEAD | login                     | login            | App\Http\Controllers\Auth\LoginController@showLoginForm                | web,guest    |
|        | POST     | logout                    | logout           | App\Http\Controllers\Auth\LoginController@logout                       | web          |
|        | POST     | password/email            | password.email   | App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail  | web,guest    |
|        | GET|HEAD | password/reset            | password.request | App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm | web,guest    |
|        | POST     | password/reset            | password.update  | App\Http\Controllers\Auth\ResetPasswordController@reset                | web,guest    |
|        | GET|HEAD | password/reset/{token}    | password.reset   | App\Http\Controllers\Auth\ResetPasswordController@showResetForm        | web,guest    |
|        | GET|HEAD | register                  | register         | App\Http\Controllers\Auth\RegisterController@showRegistrationForm      | web,guest    |
|        | POST     | register                  |                  | App\Http\Controllers\Auth\RegisterController@register                  | web,guest    |
+--------+----------+---------------------------+------------------+------------------------------------------------------------------------+--------------+