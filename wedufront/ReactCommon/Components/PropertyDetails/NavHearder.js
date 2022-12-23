import React, { useState, useEffect, useRef } from "react";
import BreadCrumbs from "./BreadCrumbs";
import 'react-toastify/dist/ReactToastify.css';
import { ToastContainer, toast } from 'react-toastify';
import { Row, Col, Modal, Button } from "react-bootstrap";
import printSvg from "./../../../public/images/icons/pdf.svg";
import share from "./../../../public/images/icons/share.svg";
import API from "./../../utility/api";
import { shareEmailApi, agentId, favUrl } from "../../../constants/GlobalConstants";
import emptyHeart from "../../../public/images/icons/empty_heart.svg";
import fillHeart from "../../../public/images/icons/heartFill.svg";
const NavHeader = (prop) => {
  const props = prop.props;
  const isLogin = prop.isLogin;
  const shareLink = props.shareLink;
  const details = props.details ? props.details.details : {};
  const [name, setName] = useState(false);
  const [email, setEmail] = useState(false);
  const [emails, setEmails] = useState(false);
  const [message, setMessage] = useState(false);
  const [showBtn, setShowBtn] = useState(true);
  const inputDivRef = useRef(null);
  const [duplicateEmails, setDuplicateEmails] = useState(false);
  const [loaderState, setLoaderState] = useState(false);
  const [modalShow, setModalState] = useState(false);
  const [nameVal, setNameVal] = useState();
  const [emailVal, setEmailVal] = useState();
  const [emailsVal, setEmailsVal] = useState();
  const [messageVal, setMessageVal] = useState();
  const [favIconImg, setfavIconImg] = useState(emptyHeart);
  const [showShare, setShowShare] = useState(false);
  function fbook() {
    window.open(
      `https://www.facebook.com/share.php?u=${shareLink}`,
      "Facebook",
      "width=650,height=500"
    );
    setShowShare(false);
  }
  function twitter() {
    window.open(
      `https://twitter.com/intent/tweet?text=${shareLink}`,
      "Twitter",
      "width=650,height=500"
    );
    setShowShare(false);
  }
  function pinterest() {
    window.open(
      `https://pinterest.com/pin/create/button/?url=${shareLink}`,
      "Pinterest",
      "width=650,height=500"
    );
    setShowShare(false);
  }
  function showShareBox(params) {
    setShowShare(!showShare);
  }
  useEffect(() => {
    document.addEventListener("click", handleOuterClick);
  }, []);
  const handleOuterClick = e => {
    if (inputDivRef !== null && inputDivRef.current !== null && !inputDivRef.current.contains(e.target)) {
      setShowShare(false);
    }
  }
  function shareEmail(state) {
    if (!isLogin) {
      prop.loginPop();
      return;
    }
    setModalState(state);
  }
  function print() {
    window.print();
  }
  function sendDetails(e) {
    if (nameVal && emailVal && emailsVal && messageVal && !duplicateEmails) {
      setShowBtn(false);
    } else {
      setShowBtn(true);
      return;
    }
    setLoaderState(true);
    setShowBtn(true);
    let data = {
      name: nameVal,
      email: emailVal,
      emails: emailsVal,
      message: messageVal,
      page_from: "property details share email form",
      property_url: props.shareLink,
      property_id: details.id,
      property_mls_no: details.Ml_num,
      details: details,
      agentId: agentId
    };
    let uri = "http://127.0.0.1:8000/api/v1/services/shareEmail"
    uri = shareEmailApi
    API.jsonApiCall(uri, data, "POST", null, {
      "Content-Type": "application/json",
    }).then((res) => {
      if (res.status == 200) {
        toast.success("Submit Successfully");
        shareEmail(false);
        setShowBtn(true);
        setLoaderState(false);
        setNameVal("");
        setEmailVal("");
        setEmailsVal("");
        setMessageVal("");
      }
    })
      .catch((e) => {
        toast.error("Something went wrong try later!");
        this.setState({
          dataFlag: false
        });
      });
  }
  function handleChanges(e) {
    setLoaderState(false);
    if (e.target.name === "sender_name") {
      setNameVal(e.target.value);
      setName(e.target.value ? false : true);
    }
    if (e.target.name === "sender_email") {
      if (!validateEmail(e.target.value)) {
        setShowBtn(false);
        setEmail(true);
        setEmailVal("");
        return;
      } else {
        setEmail(false);
        setEmailVal(e.target.value);
      }
    }
    if (e.target.name === "message") {
      setMessageVal(e.target.value);
      setMessage(e.target.value ? false : true);
    }
    if (e.target.name === "email") {
      let emails = e.target.value;
      let errCount = 0;
      let duplicateCount = 0;
      emails = emails.replace(" ", "");
      emails = emails.split(',');
      let newEmails = [];
      emails.forEach(mails => {
        if (mails) {
          if (!validateEmail(mails)) {
            errCount++;
          }
          if (!newEmails.includes(mails)) {
            newEmails.push(mails);
          } else {
            setShowBtn(true);
            duplicateCount++;

          }
        }
      });
      if (duplicateCount !== 0) {
        setDuplicateEmails(true);
        return;
      } else {
        setDuplicateEmails(false);
      }
      if (errCount !== 0) {
        setShowBtn(true);
        setEmails(true);
        setEmailsVal("");
        return;
      } else {
        setEmails(false);
        setEmailsVal((emails));
      }
    }
    setTimeout(() => {
      if (nameVal && emailVal && emailsVal && messageVal && !duplicateEmails) {

        setShowBtn(false);
      } else {
        setShowBtn(true);

      }
    }, 150);
  }
  function validateEmail(e) {
    return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(e)
  }

  // favourite
  const favorite = (e) => {
    let userData = localStorage.getItem("userDetail");
    if (!userData) {
      prop.loginPop();
      return true;
    }
    if (
      !localStorage.getItem("login_token") &&
      props.openUserPopup &&
      props.openLoginCb &&
      !userData
    ) {
      props.openLoginCb();
      return true;
    }
    let token = localStorage.getItem("login_token");
    userData = userData ? JSON.parse(localStorage.getItem("userDetail")) : null;
    const indexArr = userData.favourite_properties.indexOf(details.Ml_num)
    const reqBody = {
      LeadId: userData.login_user_id,
      AgentId: agentId,
      ListingId: details.Ml_num,
      Fav: indexArr === -1 ? 1 : 0,
    };
    const headers = {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    };
    API.jsonApiCall(favUrl, reqBody, "post", null, headers).then((res) => {
      if (reqBody.Fav === 1) {
        userData.favourite_properties.push(details.Ml_num)
        setfavIconImg(fillHeart);
      } else {
        const favArr = userData.favourite_properties;
        favArr.splice(indexArr, 1);
        userData.favourite_properties = favArr;
        setfavIconImg(emptyHeart);
      }
      localStorage.setItem("userDetail", JSON.stringify(userData))
      if (props.checkFavApiCall) {
        props.checkFavApiCall(reqBody);
      }
    });
  };
  useEffect(() => {
    let userData = localStorage.getItem("userDetail");
    userData = userData ? JSON.parse(localStorage.getItem("userDetail")) : null;
    if (userData && userData !== null && userData !== "undefined" && userData.favourite_properties.indexOf(details.Ml_num) !== -1) {
      setfavIconImg(fillHeart);
    }
  }, [details]);
  return (
      <div className="container-fluid pt-3">
        <div className="row">
          <div className="col-md-6 col-lg-6 col-sm-12 col-xs-12 ml-1">
            <BreadCrumbs listItems={props.breadcrumb} callBackHandle={props} />
            <div className="">
              <Modal
                show={modalShow} onHide={() => shareEmail(false)}
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
                        <input type="text" onChange={handleChanges} onBlur={handleChanges} className=" form-control senderName input-box" placeholder="Name*" name="sender_name" required="" />
                        <span className={`validateError  ${name ? "" : "hide"}`} >Name is required.</span>
                      </div>
                      <div className="form-group col-md-6 col-sm-6 col-lg-6">
                        <label className="">Your Email*</label>
                        <input type="email" onChange={handleChanges} onBlur={handleChanges} className="  form-control senderEmail input-box" placeholder="Email*" name="sender_email" required="" />
                        <span className={`validateError  ${email ? "" : "hide"}`} >Email is required.</span>
                      </div>
                      <div className="form-group col-md-12 col-sm-12 col-lg-12 mt-3">
                        <label className="">Your Friends Emails*</label>
                        <input type="email" onChange={handleChanges} onBlur={handleChanges} className=" form-control recipentEmail input-box" placeholder="Emails*" name="email" required="" />
                        <span className={`validateError  ${emails ? "" : "hide"}`} >Emails are required.</span>
                        <span className={`validateError  ${duplicateEmails ? "" : "hide"}`} >Remove duplicate emails</span>

                      </div>

                      <div className="form-group col-lg-12 mt-3">
                        <label className="">Message*</label>
                        <textarea onChange={handleChanges} onBlur={handleChanges} className="form-control input-box height-66" name="message"   placeholder={`I would like to get more info about ${details.Addr}`}></textarea>
                        <span className={`validateError  ${message ? "" : "hide"}`} >Message is required.</span>
                      </div>
                      <div className="col-md-4">
                      </div>
                      <div className="col-md-4">
                        <div className="shareEmail">
                          {!loaderState ? <>
                            <button id="shareEmailBtn" disabled={showBtn || message} type="btn" className="btn showSchedule btn-sm mt-4" onClick={sendDetails} > <i className="fa fa-spinner fa-spin"> </i>Submit</button>
                          </> : <>
                            <button id="shareEmailBtn" disabled={true} type="btn" className="btn showSchedule btn-sm mt-4" onClick={sendDetails} > <i className="fa fa-spinner fa-spin"> </i>Submiting......</button>
                          </>
                          }
                        </div>
                      </div>
                      <div className="col-md-4">
                      </div>
                    </div>
                  </div>
                </Modal.Body>
              </Modal>
            </div>
          </div>
          <div className="col-sm-6 col-xs-12 col-md-6 col-lg-6 ">
            <div className="displayFlex">
              <div className="col-md-6 col-sm-6 col-lg-6"></div>
              <div className="col-md-2 col-sm-2 col-lg-2">
                <img
                  {...favIconImg}
                  height="33"
                  width="33"
                  onClick={favorite}
                  alt="fav-img"
                  title="fav-img"
                  className="favicon hoverAble"
                />
              </div>
              <div className="col-md-2 col-sm-2 col-lg-2">
                <img
                  className="navImgs"
                  {...printSvg}
                  title="Save PDF or Print"
                  alt="Save PDF or Print"
                  onClick={print}
                />
              </div>
              <div className="col-md-2  col-sm-2 col-lg-2  share position-relative" ref={inputDivRef}>
                <img
                  {...share}
                  title="Share"
                  alt="Share"
                  className="shareLinks navImgs"
                  onClick={showShareBox}
                  
                />
                <div className={`dropdown-menu mt-1 show ${showShare?"d-block":"d-none"}`} x-placement="bottom-start">
                  <a className="dropdown-item fb-share mt-1 p-2" onClick={fbook}>
                    <i className="fa fa-facebook share-facebook"></i>  Facebook
                  </a>
                  <a className="dropdown-item p-2" onClick={twitter}>
                    <i className="fa fa-twitter share-twitter share-facebook "></i> Twitter
                  </a>
                  <a className="dropdown-item p-2" onClick={pinterest}>
                    <i className="fa fa-pinterest share-pinterest share-facebook"></i> Pinterest
                  </a>
                  <a className="dropdown-item mb-1 p-2" onClick={() => shareEmail(true)}>
                    <i className="fa fa-envelope share-email share-facebook"></i> Email
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  );
};
export default NavHeader;