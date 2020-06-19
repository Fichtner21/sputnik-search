document.addEventListener("DOMContentLoaded", function () {
  const activeClass = "active";

  if (document.getElementById("sputnik-search-form-toggle")) {
    const searchToggleButton = document.getElementById(
      "sputnik-search-form-toggle"
    );

    const searchForm = document.getElementById("sputnik-search-form");

    searchToggleButton.addEventListener("click", function (e) {
      e.preventDefault();

      searchForm.classList.toggle(`sputnik-search-form--${activeClass}`);
    });
  }
});
