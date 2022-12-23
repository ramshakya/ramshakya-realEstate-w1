import React, { Component, useState, useEffect, useRef } from "react";
import MortgagePaymentCalculator from './MortgagePaymentCalculator'
import LandTransferTaxCalculator from './LandTransferTaxCalculator'
import Constants from './../../../constants/Global';

import { render } from "react-dom";
const Calculator = (props) => {
    const [chnageCal, setChnageCal] = useState(1)
    const [propTypeState, setPropTypeState] = useState(true)
    const [calData, setCalData] = useState("")
    
    const showResult = async (e) => {
    // function  showResult =>(e) {
        //console.log("ee", e);
        setChnageCal(e);
        await fetch(Constants.base_url+"api/calculators").then((response) =>
            response.text()).then((res) => JSON.parse(res))
            .then((json) => {
                console.log("=======json.total", json);
                setCalData(json.cal1);
            }).catch((err) => console.log({ err })
            );
    }
    function renderLandTransfer() {
        return (
            <LandTransferTaxCalculator />
       )
    }

return (
    <div className="  mt-2 m-3">
        <div className="row">
            <div className="col-sm-12 mortgage-calc-div divclass">
                <div className="row">
                    <div className="col-sm-12" id="paymentCalcu" title="123456" onClick={() => showResult(1)}>
                        <div id="MortgageTab" className="tabSection">
                            <div className="CalcIconCon colr mr-5">
                                <i className="fa fa-calculator"></i>
                            </div>
                            <div className="tabSectionText payment_calc_class activeDraw">
                                Payment Calculator</div>
                            <i className="fa fa-circle tabDot "></i>
                        </div>
                    </div>
                    <div hidden className="col-sm-3" id="landTransCalc" data-set="landTransCalc" onClick={() => showResult(2)}>
                        <div id="LandTransferTab" className="tabSection">
                            <div className="CalcIconCon colr">
                                <i className="fa fa-map-marker"></i>
                            </div>
                            <div id="" className="tabSectionText land_trans_class unactiveDraw">Land Transfer Tax Calculator</div>
                            <i className="fa fa-circle tabDot"></i>
                        </div>
                    </div>
                    <div hidden className="col-sm-3" id="affordabilityCalc" data-set="affordabilityCalc" onClick={() => showResult(3)}>
                        <div id="AffordabilityTab" className="tabSection">
                            <div className="CalcIconCon colr">
                                <i className="fa fa-rupee" aria-hidden="true"></i>
                            </div>
                            <div className="tabSectionText affordability_class unactiveDraw">Affordability Calculator</div>
                            <i className="fa fa-circle tabDot"></i>
                        </div>
                    </div>
                    <div  hidden className="col-sm-3" id="chmcCalc" data-set="chmcCalc" onClick={() => showResult(4)}>
                        <div id="AffordabilityTab" className="tabSection">
                            <div className="CalcIconCon colr">
                                <i className="fa fa-home"></i>
                            </div>
                            <div className="tabSectionText cmhc_class unactiveDraw">CMHC Calculator</div>
                            <i className="fa fa-circle tabDot"></i>
                        </div>
                    </div>
                    <div hidden className="col-sm-3 mt-3" id="compCalc" data-set="compCalc" onClick={() => showResult(5)}>
                        <div id="AffordabilityTab" className="tabSection">
                            <div className="CalcIconCon colr">
                                <i className="fa fa-rupee" aria-hidden="true"></i>
                            </div>
                            <div className="tabSectionText comp_class unactiveDraw">Mortgage rate comparison Calculator</div>
                            <i className="fa fa-circle tabDot"></i>
                        </div>
                    </div>
                </div>
                <div id="result" className="col-md-12 mt-3" >
                    <div className={chnageCal == 1 ? "showCls" : "hideCls"}>
                        <MortgagePaymentCalculator />
                    </div>
                    <div className={chnageCal == 2 ? "showCls" : "hideCls"}>
                    <div dangerouslySetInnerHTML={{ __html: calData }} />
                    </div>


                </div>
            </div>
        </div>
        <br />
    </div>
)
}
export default Calculator;