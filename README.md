# Project Title

Short description of project.

## Requirements

List of software dependencies required to run the project, including versions if applicable.

- PHP >= 8.1
- MySQL >= 8.0

## Installation

Instructions for installing the project on a local machine or server.

1. Clone the repository: `git clone git@github.com:melkonyanmisha/yii2-rbac.git`
2. Install dependencies: `composer install`
3. Configure the database connection in `config/db.php`
4. Run migrations: `php yii migrate`

This will generate the following users:
- `Admin User:`
  - `Username: admin`
  - `Password: admin123`

- `Moderator User:`
    - `Username: moderator`
    - `Password: admin123`
- `User:`
    - `Username: user`
    - `Password: admin123`

## User Role Modification
You can modify user roles from the Yii2 dashboard. {domain}/web/user/index

## API Endpoints

List of available API endpoints with brief descriptions of their functionality.

**Method:** GET

- `/web/api/me` - Retrieve info about current user
- `/web/api/post` - Retrieve a list of posts
- `/web/api/post/{:id}` - Retrieve details of a specific post
- `/web/api/post?start_date={timestamp}&end_date={timestamp}` - Retrieve posts between dates
- `/web/api/post?title={title}` - Retrieve posts by title
- `/web/api/post?author_id={:id}` - Retrieve posts by Author ID

**Method:** POST

- `/web/api/register` - User registration
- `/web/api/login` - User login
- `/web/api/post/create` - Create a new post

**Method:** PUT

- `/web/api/post/{:id}` - Update the post

## Postman Collection
A Postman collection containing sample requests for the API endpoints is provided in the project. You can import this
collection into Postman to easily test the endpoints.

- `RBAC.postman_collection.json`

## React App

You can run the React app using the following commands:

```bash
cd client
npm install
npm start