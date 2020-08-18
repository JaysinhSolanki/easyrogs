# EasyRogs Webapp

## Development

### Local Environment

Clone this repo and:

```bash
composer install
cp system/settings.sample.php system/settings.php
cp .env.sample .env
cp .env.test.sample .env.test
```

* Update `ROOTURL` and `ROOTPATH` with your local values in `settings.php`.
* Set your DB credentials in `.env`
* Set your test DB credentials in `.env.test`

The suggested development flow is as follows:

1. Update your local and create a branch from `master`.

    e.g.
    ```bash
    git pull origin master
    git checkout -b bugs/my-bug-fix
    ```

2. Do your work

3. Commit and push to your branch:

    e.g.
    ```bash
    git add *
    git commit -am "Your commit message"
    git push origin bugs/my-bug-fix
    ```

4. Create a pull request from your branch to `master`
5. Let other members of the team review and approve your changes.
6. Merge your pull request

The changes will be automatically deployed as specified below.

## Tests

Add your [PHPUnit](https://phpunit.de/) tests in `/tests` following the project structure.

Run tests suite:
```bash
./test
```

## Deployments

The deployments pipeline works using a pull strategy, automatically deploying any changes to the repo every minute, as follows:

* Any changes pushed to the `master` branch will be deployed here: https://dev.easyrogs.com . We use dev.easyrogs.com as staging server. New migrations will run automatically.
* **Production deployments** occur frequently and are executed manually. The production version of the app can be seen here https://members.easyrogs.com.


## DB Migrations

Database migrations are implemented using [Poorman Migrations](https://packagist.org/packages/soft4good/poorman-migrations). Please refer to the documentation [here](https://github.com/soft4good/poorman-migrations/blob/master/README.md).

Our composer setup already takes care of installing the package and running the setup task, you only need to update the created `.env` file with your database credentials.

#### Happy coding!
