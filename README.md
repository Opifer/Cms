[![Build Status](https://travis-ci.org/Opifer/MediaBundle.svg)](https://travis-ci.org/Opifer/MediaBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7bab65ce-147b-4148-90b2-81ea8454ebf0/mini.png)](https://insight.sensiolabs.com/projects/7bab65ce-147b-4148-90b2-81ea8454ebf0)

MediaBundle
===========

This bundle is still very much a work in progress, so BC-breaks will happen until the first stable release.

Creating a media provider
-------------------------

All media providers must implement the `Opifer\MediaBundle\Provider\ProviderInterface`.
The easiest way to get started is to extend from `Opifer\MediaBundle\Provider\AbstractProvider`,
which already implements most of the required methods.

To register the provider, create a service. Tag it with `opifer.media.provider` and give it an `alias`.

```yaml
acme.media.provider.youtube:
    class: Opifer\MediaBundle\Provider\YoutubeProvider
    tags:
        - { name: opifer.media.provider, alias: youtube }
```
