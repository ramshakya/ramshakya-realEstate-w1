import React from "react";
import Styles from "./../../styles/css/ReactCommon/ToggleSwitch.module.css"
const ToggleSwitch = (props) => {
    const extraProps=props.extraProps?props.extraProps:{};
    const handleOnChange = (e)=>{
    }
    return (
        <div className={Styles.container_switch}>
            <div className={Styles.toggleSwitch}>
                <input type="checkbox" className={Styles.checkbox}
                    name={props.label?props.label:""} id="toggle_able" onChange={props.callBack?props.callBack:handleOnChange} {...extraProps} />
                <label className={Styles.label} htmlFor="toggle_able">
                    <span className={Styles.inner} />
                    <span className={Styles.switch} />
                </label>
            </div>
        </div>
    );
};
export default ToggleSwitch;