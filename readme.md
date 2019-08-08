
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
6. associate a related model
7. select certain attributes from model
8. select related model's attributes
9. fetch available related models on options request 
10. support pagination
# Not supported yet: 
- fetch related model by relation type i.e:  if a one to one relation: object is returned instead of array 
- grouping operator for filterer
- filter by mutator 
- fetch options for a certain model scopes, mutators
- hide mutator if not requested by select
- upon requesting options: fetch description using annotation
- filter using scopes as: functionName(params)
- limit the count of related object on select, associate
- add strict mode for fetching only annotated relations && filters

# running tests
for running the tests use:  `vendor/bin/phpunit` within the package directory
or config phpstrom by using the config file `phpunit.xml` within the package dir
