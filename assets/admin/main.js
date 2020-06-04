(function ($) {
  $(document).ready(function () {
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
