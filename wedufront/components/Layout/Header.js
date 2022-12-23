import React, { useEffect, useState } from "react";
const parse = require("html-react-parser");
import { useRouter } from "next/router";
import Head from "next/head";
import metaJson from "./../../public/json_data/meta.json";
import {
  metaContent,
  front_url,
  pageMeta,
} from "../../constants/GlobalConstants";
export default function Layout(props) {
  const {
    logo,
    title,
    webSettingDetail,
    metaKeyword,
    children,
    websiteName,
    websiteTitle,
    MetaDescription,
    MetaTags,
    MetaTitle,
    favicon,
    metaInfo,
    defaultMeta,
  } = props;
  const router = useRouter();
  const [FbAppId, setFbAppId] = useState(
    webSettingDetail.FbAppId ? webSettingDetail.FbAppId : ""
  );
  const [metaTitles, setMetaTitles] = useState(
    "Homes for Sale & Real Estate Get Listings in Canada | Wedu"
  );
  const [metaDesc, setMetaDesc] = useState(
    "Wedu Is Your Best Choice for Real Estate Search in Canada. Find Homes for Sale, New Developments, Rental Homes, Real Estate Agents, and Property Insights"
  );
  const [metaKeywords, setMetaKeyword] = useState(defaultMeta.keyword);
  const [metaImgAlt, setMetaImgAlt] = useState(defaultMeta.imgAlt);

  const [metaUrl, setMetaUrl] = useState(defaultMeta.url);
  const [metaImgUrl, setMetaImgUrl] = useState(
    defaultMeta.imgUrl
      ? defaultMeta.imgUrl
      : "https://panel.wedu.ca//storage/banner_webp/62d1360d30497.webp"
  );

  // const [metaDesc, setMetaDesc] = useState(MetaTitle ? MetaTitle : defaultMeta.ogtitle);
  let data = {
    "@context": "http://schema.org/",
    "@type": "Review",
    itemReviewed: {
      "@type": "Thing",
      name: "Name",
    },
    reviewRating: {
      "@type": "Rating",
      ratingValue: "3",
      bestRating: "5",
    },
    publisher: {
      "@type": "Organization",
      name: "1234",
    },
  };

  const page_path = router.asPath;
  let sub_menu = page_path;
  let urlSearch = router.asPath;
  let infoMeta = {};
  if (sub_menu == "/") {
    infoMeta = metaJson.home;
  }
  if (
    urlSearch.includes("advance search") ||
    urlSearch.includes("map") ||
    urlSearch.includes("Map") ||
    urlSearch.includes("Listings")
  ) {
    infoMeta = metaJson.advanceSearch;
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
    infoMeta = metaJson.mortgage;
  }

  if (urlSearch.includes("profile")) {
    infoMeta = metaJson.profile;
  }
  if (urlSearch.includes("homevalue")) {
    infoMeta = metaJson.buldings;
  }
  if (urlSearch.includes("agentList")) {
    infoMeta = metaJson.agents;
  }
  if (
    urlSearch.includes("SetPassword") ||
    urlSearch.includes("reset-password") ||
    urlSearch.includes("resetpassword")
  ) {
    logingPop = true;
    infoMeta = {
      keyword: "Set-Password",
      title: "Reset Password | Wedu",
      description: "Reset your account password!",
      imgAlt: "Reset",
      url: "Reset",
      imgUrl: "/images/password.jpg",
    };
  }
  useEffect(() => {
    if (!FbAppId) {
      setFbAppId(webSettingDetail.FbAppId ? webSettingDetail.FbAppId : "");
    }
  }, []);
  // useEffect(() => {
  //   document.documentElement.lang = "en";

  // }, [router.asPath]);
  return (
    <>
      <Head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
        <meta
          http-equiv="Access-Control-Allow-Origin"
          content="https://accounts.google.com"
        />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="robots" content="noodp" />
        <meta name="msapplication-TileColor" content="#ff5b60" />
        <meta name="theme-color" content="#ff5b60" />
        <link href="https://www.wedu.ca/" rel="preconnect" />
        <link rel="shortcut icon" href="/images/icons/favicon.png" />
        <link
          rel="apple-touch-icon"
          sizes="114x114"
          href="https://www.wedu.ca/images/logo/logo.png"
        />
        <meta name="twitter:card" content="app" />
        <meta name="twitter:site" content="@wedu.ca" />
        <meta name="twitter:creator" content="@wedu.ca" />
        <meta name="twitter:app:country" content="ca" />
        <meta
          name="twitter:app:url:googleplay"
          content="https://www.wedu.ca/"
        />
        <meta property="og:type" content="website" />
        <meta name="og_site_name" property="og:site_name" content="Wedu.ca" />

        <meta
          name="Keywords"
          content={"Homes for sale, real estate get listings"}
        />

        {/* <meta property="fb:page_id" content="102988293558" />
        <meta property="fb:admins" content="658873552,624500995,100000233612389" /> */}
        {!urlSearch.includes("propertydetails") &&
          !urlSearch.includes("/blogs/") && (
            <>
              <title>{infoMeta.title}</title>
              <link rel="canonical" href={"https://www.wedu.ca/"} />
              <link rel="alternate" href="https://www.wedu.ca/" />
              <meta
                name="og_url"
                property="og:url"
                content="https://www.wedu.ca/"
              />
              <meta name="Description" content={infoMeta.description} />
              <meta
                name="og_title"
                property="og:title"
                content={infoMeta.title}
              />
              <meta name="og:description" content={infoMeta.description} />
              <meta
                name="og_image"
                property="og:image"
                content={infoMeta.imgUrl}
              />
              <meta name="og:image:alt" content={infoMeta.imgAlt} />
              <meta name="twitter:title" content={infoMeta.title} />
              <meta name="twitter:description" content={infoMeta.description} />
              <meta name="twitter:image" content={infoMeta.imgUrl} />
              <meta name="twitter:image:alt" content={infoMeta.imgAlt} />
            </>
          )}
        {webSettingDetail.ScriptTag && parse(webSettingDetail.ScriptTag)}
      </Head>
    </>
  );
}
