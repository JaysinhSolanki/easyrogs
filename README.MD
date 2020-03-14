# EasyRogs Webapp

## Development

### Local Environment

Clone this repo and:

```bash
$ cp system/settings.sample.php system/settings.php
```

Update `settings.php` with your local DB config.

The suggested development flow is as follows:

1. Update your local and create a branch from `master`.

    e.g.
    ```bash
    $ git pull origin master
    $ git checkout -b bugs/my-bug-fix
    ```

2. Do your work

3. Commit and push to your branch:

    e.g.
    ```bash
    $ git add *
    $ git commit -am "Your commit message"
    $ git push origin bugs/my-bug-fix
    ```

4. Create a pull request from your branch to `dev` or `master`
5. Let other members of the team review and approve your changes.
6. Merge your pull request

The changes will be automatically deployed as specified below.

## Deployments

The deployments pipeline works using a pull strategy, automatically deploying any changes to the repo every minute, as follows:

* Any changes pushed to the `dev` branch will be deployed here: https://dev.easyrogs.com . We use dev.easyrogs.com as staging server.
* Any changes pushed to the `master` branch will be deployed here: https://www.easyrogs.com. This is the production version.


#### Happy coding!
