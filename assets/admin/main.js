(function ($) {
  $(document).ready(function () {
    // Synchronize index
    $("#js-synchronize").click(function () {
      var index = 0;

      sendPostToES(index);
    });

    function sendPostToES(index) {
      var data = {
        action: "index_post_in_es",
        id: index,
      };

      jQuery
        .post(ajaxurl, data, function (response) {
          $("#es-logs").append(
            "<li style='color: green;'>Dodano wpis " + index + "!</li>"
          );

          if (response != "nothing") {
            sendPostToES(++index);
          } else {
            const $reloadButton = $("<button/>", {
              text: "Odśwież stronę",
              class: "btn btn--medium btn--primary",
              click: function () {
                window.location.reload();
              },
            });

            $("#es-logs")
              .addClass("synchronize-complete")
              .after($reloadButton)
              .after("<p>Wszystkie wpisy zostały zsynchronizowane.</p>");
            return;
          }
        })
        .fail(function () {
          $("#es-logs").append(
            "<li style='color: red;'>Nie dodano wpisu " + index + "!</li>"
          );

          sendPostToES(++index);
        });
    }

    // Synchronize files
    $("#js-synchronize-files").click(function () {
      var index = 3905;

      sendFilesToEs(index);
    });

    function sendFilesToEs(index) {
      var data = {
        action: "index_attachment_in_es",
        id: index,
      };

      jQuery
        .post(ajaxurl, data, function (response) {
          $("#es-logs").append(
            "<li style='color: green;'>Dodano załącznik " + index + "!</li>"
          );
          console.log(response);
          sendFilesToEs(++index);
        })
        .fail(function () {
          $("#es-logs").append(
            "<li style='color: red;'>Nie dodano załącznika " + index + "!</li>"
          );
          console.log(response);

          sendFilesToEs(++index);
        });
    }

    // Delete index
    $("#js-deleteindex").click(function () {
      deleteIndexFromES();
    });

    function deleteIndexFromES() {
      var data = {
        action: "deleteindex",
      };

      jQuery
        .post(ajaxurl, data, function (response) {
          // $('#es-logs').append("<li style='color: red;'>Usunięto Indeks!</li>");
        })
        .fail(function () {
          $("#es-logs").append(
            "<li style='color: red;'>Nie powiodło się usuwanie indeksu!</li>"
          );
        })
        .done(function () {
          window.alert(
            "Poprawnie usunięto indeks, strona zostanie przeładowana w celu utworzenia nowego indeksu."
          );

          window.location.reload();
        });
    }

    // Categories list
    if ($("#js-sputnik-search-categories-list")) {
      const $categoriesList = $("#js-sputnik-search-categories-list");
      const $categoriesToggleButton = $(
        "#js-sputnik-search-categories-list-toggle"
      );
      const $categoriesListItems = $(".content-categories__item");
      const $categoriesInputs = $(".content-categories__checkbox");

      let animationDelay = 0;

      $categoriesList.slideUp();

      $.each($categoriesListItems, function (index, item) {
        $(item).css("animation-delay", `${animationDelay}ms`);

        animationDelay += 20;
      });

      $.each($categoriesInputs, function (index, input) {
        $(input).on("change input", function () {
          if ($(this).is(":checked")) {
            $(this).parent().parent().addClass("active");
          } else {
            $(this).parent().parent().removeClass("active");
          }
        });
      });

      $categoriesToggleButton.on("click", function (e) {
        e.preventDefault();

        $categoriesList.slideToggle();
      });
    }
  });
})(jQuery);

document.addEventListener("DOMContentLoaded", function () {
  if (document.getElementById("custom-css")) {
    const textarea = document.getElementById("custom-css");

    const myCodeMirror = CodeMirror.fromTextArea(textarea);
  }
});
