<div class="wrap sputnik-search-page">
    <div class="sputnik-search-page__inner">
        <div class="sputnik-search-page__branding">
            <img src="<?= plugin_dir_url( dirname( __FILE__ ) ); ?>/assets/admin/logo-sputnik.svg" alt="">
        </div>

        <div class="sputnik-search-page__content">
            <h1 class="sputnik-search-page__title">Sputnik Search</h1>
            <p class="sputnik-search-page__text">Advanced search on website using ElasticSearch</p>

            <div class="sputnik-action-buttons">
                <button class="btn btn--medium btn--primary sputnik-action-buttons__button" title="Synchronizuj Wpisy" id="js-synchronize">Synchronizuj Wpisy</button>
                <button class="btn btn--medium btn--primary sputnik-action-buttons__button" title="Synchronizuj Pliki" id="js-synchronize-files">Synchronizuj Pliki</button>
                <button class="btn btn--medium btn--danger sputnik-action-buttons__button" title="Usuń indeks" id="js-deleteindex">Usuń indeks <small>Usunięcie indeksu spowoduje błąd w wyszukiwarce</small></button>
            </div>

            <ul id="es-logs"></ul>
        </div>
    </div>
</div>

<script type="text/javascript">
    (function($) {
        $(document).ready(function($) {
            const indexed_posts = [];
            // Synchronize index
            $('#js-synchronize').click(function(){
                var index = 0;

                sendPostToES(index);
            });

            function sendPostToES(index) {
                var data = {
                    'action': 'index_post_in_es',
                    id: index
                };

                jQuery.post(ajaxurl, data, function(response) {
                    $('#es-logs').append("<li style='color: green;'>Dodano wpis " + index + "!</li>");

                    // indexed_posts.push(response);  Make array and filter last null array

                    sendPostToES(++index);
                }).fail(function() {
                    $('#es-logs').append("<li style='color: red;'>Nie dodano wpisu " + index + "!</li>");
      
                    sendPostToES(++index);
                });
            }

            // Synchronize files
            $('#js-synchronize-files').click(function(){
                var index = 3905;

                sendFilesToEs(index);
            });
    
            function sendFilesToEs(index) {
                var data = {
                    'action': 'index_attachment_in_es',
                    id: index
                };

                jQuery.post(ajaxurl, data, function(response) {
                    $('#es-logs').append("<li style='color: green;'>Dodano załącznik " + index + "!</li>");

                    sendFilesToEs(++index);
                }).fail(function() {
                    $('#es-logs').append("<li style='color: red;'>Nie dodano załącznika " + index + "!</li>");

                    sendFilesToEs(++index);
                });
            }

            // Delete index
            $('#js-deleteindex').click(function(){
                deleteIndexFromES();
            });

            function deleteIndexFromES() {
                var data = {
                    'action': 'deleteindex',
                };

                jQuery.post(ajaxurl, data, function(response) {
                    // $('#es-logs').append("<li style='color: red;'>Usunięto Indeks!</li>");
                }).fail(function() {
                    $('#es-logs').append("<li style='color: red;'>Nie powiodło się usuwanie indeksu!</li>");
                }).done(function() {
                    window.alert('Poprawnie usunięto indeks, strona zostanie przeładowana w celu utworzenia nowego indeksu.');

                    window.location.reload();
                });
            }
        });
    })(jQuery);
</script> 