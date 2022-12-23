import React from "react";

const MortgagePaymentCalculator = (props) => {
    return (
        <>
            <div className="paymentCalSection"><h2
                className="fontStyle">
                Mortgage payment calculator</h2>
                <div className="widget" data-widget="calc-payment" data-lang="en"></div>
                <div className="textCenter"><a href="https://www.ratehub.ca/"
                    className="contents"><img
                        src="https://www.ratehub.ca/assets/images/widget-logo.png" className="width100"
                        alt="Ratehub.ca logo" /></a></div>
            </div>
        </>
    )
}
export default MortgagePaymentCalculator