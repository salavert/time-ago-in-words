# Time ago in words Twig extension
[![Packagist](https://img.shields.io/packagist/dt/salavert/time-ago-in-words.svg)]() [![Build Status](https://travis-ci.org/salavert/time-ago-in-words.svg?branch=master)](https://travis-ci.org/salavert/time-ago-in-words)

This is a Twig extension for Symfony2 Framework where you can easily convert a datetime/timestamp to a distance of time in words.

By example

```twig
{{ user.lastLogin|time_ago_in_words }}
```

Outputs __3 days ago__

# Installation for Symfony2

1) Update your composer.json

```json
"require": {
	"salavert/time-ago-in-words": "1.*"
}
```

or use composer's require command:

	composer require salavert/time-ago-in-words:1.*

2) Register an Extension as a Service

Now you must let the Service Container know about your newly created Twig Extension:

YAML:

```yaml
# app/config/config.yml
services:
	salavert.twig.time_ago:
		class: Salavert\Twig\Extension\TimeAgoExtension
		arguments: [@translator]
		tags:
		- { name: twig.extension }
```

XML:

```xml
# or into your bundle src\Acme\AcmeBundle\Resources\config\services.xml
<service id="salavert.twig.time_ago" class="Salavert\Twig\Extension\TimeAgoExtension">
	<tag name="twig.extension" />
	<argument type="service" id="translator" />
</service>
```

# Usage

To display distance of time in words between a date and current date:

	{{ message.created|time_ago_in_words }}

To display distance of time between two custom dates you should use 

	{{ message.created|distance_of_time_in_words(message.updated) }}

You also have two available options, for both time_ago_in_words & distance_of_time_in_words filters
	
- include_seconds (boolean) if you need more detailed seconds approximations if time is less than a minute
- include_months (boolean) if you want days to be approximated in months if time is greater than 31 days.

Thus, if you want to have the months approximation but not the seconds one, you should use:

	{{ message.created|time_ago_in_words(false, true) }}

# Translations

Add the following translations to your `\app\Resources\translations\messages.locale.yml`

This is a translation to spanish:

	# Time ago in words - Twig Extension
	less than %seconds seconds ago: hace menos de %seconds segundos
	half a minute ago: hace medio minuto
	less than a minute ago: hace menos de un minuto
	1 minute ago: hace 1 minuto
	%minutes minutes ago: hace %minutes minutos
	about 1 hour ago: hace casi 1 hora
	about %hours hours ago: hace %hours horas
	1 day ago: hace 1 día
	%days days ago: hace %days días
	"{1} 1 month ago |]1,Inf[ %months months ago": "{1} hace un mes |]1,Inf[ hace %months meses"
    "{1} 1 year ago |]1,Inf[ %years years ago":  "hace un año |]1,Inf[ Hace %years años" 

In the same case, for future:

    # Time ago in words - Twig Extension
    in less than %seconds seconds: en menos de %seconds segundos
    in half a minute: en medio minuto
    in less than a minute: en menos de un minuto
    in 1 minute: en 1 minuto
    in %minutes minutes: en %minutes minutos
    in about 1 hour: dentro de casi 1 hora
    in about %hours hours: en %hours horas
    in 1 day: en 1 día
    in %days days: en %days días
	
# Testing

To launch all tests first make sure dependecies are met with composer and run:

	./vendor/bin/phpunit
