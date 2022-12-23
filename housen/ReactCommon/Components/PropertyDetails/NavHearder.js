import React, { useState, useEffect, useRef } from "react";
import BreadCrumbs from "./BreadCrumbs";
import { Row, Col, Modal, Button } from "react-bootstrap";
import Styles from "../../../styles/css/ReactCommon/propertydetail.module.css";
import printSvg from "./../../../public/images/icon/pdf.svg";
import star from "./../../../public/images/icon/star.svg";
import share from "./../../../public/images/icon/share.svg";
import Tooltip from "./../Tooltip";
const NavHeader = (prop) => {
  const props = prop.props;
  const shareLink = props.shareLink;
  const details = props.details ? props.details.details : {};
  // console.log("NavHeader===>>1", details);
  const [name, setName] = useState(false);
  const [email, setEmail] = useState(false);
  const [emails, setEmails] = useState(false);
  const [message, setMessage] = useState(false);
  const [modalShow, setModalState] = useState(false);
  let data =[];
  useEffect(() => {
    

  }, [false]);
  function fbook() {
    window.open(
      `https://www.facebook.com/share.php?u=${shareLink}`,
      "Facebook",
      "width=650,height=500"
    );
  }
  function twitter() {
    window.open(
      `https://twitter.com/intent/tweet?text=${shareLink}`,
      "Twitter",
      "width=650,height=500"
    );
  }
  function pinterest() {
    window.open(
      `https://pinterest.com/pin/create/button/?url=${shareLink}`,
      "Pinterest",
      "width=650,height=500"
    );
  }
  function shareEmail(prop) {
    setModalState(prop);
  }
  function print() {
    window.print();
  }
  function sendDetails(e){
    
  }
  function checkValidate(e){
    console.log("checkValidate==>>",e.target.value);
    if(e.target.name==="sender_name"){
      setName(e.target.value?false:true);
    }
    if(e.target.name==="sender_email" && !e.target.value){
      setEmail(e.target.value?false:true);
    }
    if(e.target.name==="message" && !e.target.value){
      setMessage(e.target.value?false:true);
    }
    if(e.target.name==="email" ){
      setEmails(e.target.value?false:true);
    }
  }
 function handleChanges(e){
  data[e.target.name] = e.target.value;
  console.log("handleChanges",e.target.name);
 }

  function MydModalWithGrid(props) {
    return (
      <div className="">
        <Modal
          {...props}
          className="emailShareModel"
          size="lg"
          aria-labelledby="contained-modal-title-vcenter"

        >
          <Modal.Header closeButton>
            <Modal.Title id="contained-modal-title-vcenter">
              Share Listings
          </Modal.Title>
          </Modal.Header>
          <Modal.Body>
            <div className="popup_propform" id="modalEmailForm" >
              <div className="row" id="2">
                <div className="col-md-6 col-sm-6 col-lg-6 form-group ">
                  <label className="">Your Name*</label>
                  <input type="text" onChange={handleChanges} on onBlur={checkValidate}  className=" form-control senderName input-box" placeholder="Name*" name="sender_name" required="" />
                  <span className={`validateError  ${name?"":"hide"}`} >Name is required.</span>
                </div>
                <div className="form-group col-md-6 col-sm-6 col-lg-6">
                  <label className="">Your Email*</label>
                  <input type="email" onChange={handleChanges} on onBlur={checkValidate} className="  form-control senderEmail input-box" placeholder="Email*" name="sender_email" required="" />
                  <span className={`validateError  ${email?"":"hide"}`} >Email is required.</span>
                </div>
                <div className="form-group col-md-12 col-sm-12 col-lg-12 mt-3">
                  <label className="">Your Friends Emails*</label>
                  <input type="email" onChange={handleChanges} on onBlur={checkValidate} className=" form-control recipentEmail input-box" placeholder="Emails*" name="email" required="" />
                  <span className={`validateError  ${emails?"":"hide"}`} >Emails are required.</span>
                </div>

                <div className="form-group col-lg-12 mt-3">
                  <label className="">Message*</label>
                  <textarea onChange={handleChanges} on onBlur={checkValidate} className="form-control input-box" name="message" style={{ "height": "66px" }} placeholder={`I would like to get more info about ${details.Addr}`}></textarea>
                  <span className={`validateError  ${message?"":"hide"}`} >Message is required.</span>
                </div>
                <input type="hidden" name="property_url" value={props.shareLink} id="property_url" />
                <input type="hidden" name="property_id" value={details.id} />
                <input type="hidden" name="property_mls_no" value={details.Ml_num} />
                <input type="hidden" name="page_from" value="property details share email form" />
                <div className="col-md-4">
                </div>
                <div className="col-md-4">
                  <div className="shareEmail">
                    <button id="shareEmailBtn" type="btn" className="btn showSchedule btn-sm mt-4" onClick={sendDetails} > <i className="fa fa-spinner fa-spin"> </i>Submit</button>
                  </div>
                </div>
                <div className="col-md-4">
                </div>
              </div>
            </div>
          </Modal.Body>
        </Modal>
      </div>
    );
  }

  return (
    <>
      {/*  */}

      <div className="container-fluid ">
        <div className="row">
          <div className="col-md-6 col-lg-6 col-sm-12 col-xs-12 ml-1">
            <BreadCrumbs listItems={props.breadcrumb} callBackHandle={props} />
            <MydModalWithGrid show={modalShow} onHide={() => shareEmail(false)} />

          </div>
          <div className="col-sm-6 col-xs-12 col-md-6 col-lg-6 ">
            <div className="displayFlex">
              <div className="col-md-6 col-sm-6 col-lg-6"></div>
              <div className="col-md-2 col-sm-2 col-lg-2">
                <img className="navImgs" {...star} title="Add Favourite" />
              </div>
              <div className="col-md-2 col-sm-2 col-lg-2">
                <img
                  className="navImgs"
                  {...printSvg}
                  title="Save PDF or Print"
                  onClick={print}
                />
              </div>
              <div className="col-md-2  col-sm-2 col-lg-2  share ">
                <img
                  className="navImgs"
                  {...share}
                  title="Share"
                  className="shareLinks"
                />
                <div className="dropdown-menu show" x-placement="bottom-start">
                  <a className="dropdown-item fb-share mt-1" onClick={fbook}>
                    <i className="fa fa-facebook share-facebook"></i> Facebook
                  </a>
                  <a className="dropdown-item" onClick={twitter}>
                    <i className="fa fa-twitter share-twitter"></i> Twitter
                  </a>
                  <a className="dropdown-item" onClick={pinterest}>
                    <i className="fa fa-pinterest share-pinterest"></i> Pinterest
                  </a>
                  <a className="dropdown-item mb-1" onClick={() => shareEmail(true)}>
                    <i className="fa fa-envelope share-email"></i> Email
                  </a>
                </div>

                {/* <Tooltip
                                message="Test"
                                position="Top"
                                children="children"
                                >TEST </Tooltip> */}
              </div>
              <div></div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default NavHeader;
