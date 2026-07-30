Hi Christ-Jan, nog een korte uitleg over de applicatie:

Deze applicatie simuleert een parkeergarage. Hiervoor heb ik 2 entities gemaakt: ParkingGarage en ParkingSession.

De CRUD operaties voor de ParkingGarage werken niet helemaal; ik had hier ook niet de focus op gelegd tijdens het oefenen. De crud operaties voor de ParkingGarage werken nog volgens de eerste opzet, dus met een gescaffolde controller. Ik zou hier alleen de POST even voor gebruiken. Voorbeeld payload:

{
  "name": "Garage01",
  "capacity": 5
}

Deze kan je opsturen naar /api/parking/garage/

Het stukje om auto's in- en uit te laten rijden heb ik gebouwd met behulp van API platform. De routes staan op de entity zelf in PHPAttributes.

Je kan een auto laten "inrijden" door een ParkingSession entity aan te maken. Dit gaat via /api/start-parking-session. Voorbeeld JSON:

{
  "garageId": 1,
  "actualEntry": "2026-07-30 11:00:00",
  "licencePlate": "ABC123"
}

Bij het inrijden checkt de ParkingSessionStartProcessor of er plek is in de garage (hier kijkt hij naar het aantal actieve sessies en vergelijkt dat met de capaciteit van de garage). Zo ja, dan maakt hij de entity aan. Hier heb ik ook het stukje met de locks ingebouwd om concurrency issues tegen te gaan.

Bij het "uitrijden" wordt /api/end-parking-session aangeroepen (POST). Voorbeeld JSON:

{
  "garageId": 1,
  "actualExit": "2026-07-30 12:00:00",
  "licencePlate": "ABC123"
}

Hier simuleert de ParkingSessionEndProcessor ook nog het versturen van een mail; voor dit heb ik om het simpel te houden gewoon een logger gebruikt in de MessageHandler. Ik wilde vooral even kijken hoe de flow gaat bij het gebruiken van messages.

Verder is het nog mogelijk om een kenteken op te geven en (indien deze bestaat) de actieve parkeersessie voor dit kenteken op te vragen. Hiervoor kan je het endpoint /api/get-active-session/{$licencePlate} gebruiken (POST).

Om de applicatie te runnen kan je in de folder van de repo het commando docker compose up -d uitvoeren. 
Om commands te runnen binnen de applicatie kan je docker exec -it parking-php-1 <command> runnen.

Als ik wat meer tijd had gehad had ik ook wat tests geschreven maar ivm de beperkte tijd heb ik dit achterwege gelaten.

Hieronder de meegeleverde README van Symfony zelf over de infrastructuur van de applicatie:

# Symfony Docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework,
with [FrankenPHP](https://frankenphp.dev) and [Caddy](https://caddyserver.com/) inside!

Coding-agents ready: ships with a [Dev Container](https://containers.dev/) and a [one-page guide](docs/agents.md)
to run [OpenCode](https://opencode.ai), [Claude Code](https://claude.ai/claude-code), or any AI coding assistant,
against a local or a remote model, with an optional network sandbox.

![CI](https://github.com/dunglas/symfony-docker/workflows/CI/badge.svg)

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up --wait` to set up and start a fresh Symfony project
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Features

- Production, development and CI ready
- Just 1 service by default
- Super-readable configuration
- Blazing-fast performance thanks to [the worker mode of FrankenPHP](https://frankenphp.dev/docs/worker/)
- [Installation of extra Docker Compose services](docs/extra-services.md) with Symfony Flex
- Automatic HTTPS (in dev and prod)
- HTTP/3 and [Early Hints](https://symfony.com/blog/new-in-symfony-6-3-early-hints) support
- Real-time messaging thanks to a built-in [Mercure hub](https://symfony.com/doc/current/mercure.html)
- [Vulcain](https://vulcain.rocks) support
- Native [XDebug](docs/xdebug.md) integration
- [Hot Reloading](https://frankenphp.dev/docs/hot-reload/)
- [Dev Container](https://containers.dev/) support
- [AI coding agents](docs/agents.md) with an optional network sandbox
- Rootless, slim production image

**Enjoy!**

## Docs

1. [Options available](docs/options.md)
2. [Using Symfony Docker with an existing project](docs/existing-project.md)
3. [Support for extra services](docs/extra-services.md)
4. [Deploying in production](docs/production.md)
5. [Debugging with Xdebug](docs/xdebug.md)
6. [TLS Certificates](docs/tls.md)
7. [Using MySQL instead of PostgreSQL](docs/mysql.md)
8. [Using Alpine Linux instead of Debian](docs/alpine.md)
9. [Using a Makefile](docs/makefile.md)
10. [Updating the template](docs/updating.md)
11. [Troubleshooting](docs/troubleshooting.md)
12. [Using AI coding agents](docs/agents.md)

## License

Symfony Docker is available under the MIT License.

## Credits

Created by [Kévin Dunglas](https://dunglas.dev), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).
