import React, { useState, useEffect, useRef } from "react";
import Head from "next/head";
import { useRouter } from "next/router";
import metaJson from "./../../public/json_data/meta.json";
const parse = require("html-react-parser");

const Header = (props) => {
  const router = useRouter();
  const {
    title,
    favicon,
    webSettingDetail,
    websiteName,
    websiteTitle,
    MetaDescription,
    MetaTags,
    MetaTitle,
    metaInfo,
    author,
  } = props;


  const page_path = router.asPath;
  let sub_menu = page_path;
  let urlSearch = router.asPath;
  let infoMeta = {};
  if (sub_menu == "/") {
    infoMeta = metaJson.home;
  }
  if (urlSearch.includes("advance search") || urlSearch.includes("map") || urlSearch.includes("Map") || urlSearch.includes("Listings")) {
    infoMeta = metaJson.advanceSearch
  }
  if (urlSearch.includes("aboutUs")) {
    infoMeta = metaJson.aboutUs;
  }
  if (urlSearch.includes("ContactUs")) {
    infoMeta = metaJson.contactUs;
  }
  if (urlSearch.includes("buyinghomes")) {
    infoMeta = metaJson.newHome;
    infoMeta.imgUrl = "/images/homevaluetion.jpg";
  }
  if (urlSearch.includes("sellinghomes")) {
    infoMeta = metaJson.newHome;
  }
  if (urlSearch.includes("blogs")) {
    infoMeta = metaJson.blogs;
  }
  if (urlSearch.includes("calculator")) {
    infoMeta = metaJson.mortgage
  }

  if (urlSearch.includes("profile")) {
    infoMeta = metaJson.profile;
  }
  if (urlSearch.includes("homevalue")) {
    infoMeta = metaJson.buldings
  }
  if (urlSearch.includes("agentList")) {
    infoMeta = metaJson.agents;
  }
  if (urlSearch.includes("SetPassword") || urlSearch.includes("reset-password") || urlSearch.includes("resetpassword")) {
    logingPop = true;
    infoMeta = {
      "keyword": "Set-Password",
      "title": "Reset Password | Wedu",
      "description": "Reset your account password!",
      "imgAlt": "Reset",
      "url": "Reset",
      "imgUrl": "/images/password.jpg"
    };
  }

  return (
    <>
      <Head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="robots" content="noodp" />
        <meta name="msapplication-TileColor" content="#0081a7" />
        <meta name="theme-color" content="#0081a7" />
        <link href="https://housen.ca/" rel="preconnect" />
        <link rel="shortcut icon" href="/images/icon/favicon.png" />
        <meta name="twitter:card" content="app" />
        <meta name="twitter:site" content="@housen.ca" />
        <meta name="twitter:creator" content="@housen.ca" />
        <meta name="twitter:app:country" content="ca" />
        <meta name="twitter:app:url:googleplay" content="https://housen.ca/" />
        <meta property="og:type" content="website" />
        <meta name="og_site_name" property="og:site_name" content="housen.ca" />
        
        <meta name="keywords" content={MetaTags} />
        <meta name="author" content={author} />
        <meta name="robots" content="follow" />
        <link rel="icon" type="image/png/x-icon" href={favicon}></link>
        {/*<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/lightslider/1.1.5/css/lightslider.min.css' />
				<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
				<script src='https://cdnjs.cloudflare.com/ajax/libs/holder/2.9.0/holder.min.js'></script>
				<script src='https://cdnjs.cloudflare.com/ajax/libs/lightslider/1.1.5/js/lightslider.min.js'></script>*/}
        <script
          src={`https://sdk.hoodq.com/hq-sdk-v2.js?key=JyH8TjujLh5C7Qrf2mpAc4bcOFxnU4O55badfZVO&libs=ah`}
        ></script>
        {
		 !urlSearch.includes("propertydetails") &&
          !urlSearch.includes("/blogs/") && 
		  <>
		   <title>{infoMeta.title}</title>
            <meta name="Description" content={infoMeta.description} />
            <meta name="og_title" property="og:title" content={infoMeta.title} />
            <meta name="og:description" content={infoMeta.description} />
            <meta name="og_image" property="og:image" content={infoMeta.imgUrl} />
            <meta name="og:image:alt" content={infoMeta.imgAlt} />

            <meta name="og_url" property="og:url" content="https://housen.ca/" />
            <link rel="canonical" href={"https://housen.ca/"} />
            <link rel="alternate" href="https://housen.ca/" />

            <meta name="twitter:title" content={infoMeta.title} />
            <meta name="twitter:description" content={infoMeta.description} />
            <meta name="twitter:image" content={infoMeta.imgUrl} />
            <meta name="twitter:image:alt" content={infoMeta.imgAlt} />

            <meta name="facebook:title" content={infoMeta.title} />
            <meta name="facebook:description" content={infoMeta.description} />
            <meta name="facebook:image" content={infoMeta.imgUrl} />
            <meta name="facebook:image:alt" content={infoMeta.imgAlt} />
		   </>
		}
        {webSettingDetail.ScriptTag && parse(webSettingDetail.ScriptTag)}
      </Head>
      {webSettingDetail.bodyscriptTag && parse(webSettingDetail.bodyscriptTag)}
    </>
  );
};
export default Header;
