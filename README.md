# code-4
- We need a lot of refactoring app/Http/Controllers/BookingController.php, such as:
	- I think we need to use try-catch to handle unexpectation code that will make crash. It will be very helpful to show the error message on frontend too
	- For API, we should be much better write response with specific status code (2xx, 4xx, 5xx).
	- If we want to call a variable than we need to declare it. or if there is if block, we need set on else too. E.g $response on function index(), it will return Undefined.
	- I think we need implementing middleware Auth (session/token) for security purposes, to prevent unauthorize user to steal other user privacy like modify 'user_id' value on request
	- For ADMIN_ROLE_ID and SUPERADMIN_ROLE_ID should be much better if using database structure to make it more flexible
	- For any DB change we should be much better if using DB::beginTransaction(), DB::commit(), and DB::rollback(). espescially if there is more than one proccess in one function, so it will rollback (not stored) if failed.