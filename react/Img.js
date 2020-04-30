import React, { Component } from "react";
import PropTypes from "prop-types";

export class Img extends Component {
  constructor(props) {
    super(props);

    this.state = {
      loaded: false,
    };
  }

  componentDidMount() {
    this.loadImage(this.props.src);
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.src !== this.props.src) {
      this.state.loaded = false;

      this.loadImage(nextProps.src);
    }
  }

  loadImage(src) {
    if (src) {
      const image = new Image();

      image.onload = () => {
        this.setState({ loaded: true });
      };

      image.src = src;
    }
  }

  render() {
    const { loaded } = this.state;
    const { className, id, src, width, height, style } = this.props;
    const props = {};

    if (className) {
      props.className = className;
    }

    if (id) {
      props.id = id;
    }

    if (loaded) {
      return (
        <img
          src={src}
          style={{
            width,
            height,
            ...style,
          }}
        />
      );
    }

    return (
      <div className="spinner">
        <div className="bounce1" />
        <div className="bounce2" />
        <div className="bounce3" />
      </div>
    );
  }
}

Img.defaultProps = {
  id: "",
  className: "auto",
  height: "auto",
  width: "auto",
  src: "",
  style: {},
};

Img.propTypes = {
  id: PropTypes.string,
  className: PropTypes.string,
  height: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
  width: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
  src: PropTypes.string,
  style: PropTypes.object,
};
