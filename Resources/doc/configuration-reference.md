Configuration Reference
==========================

The complete configuration reference with its default values

```yaml
opifer_media:
    media_class: ~
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
