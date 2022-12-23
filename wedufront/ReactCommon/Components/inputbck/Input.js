import React,{useState,useEffect} from 'react'
import validateUtility from '../utility/validateUtility'
// import './input.css'
import Style from '../../styles/css/ReactCommon/input.module.css'

const Input = (props) => {
    const { extraProps } = props;
    const initiaProps = props.props;
    const [inputError,setInputError]=useState(false);
    useEffect(() => {
        setInputError(extraProps.errorStatus)
        
    }, [extraProps.errorStatus])
    const handleKeyUp=(e)=>{
        // console.log("====>hendle",this);
        validateUtility.validate(e.target,setInputError)
        if(initiaProps.onKeyUp){
            initiaProps.onKeyUp(e)
        }

    }
    

    // console.log("initiaProps",initiaProps);
    return (

        <div className={`${extraProps.parentcls ? extraProps.parentcls : "" ,Style.inputClass}  `}>
            <input
                {...initiaProps}
                data-inp-error-msg={extraProps.errorMsg?extraProps.errorMsg:""}
                data-inp-validation={extraProps.validation?extraProps.validation:""}
                onKeyUp={handleKeyUp}
                onKeyPress={(e) => validateUtility.stopDefault(e)}
            /> 
            {inputError&&
                <span className="err-inp-msg">
                    {extraProps.errorMsg}
                </span>
            }
            {props.children}
        </div>
    )
}
 export default Input;
