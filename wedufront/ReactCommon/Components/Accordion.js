import React, { useEffect } from "react";
import Autocomplete from "./AutoSuggestion";
const Accordion = (props) => {
  const downPayment = [
    {
      text: "5%",
      value: "5",
    },
    {
      text: "10%",
      value: "10",
    },
    {
      text: "15%",
      value: "15",
    },
    {
      text: "20%",
      value: "20",
    },
  ];
  const year = [
    {
      text: "10 Years",
      value: "10",
    },
    {
      text: "15 Years",
      value: "15",
    },
    {
      text: "20 Years",
      value: "20",
    },
    {
      text: "25 Years",
      value: "25",
    },
    {
      text: "30 Years",
      value: "30",
    },
  ];
  return (
    <section className="accorionSection">
      <input type="checkbox" value="1" id="state" hidden />
      <div className="content">
        <div className="drop-form">
          <a className="close-form dark">
            {" "}
            <label htmlFor="state">x</label>
          </a>
          <div className="row downPaymentSection">
            <div className="col-lg-3 col-sm-3 col-md-3 form-group">
              <label className="fontSize13">Price</label>
              <input
                type="text"
                id="main_price"
                placeholder={props.price ? props.price : "$0,00,000"}
                className="form-control"
                readOnly={true}
              />
            </div>
            <div className="col-lg-4 col-sm-4 col-md-4">
              <div className="form-group">
                <label className="fontSize13">Down Payment</label>
                <div className="inner-form">
                  <div className="row">
                    <div className="col-lg-12 col-sm-12 col-md-12 d-flex ">
                      <input
                        type="text"
                        placeholder={
                          props.downPaymentPrice
                            ? props.downPaymentPrice
                            : "$0,00,000"
                        }
                        className="form-control   fromController"
                        readOnly={true}
                        id="down_payment_input"
                      />
                      &nbsp;
                      <Autocomplete
                        inputProps={{
                          id: "down_payment_select",
                          name: "down_payment",
                          className:
                            "auto form-control auto-suggestion-inp form-control textEnd select-pad calc  fromController",
                          placeholder: "",
                          title: "",
                          readOnly: true,
                        }}
                        selectedText={
                          props.downPaymentPercent
                            ? props.downPaymentPercent + "%"
                            : "20%"
                        }
                        allList={downPayment}
                        cb={props.cbDownpayment}
                        extraProps={{}}
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div className="col-lg-2  col-md-2">
              <div className="form-group">
                <label className="fontSize13">Rate</label>
                <div className="inner-form">
                  <input
                    type="text"
                    id="rate_input"
                    placeholder={props.rate ? props.rate : "6"}
                    className="form-control rate_select"
                    onKeyUp={props.cbkeyUpHandler}
                    style={{ background: "#e9ecef !important" }}
                  />
                  <div className="custom-select1"></div>
                </div>
              </div>
            </div>
            <div className="col-lg-3  col-md-3">
              <div className="form-group">
                <label className="fontSize13">Amortization</label>
                <div className="inner-form">
                  <div className="row">
                    <div className="col-lg-12 col-sm-12 col-md-12 d-flex ">
                      <input
                        type="text"
                        id="year_input"
                        readOnly={true}
                        placeholder={props.term ? props.term : "25"}
                        className="form-control  fromController"
                      />
                      &nbsp;
                      <Autocomplete
                        inputProps={{
                          id: "year_select",
                          name: "year_select",
                          className:
                            "form-control select-pad calc textEnd fromController ",
                          placeholder: "",
                          title: "",
                          readOnly: true,
                        }}
                        selectedText={
                          props.term ? props.term + " Years" : "25 Years"
                        }
                        allList={year}
                        cb={props.cbAmortization}
                        extraProps={{}}
                      />
                      <div className="select-items select-hide"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="row align-items-end botm-form mt-3">
            <div className="col-lg-4">
              <div className="form-group">
                <label className="fontSize13">
                  CMHC/GE Premium (Maintainance)
                  <input
                    type="text"
                    readOnly={true}
                    placeholder={
                      props.CMHCMaintaince ? props.CMHCMaintaince : "$0"
                    }
                    id="maintainance"
                    className="form-control focusCls12"
                  />
                </label>
              </div>
            </div>
            <div className="col-lg-5">
              <div className="row">
                <div className="col-lg-7">
                  <div className="form-group">
                    <label className="fontSize13">
                      Mortgage Amount
                      <input
                        type="text"
                        id="mortgagae_amount"
                        placeholder={
                          props.mortgageAmount ? props.mortgageAmount : ""
                        }
                        readOnly={true}
                        className=" form-control"
                      />
                    </label>
                  </div>
                </div>
                <div className="col-lg-5">
                  <div className="form-group">
                    <label className="fontSize13">
                      Mortgage Payment
                      <input
                        id="mortgagae_payment"
                        readOnly={true}
                        placeholder={
                          props.mortgagePayment ? props.mortgagePayment : ""
                        }
                        className="font143 form-control"
                      />
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div className="col-lg-3">
              <div className="row">
                <div className="col-lg-6">
                  <div className="form-group">
                    <label className="fontSize13">Taxes</label>
                    <input
                      type="text"
                      readOnly={true}
                      placeholder={props.taxes ? props.taxes : "$2,231  /yr"}
                      className="form-control taxes"
                      style={{ width: "114%" }}
                    />
                  </div>
                </div>
                <div className="col-lg-6">
                  <div className="form-group">
                    <label className="fontSize13">Condo Fees</label>
                    <input
                      type="text"
                      readOnly={true}
                      placeholder={props.condoFee ? props.condoFee : "$0   /m"}
                      className="form-control condo_fees"
                      style={{
                        width: "11% !important",
                        marginLeft: "-7px !important",
                      }}
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="form-btns mt-4">
            <a>
              Total Monthly Payment{" "}
              <span id="final_mortgagae_amount">
                {" "}
                {"   " + props.totalMonthlyPayment}
              </span>
            </a>{" "}
            <a className="get-btn">Get Pre-Approved</a>
          </div>
          <div className="form-btns mt-4">
            <p className="mt-3">
              <span className="font14 ">Disclaimer: </span>
              <span className="font12 ">
                All calculations are hypothetical and for illustrative purpose
                only.We strongly recommend you to consult with mortgage
                professionals for personalized financial advice.
              </span>
            </p>
          </div>
        </div>
      </div>
    </section>
  );
};
export default Accordion;
