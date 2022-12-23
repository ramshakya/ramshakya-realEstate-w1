import Link from "next/link";
import React from "react";
import Style from "../../styles/css/ReactCommon/popup.module.css";
import Facebook from "./facebook";
import Google from "./google";
import { useEffect, useState } from "react";
import signUpLogo from "./../../public/images/logo/Housen.ca.png";
const Popup = (props) => {
  const [logo, setLogo] = useState("");
  const [banner, setBanner] = useState("../images/search_background.jpg");
  const [webSettingsFlag, setWebSettingsFlag] = useState(false);
  const [webSettings, setWebSettings] = useState(null);

  useEffect(() => {
    try {
      window.FB.logout();
    } catch (e) {}
    try {
      let websetting = localStorage.getItem("websetting");
      if (
        websetting &&
        websetting !== null &&
        websetting !== "undefined" &&
        websetting !== undefined
      ) {
        websetting = JSON.parse(websetting);
        setWebSettings(websetting);
        setWebSettingsFlag(true);
      }
    } catch (error) {}
  }, [webSettingsFlag]);
  useEffect(() => {
    let websetting = localStorage.getItem("websetting");
    if (
      websetting &&
      websetting !== null &&
      websetting !== "undefined" &&
      websetting !== undefined
    ) {
      try {
        let websetting = JSON.parse(websetting);
        if (websetting) {
          setLogo(websetting.UploadLogo);
        }
      } catch (error) {}
    }
  }, []);
  useEffect(() => {
    document.body.addEventListener("click", handleModelClick, true);
  }, []);
  function handleModelClick(e) {
    // props.showmenu(e);
    if (e.target.id == "LoginPopupModal") {
      props.handleClose();
    }
  }
  return (
    <div
      className={`${
        (props.parentcls ? props.parentcls : "", Style.commonPopup)
      }  `}
      id="LoginPopupModal"
    >
      <div className={Style.box}>
        <div className="row">
          <div
            className="col-md-5 popupbackground"
            style={{ "background-image": "url(" + banner + ")" }}
          ></div>
          <div className="col-md-7 form-content">
            <span
              className={`${Style.closeIcon} popcloseBtn`}
              id="closeBtn"
              onClick={props.handleClose}
            >
              <i className="fa fa-times-circle-o"></i>
            </span>
            <center>
              <img {...signUpLogo} className="loginLogo loginLogo2 mb-5" />
            </center>
            {props.children}
            {webSettings !== null && (
              <>
                <Google websetting={webSettings} />
                <Facebook websetting={webSettings} />
              </>
            )}
          </div>
        </div>

        {/* <p className="text-center mt-3 mb-0 font-normal">I accept Terms of Use and Privacy Policy</p>
					<p className= "text-center mt-1 font-normal inline-block">Don't have an account? <a href="#" onClick={}  className="signup-link">Sign up here </a></p> */}
      </div>
    </div>
  );
};
export default Popup;
