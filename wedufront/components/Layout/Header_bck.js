import React, { useEffect, useState } from "react";
const parse = require('html-react-parser');
import Head from "next/head";
import { metaContent, front_url, pageMeta } from "../../constants/GlobalConstants";
export default function Layout(props) {
  console.log("isTest======>>>>>>", props);
  const { logo, title, webSettingDetail, metaKeyword, children, websiteName, websiteTitle, MetaDescription, MetaTags, MetaTitle, favicon, metaInfo, defaultMeta } = props;
  let isMap = true;
  let urls = front_url;
  const [FbAppId, setFbAppId] = useState(webSettingDetail.FbAppId ? webSettingDetail.FbAppId : "");
  const [metaTitles, setMetaTitles] = useState(defaultMeta.title);
  const [metaDesc, setMetaDesc] = useState(defaultMeta.description);
  const [metaKeywords, setMetaKeyword] = useState(defaultMeta.keyword);
  const [metaImgAlt, setMetaImgAlt] = useState(defaultMeta.imgAlt);

  const [metaUrl, setMetaUrl] = useState(defaultMeta.url);
  const [metaImgUrl, setMetaImgUrl] = useState(defaultMeta.imgUrl ? defaultMeta.imgUrl : "https://panel.wedu.ca//storage/banner_webp/62d1360d30497.webp");

  // const [metaDesc, setMetaDesc] = useState(MetaTitle ? MetaTitle : defaultMeta.ogtitle);
  let data = {
    "@context": "http://schema.org/",
    "@type": "Review",
    "itemReviewed": {
      "@type": "Thing",
      "name": "Name"
    },
    "reviewRating": {
      "@type": "Rating",
      "ratingValue": "3",
      "bestRating": "5"
    },
    "publisher": {
      "@type": "Organization",
      "name": "1234"
    }
  }
  useEffect(() => {
    isMap = window.location.origin.includes("map") ? true : false;
    urls = front_url + window.location.pathname + window.location.search;
    if (!FbAppId) {
      setFbAppId(webSettingDetail.FbAppId ? webSettingDetail.FbAppId : "");
    }
  }, []);
  useEffect(() => {
    document.documentElement.lang = "en-us";
    let urlSearch = window.location.origin;
    if (urlSearch.includes("advance search") || urlSearch.includes("map") || urlSearch.includes("Map") || urlSearch.includes("Listings")) {
      // if (title == "advance search" || title == "Map" || title == "Listings") {
      let infoMeta = pageMeta.advanceSearch;
      setMetaTitles(websiteName + " | " + infoMeta.title);
      setMetaKeyword(infoMeta.keyword);
      setMetaDesc(infoMeta.description);
      setMetaImgAlt(infoMeta.imgAlt);
      setMetaUrl(infoMeta.url)
      setMetaImgUrl(infoMeta.imgUrl);
    }
    if (urlSearch === "propertydetails") {
      // if (props.title === "PropertyDetails") {
        let infoMeta = pageMeta.advanceSearch;
        setMetaTitles(websiteName + " | " + infoMeta.title);
        setMetaKeyword(infoMeta.keyword);
        setMetaDesc(infoMeta.description);
        setMetaImgAlt(infoMeta.imgAlt);
        setMetaUrl(infoMeta.url)
        setMetaImgUrl(infoMeta.imgUrl);
    }
    else {
      let temTitle = defaultMeta.title ? defaultMeta.title : props.title;
      setMetaTitles(websiteName + " | " + temTitle);
      setMetaKeyword(defaultMeta.keyword ? defaultMeta.keyword : metaKeyword);
      setMetaDesc(defaultMeta.description ? defaultMeta.description : MetaDescription);
    }
  }, [props]);
  return (
    <>
      {
        !props.isTest &&
        <Head>
          <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
          <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
          <meta name="viewport" content="width=device-width, initial-scale=1" />
          <meta name="robots" content="noodp" />
          <meta name="msapplication-TileColor" content="#ff5b60" />
          <meta name="theme-color" content="#ff5b60" />
          <link href="https://www.wedu.ca/" rel="preconnect" />
          <link rel="shortcut icon" href="/images/icons/favicon.png" />

          {/* <meta property="fb:page_id" content="102988293558" />
        <meta property="fb:admins" content="658873552,624500995,100000233612389" /> */}
          <meta property="og:type" content="website" />
          <meta name="og_site_name" property="og:site_name" content="Wedu.ca" />
          <link rel="apple-touch-icon" sizes="114x114" href="https://www.wedu.ca/images/logo/logo.png" />
          <meta name="twitter:card" content="app" />
          <meta name="twitter:site" content="@wedu.ca" />
          <meta name="twitter:creator" content="@wedu.ca" />
          <meta name="twitter:title" content={metaTitles} />
          <meta name="twitter:description" content={metaDesc} />
          <meta property="twitter:image" content={metaImgUrl} />
          <meta name="twitter:app:country" content="ca" />
          <meta name="twitter:app:url:googleplay" content="https://www.wedu.ca/" />
          <title>{metaTitles}</title>
          <meta name="og_title" property="og:title" content={metaTitles} />
          <meta name="Keywords" content={metaKeywords} />
          <meta name="Description" content={metaDesc} />
          <meta property="og:description" content={metaDesc} />
          <link rel="canonical" href="https://www.wedu.ca/propertydetails/1149-Sunset-Dr-South-Bruce-Peninsula-Ontario-N0H1X0-X5578741" />
          <link rel="alternate" href="https://www.wedu.ca/propertydetails/1149-Sunset-Dr-South-Bruce-Peninsula-Ontario-N0H1X0-X5578741" />
          <meta name="og_image" property="og:image" content={metaImgUrl} />
          <meta name="og_url" property="og:url" content="https://www.wedu.ca/" />
          {/* <meta name="msvalidate.01" content="F4EEB3A0AFFDD385992A06E6920C0AC3" /> */}


          {/* TEST PURPOSE END */}

          {/* <meta http-equiv="content-language" content="en" />
        <title>   {metaTitles ? metaTitles : "wedu.ca"}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="robots" content="all, index, follow" />
        <meta name="msapplication-TileColor" content="#ff5b60" />
        <meta name="theme-color" content="#ff5b60" />
        <meta name="author" content={metaInfo.author ? metaInfo.author : metaContent.author} />
        <meta name="description" content={metaDesc} />
        <meta name="keywords" content={metaKeywords} /> */}


          {/* <!-- Open Graph / Facebook --> */}

          {/* <meta property="og:locale" content="en_US" />
        <meta property="og:title" content={metaTitles} />
        <meta name="og:type" content="website" />
        <meta name="og:description" content={metaDesc} />
        <meta name="og:url" content="https://www.wedu.ca/" />
        <meta name="og:image" content={metaImgUrl ? metaImgUrl : "https://www.wedu.ca/images/logo/logo.png"} />
        <meta property="og:image:secure_url" content={metaImgUrl ? metaImgUrl : "https://www.wedu.ca/images/logo/logo.png"} />
        <meta property="og:image:type" content="image/jpeg" />
        <meta property="og:image:width" content="200" />
        <meta property="og:image:height" content="200" />
        <meta property="og:image:alt" content={metaImgAlt}/> */}

          {/* <!-- Twitter --> */}
          {/* <meta property="twitter:card" content="summary_large_image" />
        <meta property="twitter:url" content="https://www.wedu.ca/" />
        <meta property="twitter:title" content={metaTitles} />
        <meta property="twitter:description" content={metaDesc} />
        <meta property="twitter:image" content={metaImgUrl ? metaImgUrl : "https://www.wedu.ca/images/logo/logo.png"} />
        <meta property="twitter:image:secure_url" content={metaImgUrl ? metaImgUrl : "https://www.wedu.ca/images/logo/logo.png"}  />
        <meta property="twitter:image:type" content="image/jpeg/png" />
        <meta property="twitter:image:width" content="200" />
        <meta property="twitter:image:height" content="200" />
        <meta property="twitter:image:alt" content={metaImgAlt}/> */}


          {/* <link rel="canonical" href="https://www.wedu.ca" />
        <link rel="icon" href={favicon} />
        <link rel="apple-touch-icon" sizes="180x180" href={favicon} />
        <link rel="alternate icon" type="image/png" sizes="32x32" href={favicon} />
        <link rel="alternate icon" type="image/png" sizes="16x16" href={favicon} />
        <link rel="icon" href={favicon} />
        <link rel="mask-icon" href={favicon} color="#ff5b60" />
        {webSettingDetail.ScriptTag &&
          parse(webSettingDetail.ScriptTag)
        } */}
        </Head>
      }
    </>
  );
}
