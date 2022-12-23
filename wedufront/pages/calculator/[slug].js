import React, { useEffect, useState } from "react";
import Calculator1 from "../../components/Cal/Calculator1";
import Constants from "../../constants/GlobalConstants";

const Calculator = (props) => {
    props.setMetaInfo(Constants.pageMeta.mortgage);
    return (
            <Calculator1 {...props} />
    );
};
export default Calculator;