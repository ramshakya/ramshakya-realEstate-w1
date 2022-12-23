import React, { useEffect, useState } from "react";
import Calculator1 from "../../components/Cal/Calculator1";
import Constants from "../../constants/Global";

const Calculator = (props) => {
    
    props.setMetaInfo(Constants.pageMeta.calculator);
    return (
        <>
            <Calculator1 {...props} />
        </>
    );
};
export default Calculator;