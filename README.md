# Elasticsearch

Elasticsearch to rozproszony silnik RESTful służący do wyszukiwania i analizy, który jest w stanie sprostać rosnącej liczbie przypadków użycia. Będąc sercem Elasticsearch, centralnie przechowuje dane w celu szybkiego wyszukiwania, precyzyjnej trafności i wydajnych analiz, które można łatwo skalować.

### Changelog

- [v1.3.0] Http -> Https (elasticsearch.sputnik.pl)

### Tech

W projeckie integracji Elasticsearch wykorzystujemy:

- [React] - Wyświetlanie wyników (feature)
- [node.js] - Backend Elastica (build)
- [Express] - Back node.js network app framework (build)
- [Vanilla-JS] Front wtyczki (build)
- [PHP] - natywny język wtyczki, środowiska WP (build)

### Installation

Eleasticsearch wymaga połączenia szyfrowanego (ssh) do maszyna na AWS (Amazon web services).
W tym celu należy połączyć się z:

```sh
ssh user@35.158.146.123 -p 11199
p: [tajne]
```

Następnie wpisujemy komende

```
su -
```

I ponownie hasło

```
[tajne]
```

Dalej przechodzimy do:

```
cd /home/es-service/service
```

I w tym miejscu należy wowołać skypt tworzący nowego użytkownika(userName - tutaj należy wpisać nazwe, pod kluczek -k nalezy koniecznie wpisać pX2a1Sd3k0LKs2BN).

```
node createUser.js -u userName -k pX2a1Sd3k0LKs2BN
```

### Menagment Elasticsearch in node.js

Resetowanie maszyny:

```
pm2 restart 0
```

- 0 to index
  Litowanie indexów na maszynie:

```
pm2 list
```

Sprawdzanie pojemności indeksów na maszynie (ilość wpisów, waga):

```
curl http://127.0.0.1:9200/_cat/indices
```

### Plugins

Elasticsearch jest obecnie dostępny na naszym githubie.

| Plugin | README                           |
| ------ | -------------------------------- |
| GitHub | [plugins/github/README.md][plgh] |

### Development

#### Building for source

### Todos

- Zrobić możliwość dodania od razu z ikonką lupy która działa na klik i wysuwa wyszukiwarkę. Opcja do wyboru.
- Naprawić/zrobić synchronizację plików.
- Dodać SCSS.

### Usage

- Aby wyświetlić wyszukiwarkę ES należy użyc Shortcodu w miejscu, gdzie chcemy umieścić wyszukiwarkę. Najczęściej będzie to header.php w motywie.

```
<!-- Sputnik Search plugin search form -->
<?= shortcode_exists( 'sputnik_search_form' ) ? do_shortcode( '[sputnik_search_form]' ) : false; ?>
```
