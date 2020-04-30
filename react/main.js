import React from "react";
import ReactDOM from "react-dom";
import { SputnikWordPressSearch } from "./SputnikWordPressSearch";

window.configES = {
  apiURL: "",
  user: "",
  blogID: "",
  thumbnail: {
    width: 340,
    height: 220,
  },
  facebook: {
    iconUrl: "",
  },
  onSearch: Function.prototype,
};

window.InitSputnikWordpressSearch = function (
  id = "search-app",
  q = "",
  size = 10,
  from = 0,
  mode = "",
  cs = "",
  category = "",
  d_from = "",
  d_to = "",
  sort = "score"
) {
  ReactDOM.render(
    <SputnikWordPressSearch
      q={q}
      cs={cs}
      sort={sort}
      category={category}
      d_from={d_from}
      d_to={d_to}
      mode={mode}
      size={size}
      from={from}
    />,
    document.getElementById(id)
  );
};
