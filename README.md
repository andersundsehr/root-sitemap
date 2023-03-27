# TYPO3 EXT:root_sitemap

`composer req andersundsehr/root-sitemap`

## What dose this Extension do?

if you have your languages like this: `/en/` and this: `/de/`  
**and not a single language configured like this: `/`**  
Than this Extension is for you.  

It adds the endpoint `/sitemap.xml` to all your Sites.  
This sitemap includes all active language sitemaps like: `/en/?type=1533906435` and `/de/?type=1533906435`.  

You do not need to configure anything.


### I want nicer URLS:

you can configure a routeEnhancer like this:  
file: `config/sites/.../config.yaml`
````yml
routeEnhancers:
  PageTypeSuffix:
    type: PageType
    # if you want to have trailing slashes for all pages:
    default: '/'
    index: ''
    map:
      /: 0
      sitemap.xml: 1533906435
````

# with ♥️ from anders und sehr GmbH

> If something did not work 😮  
> or you appreciate this Extension 🥰 let us know.

> We are hiring https://www.andersundsehr.com/karriere/

