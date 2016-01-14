Configuration Reference
=======================


```
opifer_cms:
    locale: en
    google_captcha_site_key: ~
    google_captcha_secret: ~
    database:
        driver: pdo_mysql
        host: 127.0.0.1
        port: ~
        name: ~
        user: ~
        password: ~
        table_prefix: "opifer_"
    classes:
        user:
            model: Opifer\CmsBundle\Entity\User
            repository: ~
```
