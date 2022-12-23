import React from "react";

const Button = (props) => {
  const extraProps = props.extraProps;  
  return (
    <div>
      <button
         {...extraProps}
      >{extraProps.text}
      </button>
    </div>
  );
};

export default Button;
