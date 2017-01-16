# leoPHPard

Quick url router filter for routes with regular expressions that don't span multiple url pieces.

It takes an partial of this format: a/b/c/d

And an array of routes with or without regular expressions:

a/c/d/f

some/account/{[0-9]{2,10}/

a/{[a-z]}/c/d

In this case the list will become
 
a/{[a-z]}/c/d

You can plug this in your favourite router that handles regular expressions matching - it will look at maching one rule instead of 3. 

With a few hundred or thousand rules there is a noticeable increase in speed.

As long as your regular expressions don't span different url pieces (cointain slashes in them) you can use them. For instance this is not a good url to be used with this library:   some/url{[0-9]/data}/download

## Install 

- composer install with minimum-stability set at dev
- download the src/Filter.php and uset it

## Usage

See test/FilterTest.php or test_random_benchmark/bench.php for usage examples

## Performance

Take a look into test_random_benchmark:. With 1000 random urls with 5-7 pieces of [a-z] or {regexp}, it takes around 2ms (0.0002s) to filter very obvious non-matching rules.

## Work in progress

Improvements to follow
- add more filtering
- better readme :)
- logging (although not sure it's needed?)

Any feedback is welcome