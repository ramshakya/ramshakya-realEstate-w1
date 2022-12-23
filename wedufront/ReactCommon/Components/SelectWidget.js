import React, { useState, useEffect, useRef } from "react";
const SelectWidget = (props) => {
    const initiaProps = props.extraProps;
    const [values, setValues] = useState([]);
    const handleChange = (e) => {
        if(props.eventHandler){
            props.eventHandler(e)
        }
        setValues();     
      };
    return (
        <select aria-label="Default select" 
        onChange={handleChange} title={initiaProps.title} name={initiaProps.name}  id={initiaProps.id} className={initiaProps.className} >
            <option value="">{initiaProps.title}</option>
            {props.data && Array.isArray(props.data) && props.data.length > 0 && props.data.map((item, key) => {
                return (
                    <option value={item.value} key={key}>{item.key}</option>
                )
            })
            }
        </select>
        
    )
}
export default SelectWidget;