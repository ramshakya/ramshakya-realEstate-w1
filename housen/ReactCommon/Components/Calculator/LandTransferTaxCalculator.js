import React from "react";
const LandTransferTaxCalculator = (props) => {
    return (
        <>
            <div><h2
                className="fontStyle">
                Land Transfer Tax Calculator</h2>
                <div className="widget" data-widget="calc-payment" data-ltt="only" data-lang="en"></div>
                <div className="textCenter"><a href="https://www.ratehub.ca/"
                    className="contents"><img
                        src="https://www.ratehub.ca/assets/images/widget-logo.png" className="width100"
                        alt="Ratehub.ca logo" /></a></div>
                <script type="text/javascript" src="https://www.ratehub.ca/assets/js/widget-loader.js"></script>
            </div>

        </>
    )
}
export default LandTransferTaxCalculator