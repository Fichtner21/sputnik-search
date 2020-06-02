import React, { Component } from "react";
import { Search } from "./Search";
import PropTypes from "prop-types";

export class SputnikWordPressSearch extends Component {
  render() {
    const {
      q,
      size,
      from,
      mode,
      cs,
      category,
      d_from,
      d_to,
      sort,
    } = this.props;

    return (
      <Search
        q={q}
        d_from={d_from}
        d_to={d_to}
        category={category}
        cs={cs}
        sort={sort}
        mode={mode}
        size={size}
        from={from}
      />
    );
  }
}

SputnikWordPressSearch.defaultProps = {
  q: "",
  sort: "",
  category: "",
  cs: "",
  d_from: "",
  d_to: "",
  mode: "",
  from: 0,
  size: 10,
};

SputnikWordPressSearch.propTypes = {
  q: PropTypes.string,
  sort: PropTypes.string,
  category: PropTypes.string,
  cs: PropTypes.string,
  d_from: PropTypes.string,
  d_to: PropTypes.string,
  mode: PropTypes.string,
  from: PropTypes.number,
  size: PropTypes.number,
};
