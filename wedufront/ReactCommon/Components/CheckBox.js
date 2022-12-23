import React from "react";
import Style from '../../styles/css/ReactCommon/checkbox.module.css'
const CheckBox = (props) => {
  const { extraProps,checkBoxProps } = props;
  return (
    <>
      <div className={`${extraProps.parentcls ? extraProps.parentcls : "" ,Style.commonCheckBox} `}>
          <input {...checkBoxProps} />
          <label htmlFor={extraProps.checkFor} className={extraProps.labelClassName}>
              {extraProps.label}
          </label>
          
      </div>
    </>
  );
};
export default CheckBox
