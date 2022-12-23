import Link from "next/link";
import Constants from "../../constants/GlobalConstants";
import API from "../../ReactCommon/utility/api";
const extra_url = Constants.extra_url;
const APP_NAME = Constants.APP_NAME;
import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import Loader from "./../loader/loader";
import { useState } from "react";
const Footer = (props) => {
  const {
    webEmail,
    phoneNo,
    websiteAddress,
    facebook,
    twitter,
    linkedin,
    instagram,
    youtube,
    websiteName,
  } = props;
  const [dataFlag, setDataFlag] = useState(false);
  const [nameValue, setNameValue] = useState("");
  const handleSubmit = (event) => {
    setDataFlag(true);
    event.preventDefault();
    const name = event.target.name;
    const email = event.target.email;
    const message = event.target.message;
    const current_url = window.location.href;
    const current_page = window.location.pathname;
    const body = JSON.stringify({
      Name: name.value,
      Email: email.value,
      Message: message.value,
      Url: current_url,
      Page: current_page,
      AgentId: Constants.agentId,
    });
    API.jsonApiCall(extra_url + "global/ContactEnquiry", body, "POST", null, {
      "Content-Type": "application/json",
    }).then((res) => {
      setDataFlag(false);
      if (res.success) {
        toast.success(res.success);
        name.value = "";
        email.value = "";
        message.value = "";
      } else {
        toast.error(res.errors);
      }
    });
  };
  return (
    <main>
      {/*  Contact section area start   */}
      <section className="contact-section">
        {dataFlag && <Loader />}
        <div className="container">
          <div className="row">
            <div className="col-md-4 col-lg-6 box-col">
              <div className="title mb-4 m-left-15">
                <h1 className="text-bold h2">Contact Us</h1>
              </div>
              <ul className="footerInfo m-left-15">
                <li>
                  <img
                    src="../images/social-icon/mail.png"
                    alt="icons"
                    className="iconImage"
                    width="25"
                    height="25"
                  />{" "}
                  <a href={"mailto:" + webEmail} className="text-dark">
                    {webEmail}
                  </a>
                </li>
                {Constants.agentInfo.mobileNumber === phoneNo ? (
                  <></>
                ) : (
                  <li>
                    <img
                      src="../images/social-icon/phone.svg"
                      alt="icons"
                      className="iconImage"
                      width="25"
                      height="25"
                    />{" "}
                    <a href={"tel:" + phoneNo} className="text-dark">
                      {" "}
                      +1 {phoneNo}
                    </a>
                  </li>
                )}
                <li>
                  <img
                    src="../images/social-icon/user.png"
                    alt="icons"
                    className="iconImage"
                    width="25"
                    height="25"
                  />{" "}
                  <span className="text-dark">{Constants.agentInfo.name}</span>{" "}
                  <small style={{ fontSize: "15px" }}>
                    {" "}
                    {Constants.agentInfo.title}{" "}
                  </small>
                </li>
                <li>
                  <img
                    src="../images/social-icon/phone.svg"
                    alt="icons"
                    className="iconImage"
                    width="25"
                    height="25"
                  />{" "}
                  <span className="text-dark">
                    <a
                      href={"tel:" + Constants.agentInfo.mobileNumber}
                      className="text-dark"
                    >
                      +1 {Constants.agentInfo.mobileNumber}
                    </a>
                  </span>
                </li>
                <li>
                  <img
                    src="../images/social-icon/marker.png"
                    alt="icons"
                    className="iconImage"
                    width="25"
                    height="25"
                  />{" "}
                  <span className="contact-addr text-dark">
                    {websiteAddress}
                  </span>
                </li>
              </ul>

              {/*  social media  */}
              <span className="m-left-15">Social Media</span>
              <ul className="mb-0 footer-social-media m-left-15">
                {facebook && (
                  <li>
                    <Link href={facebook}>
                      <a className="link">
                        <img
                          src="../images/social-icon/facebook.svg"
                          height={30}
                          width={30}
                          alt="facebook-icon"
                        />
                      </a>
                    </Link>
                  </li>
                )}
                {twitter && (
                  <li>
                    <Link href={twitter}>
                      <a className="link">
                        <img
                          src="../images/social-icon/twitter.svg"
                          height={30}
                          width={30}
                          alt="twitter-icon"
                        />
                      </a>
                    </Link>
                  </li>
                )}
                {linkedin && (
                  <li>
                    <Link href={linkedin}>
                      <a className="link">
                        <img
                          src="../images/social-icon/linkedin.svg"
                          height={30}
                          width={30}
                          alt="linkedin-icon"
                        />
                      </a>
                    </Link>
                  </li>
                )}
              </ul>
            </div>
            <div className="col-md-8 col-lg-6 form-col box-col">
              {/*  contact form start   */}
              <div className="contact-form-wrapper">
                <h2 className="h2">What Do You Want To Ask</h2>
                <form onSubmit={handleSubmit}>
                  {/*  single input   */}
                  <div className="mb-4">
                    <input
                      type="name"
                      className="form-control"
                      placeholder="Your name"
                      name="name"
                      required
                      autoComplete="off"
                    />
                  </div>
                  {/*  single input   */}
                  <div className="mb-4">
                    <input
                      type="email"
                      className="form-control"
                      placeholder="Your Email Address"
                      name="email"
                      required
                      autoComplete="off"
                    />
                  </div>
                  {/*  single input   */}
                  <div className="mb-4">
                    <textarea
                      placeholder="Your message"
                      name="message"
                      className="form-control"
                      cols="20"
                      rows="4"
                      required
                    ></textarea>
                  </div>
                  <button
                    type="submit"
                    className="common-btn form-btn pt-3 pb-3"
                  >
                    Send Now
                  </button>
                </form>
              </div>
              <div className="form-bg"></div>
            </div>
          </div>
        </div>
        <div className="container-fluid footer-credits">
          <div className="row">
            <div className="col-md-4 col-lg-4">
              <p className="copyright-website">
                {APP_NAME} - All rights reserved
              </p>
            </div>
            <div className="col-md-4 col-lg-4">
              <ul className="footer-links">
                <li>
                  <Link href="/privacy_policy">
                    <a className="text-white">Privacy Policy</a>
                  </Link>
                </li>
                <li>
                  <Link href="/Term&conditions">
                    <a className="text-white">Terms & Conditions</a>
                  </Link>
                </li>
              </ul>
            </div>
            <div className="col-md-4 col-lg-4">
              <ul className="footer-social">
                {instagram && (
                  <li>
                    <Link href={instagram}>
                      <a className="link">
                        <img
                          src="../images/social-icon/instagram.svg"
                          height={30}
                          width={30}
                          alt="instagram-icon"
                        />
                      </a>
                    </Link>
                  </li>
                )}
                {youtube && (
                  <li>
                    <Link href={youtube}>
                      <a className="link">
                        <img
                          src="../images/social-icon/youtube.svg"
                          height={30}
                          width={30}
                          alt="youtube-icon"
                        />
                      </a>
                    </Link>
                  </li>
                )}
              </ul>
            </div>
          </div>
        </div>
      </section>
      {/*   Contact section area end   */}
    </main>
  );
};
export default Footer;
