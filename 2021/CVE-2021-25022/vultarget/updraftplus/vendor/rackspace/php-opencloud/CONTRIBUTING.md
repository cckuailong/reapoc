Contributing to php-opencloud
-----------------------------

Welcome! If you'd like to work on php-opencloud, we appreciate your
efforts. Here are a few general guidelines to follow:

1. Use the `working` branch for your pull requests. Except in the case of
   an emergency hotfix, we will only update `master` with official releases.

2. All code needs to come with unit tests. If you're introducing new code, you
   will need to write new test cases; if you're updating existing code, you will
   need to make sure the methods you're updating are still completely covered.

3. Please abide by [PSR-2 code styling](#ensuring-psr-2-coding-style-compliance).

4. Explaining your pull requests is appreciated. Unless you're fixing a
   minor typographical error, create a description which explains your changes
   and, where relevant, references the existing issue you're hoping to fix.

5. If your pull request introduces a large change or addition, please consider
   creating a work-in-progress (WIP) pull request. This lets us review your changes
   and provide feedback early and often rather than all at once when the entire pull
   request is ready. To denote a pull request as WIP, simply add the "PR: Work In Progress"
   label to it. When you are finished with your work in the pull request and
   are ready for a final review and merge, please remove the "PR: Work In Progress"
   label.

6. Document your code!

If you submit code, please add your name and email address to the
CONTRIBUTORS file.

Test Instructions
-----------------

### To run unit tests:
```bash
vendor/bin/phpunit
```

### To run the full suite of acceptance tests:
1. Make sure your [variables-order](http://www.php.net/manual/en/ini.core.php#ini.variables-order) is set to "EGCRS"
2. Set your *PHP_OpenCloud_USERNAME* and *PHP_OpenCloud_API_KEY* variables
3. Run: ```php tests/OpenCloud/Smoke/Runner.php```

## Conventions

* When working on a `Service` class (e.g. [`OpenCloud\Image\Service`](/lib/OpenCloud/Image/Service.php), name methods like so:

  * Methods that return a single resource, say `Foo`, should be named `getFoo`. For example, [`getImage`](/lib/OpenCloud/Image/Service.php#L67).
  * Methods that return a collection of resources, say `Foo`, should be named `listFoos`. For example, [`listImages`](/lib/OpenCloud/Image/Service.php#L53).
  * Methods that create a new resource, say `Foo`, should be named `createFoo`. For example, [`createEntity`](/lib/OpenCloud/CloudMonitoring/Service.php#L105).

* When validating arguments to a method, please throw `\InvalidArgumentException` when an invalid argument is found. For example, see [here](/lib/OpenCloud/LoadBalancer/Resource/LoadBalancer.php#L212-L215).

## Ensuring PSR-2 coding style compliance

The code in this library is compliant with the [PSR-2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md). To ensure that any code you contribute is also PSR-2 compliant, please run the following command from the base directory of this project _before_ submitting your contribution:

    $ vendor/bin/php-cs-fixer fix --level psr2 .

Running this command will _change_ your code to become PSR-2 compliant. You will need to _commit_ these changes and make them part of your pull request.

## Releasing a new version of php-opencloud
If you are a core contributor to php-opencloud, you have the power to release new versions of it. Here are the steps to follow to ensure a proper release:

1. Update the value of the the [`VERSION` constant](/lib/OpenCloud/Version.php#L30).
2. Merge the `working` branch into the `master` branch.
3. [Run the smoke tests](#to-run-the-full-suite-of-acceptance-tests). If they fail, make necessary changes and go to step 2.
4. [Create new release notes](https://github.com/rackspace/php-opencloud/releases/new).
5. Publish release notes.
6. Announce release via appropriate channels.
7. Party :tada: :balloon: