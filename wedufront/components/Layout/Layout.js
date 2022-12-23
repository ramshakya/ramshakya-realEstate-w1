import React, { useState, useEffect, useRef } from "react";
import NavigationBar from "../Navbar/NavigationBar";
import Footer from "../Footer/Footer";
import Head from "./Header";
import { ToastContainer, toast } from "react-toastify";
import API from "../../ReactCommon/utility/api";
import Constants from "../../constants/GlobalConstants";
import Visits from "../UserActivity/Visits";
import { useRouter } from "next/router";
import WebSetting from "./../../public/json/websetting.json";
// import metaJson from "./../../public/json_data/meta.json";
let metaJson = [];
export default function Layout(props) {
  const extra_url = Constants.extra_url;
  const pageMeta = Constants.pageMeta;
  const router = useRouter();
  const { children, metaInfo } = props;
  const [logo, setLogo] = useState("");
  const [logoAlt, setLogoAlt] = useState("");
  const [favicon, setFavicon] = useState("");
  const [seoTitle, setSeoTitle] = useState("wedu");
  const [webEmail, setWebEmail] = useState("");
  const [phoneNo, setPhoneNo] = useState("");
  const [websiteAddress, setWebsiteAddress] = useState("");
  const [facebook, setFacebook] = useState("#");
  const [twitter, setTwitter] = useState("#");
  const [linkedin, setLinkedin] = useState("#");
  const [instagram, setInstagram] = useState("#");
  const [youtube, setYoutube] = useState("#");
  const [websiteName, setWebsiteName] = useState("wedu.ca");
  const [seoDetails, setSeoDetails] = useState({});
  const [webSettingDetail, setWebSettingDetail] = useState({});
  const [websiteTitle, setWebsiteTitle] = useState("Best Website in GTA");
  const [GoogleMapApiKey, setGoogleMapApiKey] = useState("");
  const [isTest, setTest] = useState(false);
  const [webSettingData, setWebSettingData] = useState({});
  const [defaultMeta, setDefaultMeta] = useState({});

  useEffect(() => {
    const head = document.head;
    let script = document.getElementById("gatag");
    if (!script) {
      script = document.createElement("script");
      script.src = "https://www.googletagmanager.com/gtag/js?id=AW-984585488";
      script.id = "gatag";
      script.onload = () => {
        window.dataLayer = window.dataLayer || [];
        function gtag() {
          dataLayer.push(arguments);
        }
        gtag("js", new Date());
        gtag("config", "AW-984585488");
      };
      head.appendChild(script);
    }
  }, [metaInfo.title]);

  useEffect(() => {
    let title = metaInfo.title;
    let pageTitle = title;
    let logingPop = false;
    if (logingPop) {
      if (props.isOpen) {
        props.togglePopUp();
      }
    }
    if (title == "Home" || title == "home") {
      pageTitle = "home";
      logingPop = true;
      setDefaultMeta(pageMeta.home);
    }
    if (
      title == "PropertyDetails" ||
      title == "property details" ||
      title == "propertydetails"
    ) {
      pageTitle = "property details";
      logingPop = true;
      setDefaultMeta(
        metaInfo.isRent ? pageMeta.listingPage : pageMeta.listingPage1
      );
    }
    if (title == "ProfilePage") {
      pageTitle = "profile";
      logingPop = true;
      setDefaultMeta(pageMeta.advanceSearch);
    }
    if (title == "advance search" || title == "Map" || title == "Listings") {
      pageTitle = "advance search";
      logingPop = true;
      setDefaultMeta(pageMeta.advanceSearch);
    }
    if (title == "Calculators") {
      pageTitle = "calculators";
      logingPop = true;
      setDefaultMeta(pageMeta.mortgage);
    }
    // mortgage
    if (title == "SetPassword") {
      pageTitle = "set password";
      logingPop = true;
      let ob = {
        keyword: "Set-Password",
        title: "Reset Password | Wedu",
        description: "Reset your account password!",
        imgAlt: "Reset",
        url: "Reset",
      };
      setDefaultMeta(ob);
    }
    if (window.location.pathname == "/") {
      // console.log("WebSetting", WebSetting.websetting);
      pageTitle = "home";
      logingPop = true;
      setDefaultMeta(pageMeta.home);
    }
  }, [metaInfo.title]);
  useEffect(() => {
    let urlSearch = window.location.origin;
    if (props.isOpen) {
      props.togglePopUp();
    }
    let title = metaInfo.title;
    let pageTitle = title;
    const localStorageData = localStorage.getItem("websetting");
    if (localStorageData !== null) {
      try {
        const parseData = JSON.parse(localStorageData);
        setLogo(parseData.UploadLogo);
        setLogoAlt(parseData.LogoAltTag);
        setFavicon(parseData.Favicon);
        setWebEmail(parseData.WebsiteEmail);
        setPhoneNo(parseData.PhoneNo);
        setWebsiteAddress(parseData.WebsiteAddress);
        setFacebook(parseData.FacebookUrl);
        setTwitter(parseData.TwitterUrl);
        setLinkedin(parseData.LinkedinUrl);
        setInstagram(parseData.InstagramUrl);
        setYoutube(parseData.YoutubeUrl);
        setWebsiteName(parseData.WebsiteName);
        setWebsiteTitle(parseData.WebsiteTitle);
        setGoogleMapApiKey(parseData.setGoogleMapApiKey);
      } catch (error) {}
    }

    if (window.location.pathname == "/") {
      let res = WebSetting;
      if (localStorageData || localStorageData === null) {
        if (res.websetting) {
          setLogo(res.websetting.UploadLogo);
          setLogoAlt(res.websetting.LogoAltTag);
          setFavicon(res.websetting.Favicon);
          setWebEmail(res.websetting.WebsiteEmail);
          setPhoneNo(res.websetting.PhoneNo);
          setWebsiteAddress(res.websetting.WebsiteAddress);
          setFacebook(res.websetting.FacebookUrl);
          setTwitter(res.websetting.TwitterUrl);
          setLinkedin(res.websetting.LinkedinUrl);
          setInstagram(res.websetting.InstagramUrl);
          setYoutube(res.websetting.YoutubeUrl);
          setWebsiteName(res.websetting.WebsiteName);
          setWebsiteTitle(res.websetting.WebsiteTitle);
          setWebSettingDetail(res.websetting);
          setGoogleMapApiKey(res.websetting.GoogleMapApiKey);
        }
      }
      localStorage.setItem("websetting", JSON.stringify(res.websetting));
      try {
        let clsBtn = document.getElementsByClassName("popcloseBtn");
        if (clsBtn.length) {
          clsBtn[0].style.display = "none";
        }
      } catch (error) {}
      try {
        if (metaInfo.slug) {
          setSeoTitle(metaInfo.slug);
          setSeoDetails(metaInfo);
        } else {
          if (res.seo !== null) {
            setSeoTitle(res.seo.MetaTitle);
            setSeoDetails(res.seo);
          }
        }
        if (res.pageSetting !== undefined) {
          if (pageTitle == "property details") {
            localStorage.setItem(
              "detailPageSetting",
              JSON.stringify(res.pageSetting)
            );
          }
          props.cb(true);
        }
      } catch (error) {}
    } else {
      API.jsonApiCall(
        extra_url + "global/webSettings",
        { PageName: pageTitle, agentId: Constants.agentId },
        "POST",
        null,
        {
          "Content-Type": "application/json",
        }
      ).then((res) => {
        // if(WebSetting)
        if (res.error) {
          props.cb(true);
        }
        // let res = WebSetting;
        if (localStorageData || localStorageData === null) {
          if (res.websetting && res.websetting !== undefined) {
            setLogo(res.websetting.UploadLogo);
            setLogoAlt(res.websetting.LogoAltTag);
            setFavicon(res.websetting.Favicon);
            setWebEmail(res.websetting.WebsiteEmail);
            setPhoneNo(res.websetting.PhoneNo);
            setWebsiteAddress(res.websetting.WebsiteAddress);
            setFacebook(res.websetting.FacebookUrl);
            setTwitter(res.websetting.TwitterUrl);
            setLinkedin(res.websetting.LinkedinUrl);
            setInstagram(res.websetting.InstagramUrl);
            setYoutube(res.websetting.YoutubeUrl);
            setWebsiteName(res.websetting.WebsiteName);
            setWebsiteTitle(res.websetting.WebsiteTitle);
            setWebSettingDetail(res.websetting);
            setGoogleMapApiKey(res.websetting.GoogleMapApiKey);
          }
        }
        localStorage.setItem("websetting", JSON.stringify(res.websetting));
        try {
          let clsBtn = document.getElementsByClassName("popcloseBtn");
          if (clsBtn.length) {
            clsBtn[0].style.display = "none";
          }
        } catch (error) {}
        try {
          if (metaInfo.slug) {
            setSeoTitle(metaInfo.slug);
            setSeoDetails(metaInfo);
          } else {
            if (res.seo !== null) {
              setSeoTitle(res.seo.MetaTitle);
              setSeoDetails(res.seo);
            }
          }
          if (res.pageSetting !== undefined) {
            if (pageTitle == "property details") {
              localStorage.setItem(
                "detailPageSetting",
                JSON.stringify(res.pageSetting)
              );
            }
            props.cb(true);
          }
        } catch (error) {}
      });
    }
  }, [metaInfo.title]);
  return (
    <div>
      {!isTest && (
        <>
          <Head
            title={seoTitle}
            isTest={isTest}
            logo={logo}
            {...seoDetails}
            favicon={favicon}
            websiteName={websiteName}
            websiteTitle={websiteTitle}
            metaInfo={metaInfo}
            webSettingDetail={webSettingDetail}
            defaultMeta={defaultMeta}
          />
          {/* <script async src="https://www.googletagmanager.com/gtag/js?id=AW-984585488"></script> */}
          {/* <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'AW-984585488'); </script> */}
          {!props.pageLoading && (
            <NavigationBar logo={logo} logoAlt={logoAlt} {...props} />
          )}
        </>
      )}
      {children}

      {!props.pageLoading && !isTest && (
        <>
          <Footer
            webEmail={webEmail}
            phoneNo={phoneNo}
            websiteAddress={websiteAddress}
            facebook={facebook}
            isTest={isTest}
            twitter={twitter}
            linkedin={linkedin}
            instagram={instagram}
            youtube={youtube}
            websiteName={websiteName}
          />
          <ToastContainer />
          <Visits {...props} />
        </>
      )}
    </div>
  );
}
