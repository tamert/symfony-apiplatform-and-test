# symfony-apiplatform-and-test

### Technical requirements

* Install PHP **7.3** or **7.4** and these PHP extensions (which are installed and enabled by default in most PHP 7 installations): [Ctype](https://www.php.net/book.ctype), [iconv](https://www.php.net/book.iconv), [JSON](https://www.php.net/book.json), [PCRE](https://www.php.net/book.pcre), [Session](https://www.php.net/book.session), [SimpleXML](https://www.php.net/book.simplexml), and [Tokenizer](https://www.php.net/book.tokenizer);

* Install [Composer](https://getcomposer.org/download/), which is used to install PHP packages.

* Download the [Symfony CLI](https://symfony.com/download) and check that your requirements are met with `symfony check:requirements`

* run `composer install` and start the local web-server with `symfony server:start`

You are now fully equipped to start developing! A local sqlite database is already set up to be used with Doctrine, too.

### Brief

It's November, and everyone is planning their holiday vacation. But management is struggling! They need a solution to approve vacation requests while also ensuring that there are still enough employees in the office to achieve end-of-year goals.  

Your task is to build one HTTP API that allows employees to make vacation requests, and another that provides managers with an overview of all vacation requests and allows them to decline or approve requests.

### Tasks

- Implement assignment using:
    - Language: PHP
    - Framework: Symfony
- There should be API routes that allow workers to:
    - See their requests
        - Filter by status (approved, pending, rejected)
    - See their number of remaining vacation days
    - Make a new request if they have not exhausted their total limit (30 per year)
- There should be API routes that allow managers to:
    - See an overview of all requests
        - Filter by pending and approved
    - See an overview for each individual employee
    - See an overview of overlapping requests
    - Process an individual request and either approve or reject it
- Write tests for your business logic

### Evaluation Criteria

- PHP best practices
- Completeness: Did you include all features?
- Correctness: Does the solution perform in a logical way?
- Maintainability: Is the solution written in a clean, maintainable way?
- Testing: Has the solution been adequately tested?
- Documentation: Is the API well-documented?

Documentation:
api/docs

for test:
```
php bin/console doctrine:schema:update --force --no-interaction  --env=test 
php bin/console doctrine:fixtures:load --env=test
php ./vendor/bin/phpunit  
```
