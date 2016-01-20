Configuration Reference
==========================

The complete configuration reference with its default values

```yaml
opifer_media:
    media:
        class: ~
        manager:
        views:
            index: 'OpiferMediaBundle:Media:index.html.twig'
            create: 'OpiferMediaBundle:Media:create.html.twig'
            edit: 'OpiferMediaBundle:Media:edit.html.twig'
    providers:
        youtube:
            api_key: ~
    default_storage: local_storage # Or aws_storage
    storages:
        local:
            directory: %kernel.root_dir%/../web/uploads
        temp:
            directory: /tmp
        aws_s3:
            key:    ~
            secret: ~
            region: ~
            bucket: ~
```

[Return to the index](../../README.md)
