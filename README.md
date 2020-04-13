# GraphQL server

<!--
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]
-->

GraphQL server in PHP, implemented through the PoP API

## Install

Via Composer

``` bash
composer require getpop/graphql dev-master
```

**Note:** Your `composer.json` file must have the configuration below to accept minimum stability `"dev"` (there are no releases for PoP yet, and the code is installed directly from the `master` branch):

```javascript
{
    ...
    "minimum-stability": "dev",
    "prefer-stable": true,
    ...
}
```

To enable pretty API endpoint `/api/graphql/`, follow the instructions [here](https://github.com/getpop/api#enable-pretty-permalinks)

> Note: if you wish to install a fully-working API, please follow the instructions under [Bootstrap a PoP API for WordPress](https://github.com/leoloso/PoP-API-WP) (even though CMS-agnostic, only the WordPress adapters have been presently implemented).

<!--
### Enable pretty permalinks

Add the following code in the `.htaccess` file to enable API endpoint `/api/graphql/`:

```apache
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /

# Rewrite from /some-url/api/graphql/ to /some-url/?scheme=api&datastructure=graphql
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteCond %{SCRIPT_FILENAME} !-f
RewriteRule ^(.*)/api/graphql/?$ /$1/?scheme=api&datastructure=graphql [L,P,QSA]

# b. Homepage single endpoint (root)
# Rewrite from api/graphql/ to /?scheme=api&datastructure=graphql
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteCond %{SCRIPT_FILENAME} !-f
RewriteRule ^api/graphql/?$ /?scheme=api&datastructure=graphql [L,P,QSA]
</IfModule>
```
-->

## 100% compliant of GraphQL syntax

All [GraphQL queries](https://graphql.org/learn/queries/) are supported (click on the links below to try them out in [GraphiQL](https://newapi.getpop.org/graphiql/)):

- <a href="https://newapi.getpop.org/graphiql/?query=query%20%7B%0A%20%20posts%20%7B%0A%20%20%20%20id%0A%20%20%20%20url%0A%20%20%20%20title%0A%20%20%20%20excerpt%0A%20%20%20%20date%0A%20%20%20%20tags%20%7B%0A%20%20%20%20%20%20name%0A%20%20%20%20%7D%0A%20%20%20%20comments%20%7B%0A%20%20%20%20%20%20content%0A%20%20%20%20%20%20author%20%7B%0A%20%20%20%20%20%20%20%20id%0A%20%20%20%20%20%20%20%20name%0A%20%20%20%20%20%20%7D%0A%20%20%20%20%7D%0A%20%20%7D%0A%7D" target="leoloso-graphiql">Fields</a>
- <a href="https://newapi.getpop.org/graphiql/?query=query%20%7B%0A%20%20posts(limit%3A2)%20%7B%0A%20%20%20%20id%0A%20%20%20%20title%0A%20%20%20%20author%20%7B%0A%20%20%20%20%20%20id%0A%20%20%20%20%20%20name%0A%20%20%20%20%20%20posts(limit%3A3)%20%7B%0A%20%20%20%20%20%20%20%20id%0A%20%20%20%20%20%20%20%20url%0A%20%20%20%20%20%20%20%20title%0A%20%20%20%20%20%20%20%20date(format%3A%22d%2Fm%2FY%22)%0A%20%20%20%20%20%20%20%20tags%20%7B%0A%20%20%20%20%20%20%20%20%20%20name%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20featuredimage%20%7B%0A%20%20%20%20%20%20%20%20%20%20id%0A%20%20%20%20%20%20%20%20%20%20src%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%7D%0A%20%20%20%20%7D%0A%20%20%7D%0A%7D" target="leoloso-graphiql">Field arguments</a>
- <a href="https://newapi.getpop.org/graphiql/?query=query%20%7B%0A%20%20rootPosts%3A%20posts(limit%3A2)%20%7B%0A%20%20%20%20id%0A%20%20%20%20title%0A%20%20%20%20author%20%7B%0A%20%20%20%20%20%20id%0A%20%20%20%20%20%20name%0A%20%20%20%20%20%20nestedPosts%3A%20posts(limit%3A3)%20%7B%0A%20%20%20%20%20%20%20%20id%0A%20%20%20%20%20%20%20%20url%0A%20%20%20%20%20%20%20%20title%0A%20%20%20%20%20%20%20%20date%0A%20%20%20%20%20%20%20%20formattedDate%3A%20date(format%3A%22d%2Fm%2FY%22)%0A%20%20%20%20%20%20%20%20tags%20%7B%0A%20%20%20%20%20%20%20%20%20%20name%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20featuredimage%20%7B%0A%20%20%20%20%20%20%20%20%20%20id%0A%20%20%20%20%20%20%20%20%20%20src%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%7D%0A%20%20%20%20%7D%0A%20%20%7D%0A%7Dhttps://newapi.getpop.org/graphiql/?query=query%20%7B%0A%20%20rootPosts%3A%20posts(limit%3A2)%20%7B%0A%20%20%20%20...postProperties%0A%20%20%20%20author%20%7B%0A%20%20%20%20%20%20id%0A%20%20%20%20%20%20name%0A%20%20%20%20%20%20nestedPosts%3A%20posts(limit%3A3)%20%7B%0A%20%20%20%20%20%20%20%20url%0A%20%20%20%20%20%20%20%20...postProperties%0A%20%20%20%20%20%20%20%20formattedDate%3A%20date(format%3A%22d%2Fm%2FY%22)%0A%20%20%20%20%20%20%7D%0A%20%20%20%20%7D%0A%20%20%7D%0A%7D%0Afragment%20postProperties%20on%20Post%20%7B%0A%20%20id%0A%20%20title%0A%20%20tags%20%7B%0A%20%20%20%20name%0A%20%20%7D%0A%7D" target="leoloso-graphiql">Aliases</a>
- <a href="https://newapi.getpop.org/graphiql/?query=query%20%7B%0A%20%20rootPosts%3A%20posts(limit%3A2)%20%7B%0A%20%20%20%20...postProperties%0A%20%20%20%20author%20%7B%0A%20%20%20%20%20%20id%0A%20%20%20%20%20%20name%0A%20%20%20%20%20%20nestedPosts%3A%20posts(limit%3A3)%20%7B%0A%20%20%20%20%20%20%20%20url%0A%20%20%20%20%20%20%20%20...postProperties%0A%20%20%20%20%20%20%20%20formattedDate%3A%20date(format%3A%22d%2Fm%2FY%22)%0A%20%20%20%20%20%20%7D%0A%20%20%20%20%7D%0A%20%20%7D%0A%7D%0Afragment%20postProperties%20on%20Post%20%7B%0A%20%20id%0A%20%20title%0A%20%20tags%20%7B%0A%20%20%20%20name%0A%20%20%7D%0A%7D" target="leoloso-graphiql">Fragments</a>
- <a href="https://newapi.getpop.org/graphiql/?query=query%20GetPosts%20%7B%0A%20%20rootPosts%3A%20posts(limit%3A2)%20%7B%0A%20%20%20%20id%0A%20%20%20%20title%0A%20%20%20%20author%20%7B%0A%20%20%20%20%20%20id%0A%20%20%20%20%20%20name%0A%20%20%20%20%7D%0A%20%20%7D%0A%7D&operationName=GetPosts" target="leoloso-graphiql">Operation name</a>
- <a href="https://newapi.getpop.org/graphiql/?query=query%20GetPosts(%24rootLimit%3A%20Int%2C%20%24nestedLimit%3A%20Int%2C%20%24dateFormat%3A%20String)%20%7B%0A%20%20rootPosts%3A%20posts(limit%3A%24rootLimit)%20%7B%0A%20%20%20%20id%0A%20%20%20%20title%0A%20%20%20%20author%20%7B%0A%20%20%20%20%20%20id%0A%20%20%20%20%20%20name%0A%20%20%20%20%20%20nestedPosts%3A%20posts(limit%3A%24nestedLimit)%20%7B%0A%20%20%20%20%20%20%20%20id%0A%20%20%20%20%20%20%20%20url%0A%20%20%20%20%20%20%20%20title%0A%20%20%20%20%20%20%20%20date%0A%20%20%20%20%20%20%20%20formattedDate%3A%20date(format%3A%24dateFormat)%0A%20%20%20%20%20%20%7D%0A%20%20%20%20%7D%0A%20%20%7D%0A%7D&operationName=GetPosts&variables=%7B%0A%20%20%22rootLimit%22%3A%203%2C%0A%20%20%22nestedLimit%22%3A%202%2C%0A%20%20%22dateFormat%22%3A%20%22d%2Fm%2FY%22%0A%7D" target="leoloso-graphiql">Variables</a>
- <a href="https://newapi.getpop.org/graphiql/?query=query%20GetPosts(%24tagsLimit%3A%20Int)%20%7B%0A%20%20rootPosts%3A%20posts(limit%3A2)%20%7B%0A%20%20%20%20...postProperties%0A%20%20%20%20author%20%7B%0A%20%20%20%20%20%20id%0A%20%20%20%20%20%20name%0A%20%20%20%20%20%20nestedPosts%3A%20posts(limit%3A3)%20%7B%0A%20%20%20%20%20%20%20%20url%0A%20%20%20%20%20%20%20%20...postProperties%0A%20%20%20%20%20%20%7D%0A%20%20%20%20%7D%0A%20%20%7D%0A%7D%0Afragment%20postProperties%20on%20Post%20%7B%0A%20%20id%0A%20%20title%0A%20%20tags(limit%3A%24tagsLimit)%20%7B%0A%20%20%20%20name%0A%20%20%7D%0A%7D&operationName=GetPosts&variables=%7B%0A%20%20%22tagsLimit%22%3A%203%0A%7D" target="leoloso-graphiql">Variables inside fragments</a>
- <a href="https://newapi.getpop.org/graphiql/?query=query%20GetPosts(%24rootLimit%3A%20Int%20%3D%203%2C%20%24nestedLimit%3A%20Int%20%3D%202%2C%20%24dateFormat%3A%20String%20%3D%20%22d%2Fm%2FY%22)%20%7B%0A%20%20rootPosts%3A%20posts(limit%3A%24rootLimit)%20%7B%0A%20%20%20%20id%0A%20%20%20%20title%0A%20%20%20%20author%20%7B%0A%20%20%20%20%20%20id%0A%20%20%20%20%20%20name%0A%20%20%20%20%20%20nestedPosts%3A%20posts(limit%3A%24nestedLimit)%20%7B%0A%20%20%20%20%20%20%20%20id%0A%20%20%20%20%20%20%20%20url%0A%20%20%20%20%20%20%20%20title%0A%20%20%20%20%20%20%20%20date%0A%20%20%20%20%20%20%20%20formattedDate%3A%20date(format%3A%24dateFormat)%0A%20%20%20%20%20%20%7D%0A%20%20%20%20%7D%0A%20%20%7D%0A%7D&operationName=GetPosts" target="leoloso-graphiql">Default variables</a>
- <a href="https://newapi.getpop.org/graphiql/?query=query%20GetPosts(%24includeAuthor%3A%20Boolean!%2C%20%24rootLimit%3A%20Int%20%3D%203%2C%20%24nestedLimit%3A%20Int%20%3D%202)%20%7B%0A%20%20rootPosts%3A%20posts(limit%3A%24rootLimit)%20%7B%0A%20%20%20%20id%0A%20%20%20%20title%0A%20%20%20%20author%20%40include(if%3A%20%24includeAuthor)%20%7B%0A%20%20%20%20%20%20id%0A%20%20%20%20%20%20name%0A%20%20%20%20%20%20nestedPosts%3A%20posts(limit%3A%24nestedLimit)%20%7B%0A%20%20%20%20%20%20%20%20id%0A%20%20%20%20%20%20%20%20url%0A%20%20%20%20%20%20%20%20title%0A%20%20%20%20%20%20%20%20date%0A%20%20%20%20%20%20%7D%0A%20%20%20%20%7D%0A%20%20%7D%0A%7D&operationName=GetPosts&variables=%7B%0A%20%20%22includeAuthor%22%3A%20true%0A%7D" target="leoloso-graphiql">Directives</a>
- <a href="https://newapi.getpop.org/graphiql/?query=query%20GetPosts(%24includeAuthor%3A%20Boolean!%2C%20%24rootLimit%3A%20Int%20%3D%203%2C%20%24nestedLimit%3A%20Int%20%3D%202)%20%7B%0A%20%20rootPosts%3A%20posts(limit%3A%24rootLimit)%20%7B%0A%20%20%20%20id%0A%20%20%20%20title%0A%20%20%20%20...postProperties%0A%20%20%7D%0A%7D%0Afragment%20postProperties%20on%20Post%20%7B%0A%20%20author%20%40include(if%3A%20%24includeAuthor)%20%7B%0A%20%20%20%20id%0A%20%20%20%20name%0A%20%20%20%20nestedPosts%3A%20posts(limit%3A%24nestedLimit)%20%7B%0A%20%20%20%20%20%20id%0A%20%20%20%20%20%20url%0A%20%20%20%20%20%20title%0A%20%20%20%20%20%20date%0A%20%20%20%20%7D%0A%20%20%7D%0A%7D&operationName=GetPosts&variables=%7B%0A%20%20%22includeAuthor%22%3A%20true%0A%7D" target="leoloso-graphiql">Fragments with directives</a>
- <a href="https://newapi.getpop.org/graphiql/?query=query%20GetPosts(%24rootLimit%3A%20Int%20%3D%203%2C%20%24nestedLimit%3A%20Int%20%3D%202)%20%7B%0A%20%20rootPosts%3A%20posts(limit%3A%24rootLimit)%20%7B%0A%20%20%20%20id%0A%20%20%20%20title%0A%20%20%20%20author%20%7B%0A%20%20%20%20%20%20id%0A%20%20%20%20%20%20name%0A%20%20%20%20%20%20content(limit%3A%24nestedLimit)%20%7B%0A%20%20%20%20%20%20%20%20__typename%0A%20%20%20%20%20%20%20%20title%0A%20%20%20%20%20%20%20%20...%20on%20Post%20%7B%0A%20%20%20%20%20%20%20%20%20%20excerpt%0A%20%20%20%20%20%20%20%20%20%20tags%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20name%0A%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20...%20on%20Media%20%7B%0A%20%20%20%20%20%20%20%20%20%20url%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%7D%0A%20%20%20%20%7D%0A%20%20%7D%0A%7D&operationName=GetPosts" target="leoloso-graphiql">Inline fragments</a>

## Extended GraphQL

An upgraded implementation of the GraphQL server, which enables to resolve [queries as a scripting language](https://leoloso.com/posts/demonstrating-pop-api-graphql-on-steroids/), is found under [this repo](https://github.com/getpop/api-graphql).

It supports several [features](https://leoloso.com/posts/pop-api-features/) not currently defined by the GraphQL spec, including [composable fields](https://github.com/getpop/api-graphql#composable-fields) and [composable directives](https://github.com/getpop/api-graphql#composable-directives).

## Support for REST

By installing the [REST package](https://github.com/getpop/api-rest), the GraphQL server can also satisfy REST endpoints, from a single source of truth. Check out these example links:

- [List of posts](https://newapi.getpop.org/posts/api/rest/)
- [Single post](https://newapi.getpop.org/posts/cope-with-wordpress-post-demo-containing-plenty-of-blocks/api/rest/)

## Demo

The GraphQL API (running on top of [a WordPress site](https://newapi.getpop.org)) is deployed under this endpoint: https://newapi.getpop.org/api/graphql/

You can play with it through the following clients: 

- GraphiQL: https://newapi.getpop.org/graphiql/
- GraphQL Voyager: https://newapi.getpop.org/graphql-interactive/

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email leo@getpop.org instead of using the issue tracker.

## Credits

- [Leonardo Losoviz][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/getpop/graphql.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/getpop/graphql/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/getpop/graphql.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/getpop/graphql.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/getpop/graphql.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/getpop/graphql
[link-travis]: https://travis-ci.org/getpop/graphql
[link-scrutinizer]: https://scrutinizer-ci.com/g/getpop/graphql/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/getpop/graphql
[link-downloads]: https://packagist.org/packages/getpop/graphql
[link-author]: https://github.com/leoloso
[link-contributors]: ../../contributors
