# Time ago in words Twig extension

This is a Twig extension for Symfony2 Framework where you can easily conrvert a datetime/timestamp to a distance of time in words

By example

	{{ user.lastLogin|time_ago_in_words }}
	
Outputs

	3 days ago

# Installation for Symfony2

1. Copy Twig folder into your Bundle

2. Activate twig extensions and set up our new sextension at `/app/config/config.yml` (replace acme by your bundle name)

These lines must go below `services:`
	
		acmebundle.twig.timeago_extension:
			class: Acme\AcmeBundle\Twig\TimeAgoExtension
			arguments: [@translator]
			tags:
			-  { name: twig.extension }
			
		twig.extension.text:
			class: Twig_Extensions_Extension_Text
			tags:
			- { name: twig.extension }


# Usage

To display distance of time in words between a date and current date:

	{{ message.created|time_ago_in_words }}
	

To display distance of time between two custom dates you should use 

	{{ message.created|distance_of_time_in_words(message.updated) }}

# Tanslations

Add the following translations to your `\app\Resources\messages.locale.yml`

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
