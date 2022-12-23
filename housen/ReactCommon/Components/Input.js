import React,{useState,useEffect,useRef} from 'react'
import validateUtility from '../utility/validateUtility'
// import './input.css'
import Style from '../../styles/css/ReactCommon/input.module.css'

const Input = (props) => {
    let inputEl = null;
    // const inputEl = useRef(null);
    const { extraProps } = props;
    const initiaProps = props.props;
    const [inputError,setInputError]=useState(true);
    const [keyUp,setKeyUp]=useState(false);
    useEffect(() => {
        // console.log(props);
        if(inputEl!==null && keyUp===true){
            setInputError(extraProps.errorStatus)
            validateUtility.validate(inputEl,setInputError)
        }
    }, [extraProps.errorStatus,props.selectedValue])
    const handleKeyUp=(e)=>{
        // console.log(props);
        validateUtility.validate(e.target,setInputError)
        setKeyUp(true);
        if(initiaProps.onKeyUp){
            initiaProps.onKeyUp(e)
        }

    }
    return (

        <div className={`${extraProps.parentcls ? extraProps.parentcls : ""} `}>
            {extraProps.label &&
                <label className={extraProps.labelClassName?extraProps.labelClassName:""} dangerouslySetInnerHTML={{__html: extraProps.label}}></label>
             }
            <input
                {...initiaProps}
                data-inp-error-msg={extraProps.errorMsg?extraProps.errorMsg:""}
                data-inp-validation={extraProps.validation?extraProps.validation:""}
                data-gsf-name={initiaProps.name}
                onKeyUp={handleKeyUp}
                onKeyPress={(e) => validateUtility.stopDefault(e)}
                onChange={props.cb}
                value={props.selectedValue}
                ref={(node) => inputEl = node}
            /> 
            {!inputError &&
                <span className="err-inp-msg">
                    {extraProps.errorMsg}
                </span>
            }
            {extraProps.msgId &&
                <span className="err-inp-msg" id={extraProps.msgId}>
                    
                </span>
            }
           
            {props.children}
        </div>
    )
}
 export default Input;
