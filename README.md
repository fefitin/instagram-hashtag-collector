<!-- @format -->

# instagram-hashtag-collector

Buscar imágenes en Instagram usando API de Facebook.

## Configuración

Instalar dependencias de composer:

```
php composer.phar install
```

Configurar variables de entorno:

```
FACEBOOK_APP_ID=XXXXX
FACEBOOK_APP_SECRET=YYYYY
```

## Ejemplo de uso

El archivo index.php genera un access token y lo utiliza para obtener imágenes de Instagram asociadas a un hashtag.

Para utilizarlo, definir el nombre de la página de Facebook a utlizar y el hashtag a buscar:

```
  $pageName = 'Divino Diseño';
  $hashtag = 'city';
```

La página de Facebook debe tener asociada una cuenta de Instagram de tipo Business o Content Creator. El usuario utilizado para el login con Facebook debe ser administrador de dicha página.
