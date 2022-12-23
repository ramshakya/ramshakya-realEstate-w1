import React from "react";
// import  HtmlTag  from 'elements/HtmlTag';
import Style from "../../styles/css/ReactCommon/popup.module.css";
const Button = (props, context) => {
  let buttonProps = props.props;
  let extraProps = props.extraProps;
  let buttondisabled = props.showBtn === false ? "disabled" : "";
  if (typeof extraProps == "undefined") return null;

  return (
    <div
      className={`${
        extraProps.btnDivCls
          ? extraProps.btnDivCls
          : ""
      }`}
    >
      <button
        {...buttonProps}
        disabled={buttondisabled}
        className={`button ${props.showBtn ? "" : "btndisabled"} ${
          props.loaderCls ? props.loaderCls : ""
        } ${
          buttonProps.btnclass ? buttonProps.btnclass : ""
        } col-xs-12 col-md-12`}
        onClick={props.cb}
        name={buttonProps ? buttonProps.name : ""}
      >
        {extraProps.label}
      </button>
    </div>
  );
};

export default Button;
