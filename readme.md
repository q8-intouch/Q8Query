
# Installation 

### publish config 
`php artisan vendor:publish --provider="Q8Intouch\Q8Query\Q8QueryServiceProvider" --tag="config"`


# config 
1. // TODO

# Features:

1. fetch models by url schema
2. fetch a certain model by id
3. fetch related models by specifying related name
4. filter using logic operators
5. filter using Comparison operators

# Not supported yet: 
1. fetch related model by relation type i.e:  if a one to one relation: object is returned instead of array 
2. grouping operator for filterer
3. filter by mutator 
4. fetch options for a certain model scopes, mutators, related
5. `only` to fetch certain params only 
6. `expand` to fetch a related model
7.  `expand` combined with `only` to fetch certain params
8. hide mutator if not requested by select
# running tests
for running the tests use:  `vendor/bin/phpunit` within the package directory
or config phpstrom by using the config file `phpunit.xml` within the package dir
