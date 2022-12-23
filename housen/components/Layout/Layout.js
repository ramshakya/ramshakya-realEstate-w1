import React, { useState, useEffect, useRef } from 'react'
import Header from "./Header";
import Navbar from "./Navbar"
import Footer from "./Footer"
import Constants from "../../constants/Global";
import Visits from "../UserActivity/Visits";
import API from "../../ReactCommon/utility/api";
import { ToastContainer, toast } from 'react-toastify';
// import WebSetting from "./../../public/json/websetting.json";
 
const Layout = (props) => {
  
  const { children, title, metaInfo,showFooterBtn } = props;
  const [logo, setLogo] = useState('');
  const [logoAlt, setLogoAlt] = useState('Housen logo');
  const [favicon, setFavicon] = useState('../images/logo/logo.png');
  const [websiteName, setWebsiteName] = useState('Housen.ca');
  const [MetaDescription, setMetaDescription] = useState('metaDesc');
  const [MetaTags, setMetaTags] = useState('tags');
  const [MetaTitle, setMetaTitle] = useState('title');
  const [author, setAuthor] = useState('Housen');
  const [webEmail, setWebEmail] = useState('');
  const [phoneNo, setPhoneNo] = useState('');
  const [websiteAddress, setWebsiteAddress] = useState('');
  const [facebook, setFacebook] = useState('#');
  const [twitter, setTwitter] = useState('#');
  const [linkedin, setLinkedin] = useState('#');
  const [instagram, setInstagram] = useState('#');
  const [youtube, setYoutube] = useState('#');
  const [seoDetails, setSeoDetails] = useState({});
  const [webSettingDetail, setWebSettingDetail] = useState({});
  const [websiteTitle, setWebsiteTitle] = useState("Best Website in GTA");
  const [GoogleMapApiKey, setGoogleMapApiKey] = useState("");
  const [metaThumbnail, setMetaThumbnail] = useState('/images/search_background.jpg');
  useEffect(() => {
    
    //ExperimentalBasis
    const localStorageData = localStorage.getItem('websetting');
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
      } catch (error) {

      }
    }
    let title = metaInfo.title;
    let pageTitle = title;
    if (!window.location.href.includes("propertydetails")) {
      if (props.isOpen) {
        props.togglePopUp();
      }
    }
    if (window.location.pathname == "/") {
      API.jsonApiCall(Constants.base_url + "api/v1/services/home/getWebsettings",
                '', "POST", {}
            ).then((WebSetting)=>{
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
      localStorage.setItem('websetting', JSON.stringify(res.websetting));
      localStorage.setItem('home_page', JSON.stringify(res));
      props.cb(true);
    });
      
      try {
        let clsBtn = document.getElementsByClassName('popcloseBtn');
        if (clsBtn.length) {
          clsBtn[0].style.display = "none";
        }
      } catch (error) {

      }
      try {

        if (metaInfo.slug) {

          setSeoTitle(metaInfo.slug);
          setSeoDetails(metaInfo);
        }
        else {
          if (res.seo !== null) {
            setSeoTitle(res.seo.MetaTitle);
            setSeoDetails(res.seo);
          }
        }
        if (res.pageSetting !== undefined) {
          if (pageTitle == "property details") {
            localStorage.setItem('detailPageSetting', JSON.stringify(res.pageSetting));
          }
          props.cb(true);
        }
      } catch (error) {

      }
    } else {
      API.jsonApiCall((Constants.base_url + 'api/v1/services/global/webSettings'), { PageName: pageTitle, agentId: Constants.agentId }, "POST", null, {
        "Content-Type": "application/json",
      }).then((res) => {
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
        if (res.websetting !== undefined) {
          localStorage.setItem('websetting', JSON.stringify(res.websetting));
        }
        if (res.pageSetting !== undefined) {
          if (pageTitle == "home") {
            localStorage.setItem('HomePageSetting', JSON.stringify(res.pageSetting));
            localStorage.setItem('arrangeSections', JSON.stringify(res.arrangeSections));
          }
          if (pageTitle == "property details") {
            localStorage.setItem('detailPageSetting', JSON.stringify(res.pageSetting));
          }
          props.cb(true);

        }
        try {
          let clsBtn = document.getElementsByClassName('popcloseBtn');
          if (clsBtn.length) {
            clsBtn[0].style.display = "none";

          }
        } catch (error) {

        }
        try {
          if (res.seo !== null) {
            setSeoTitle(res.seo.MetaTitle);
            setSeoDetails(res.seo);
          }

        } catch (error) {

        }

      })
      // props.cb(true);
    }
  }, [metaInfo]);
  return (
    <>

      <Header
        title={websiteTitle}
        favicon={favicon}
        websiteName={websiteName}
        MetaDescription={MetaDescription}
        MetaTags={MetaTags}
        MetaTitle={MetaTitle}
        author={author}
        webSettingDetail={webSettingDetail}
      />
     
      <Navbar
        {...props}
        logo={logo}
        logoAlt={logoAlt}
        popularSearch={props.popularSearch}
      />
      {children}
      <Footer
        webEmail={webEmail}
        phoneNo={phoneNo}
        websiteAddress={websiteAddress}
        facebook={facebook}
        twitter={twitter}
        linkedin={linkedin}
        instagram={instagram}
        youtube={youtube}
        websiteName={websiteName}
        logo={logo}
        logoAlt={logoAlt}
        popularSearch={props.popularSearch}
        checkCity={props.checkCity}
        checkCityChange={props.checkCityChange}
        showFooterBtn={showFooterBtn}
      />
      <ToastContainer />
      <Visits />
    </>
  )
}
export default Layout;