# Sitegeist.ShelfLife
## Modification in sitemap.xml that take content modifications into account

Since Neos calculates the last modification dates for a document solely based on the last changes to the document-node itself those dates do not take changes to the content of the document into account because this would be an expensive calculation at read time.

This package will listen to the changes made to nodes and store modifications date for the closest live documents in a dedicated database table that can then be accessed easily. This allows to generate correct modification dates for the sitemap.xml but also allows to use the stored dates for other purposes.

!!! The package respects the node dimensions but is not extensively tested in this regard yet as it is developed for a one dimensional use case. Dou your own testing if you want to use it in a multidimensional environment. !!!

### Authors & Sponsors

* Martin Ficzel - ficzel@sitegeist.de

*The development and the public-releases of this package is generously sponsored
by our customer https://www.biallo.de*

## Installation

Sitegeist.ShelfLife is available via packagist. Run `composer require sitegeist/shelflife`.

We use semantic-versioning so every breaking change will increase the major-version number.

## Usage 

### sitemap.xml

The package will automatically adjust the sitemap.xml and use newer dates from observed modifications when possible. When no modifications were observed for a node yet the existing last modification date is used.

### Eel 

To access the observed modifications dates the helper `Sitegeist.ShelfLife.documentModificationDate` is added that will return the observed modifiction date (including content) or fallback to the last modification date as it was before.

```neosfusion
lastModificationDate = ${Sitegeist.ShelfLife.documentModificationDate( node )}
```

## Contribution

We will gladly accept contributions. Please send us pull requests.
