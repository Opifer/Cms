[![Build Status](https://travis-ci.org/Opifer/MediaBundle.svg)](https://travis-ci.org/Opifer/MediaBundle)

MediaBundle
===========

Creating a media provider
-------------------------

All media providers must implement the `Opifer\MediaBundle\Provider\ProviderInterface`.
The easiest way to get started is to extend from `Opifer\MediaBundle\Provider\AbstractProvider`,
which already implements most of the required methods.

To register the provider, create a service. Tag it with `opifer.media.provider` and give it an `alias`.

    acme.media.provider.youtube:
        class: Opifer\MediaBundle\Provider\YoutubeProvider
        tags:
            - { name: opifer.media.provider, alias: youtube }

Done.
