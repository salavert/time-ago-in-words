# Time ago in words Twig extension

This is a Twig extension for Symfony2 Framework where you can easily convert a datetime/timestamp to a distance of time in words.

By example

	{{ user.lastLogin|time_ago_in_words }}
	
Outputs

	3 days ago

# Installation for Symfony2

1) Update your composer.json

```
{
	"require": {
		"salavert/time-ago-in-words": "1.*"
	}
}
```

or use composer's require command:

	composer require salavert/time-ago-in-words:1.*

2) Register an Extension as a Service

Now you must let the Service Container know about your newly created Twig Extension:

YAML:

```
# app/config/config.yml
services:
	salavert.twig.time_ago:
		class: Salavert\Twig\Extension\TimeAgoExtension
		arguments: [@translator]
		tags:
		- { name: twig.extension }
```

XML:

```
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

