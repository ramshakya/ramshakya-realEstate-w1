import React from "react";
import PropTypes from "prop-types";
import { Helmet } from "react-helmet";
import { metaJson } from "../constants/Global";

const metaDecorator = metaJson;

const MetaDecorator = ({ title, description, imageUrl, imageAlt }) => (
  <Helmet>
    <title>{title}</title>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="noodp" />
    <meta name="msapplication-TileColor" content="#0081a7" />
    <meta name="theme-color" content="#0081a7" />
    <link href="https://housen.ca/" rel="preconnect" />
    <link rel="shortcut icon" href="/images/icon/favicon.png" />

    {/* <!-- Primary Meta Tags --> */}
    <meta name="title" content={title} />
    <meta name="description" content={description} />
    {/* <meta name="keywords" content={content.keyword} /> */}
    <meta name="author" content={'housen.ca'} />

    {/* <!-- Open Graph / Facebook --> */}
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://housen.ca/" />
    <meta property="og:title" content={title} />
    <meta property="og:description" content={description} />
    <meta property="og:image" content={imageUrl} />

    <link rel="canonical" href={'https://housen.ca/'} />
    <link rel="alternate" href="https://housen.ca/" />
    <meta
      property="og:url"
      content={metaDecorator.hostname}
    />
    <meta name="twitter:image:alt" content={imageAlt} />
    <meta name="twitter:site" content={metaDecorator.twitterUsername} />

    {/* <!-- Twitter --> */}
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="https://housen.ca/" />
    <meta name="twitter:creator" content="@housen.ca" />
    <meta name="twitter:app:country" content="ca" />
    <meta name="twitter:app:url:googleplay" content="https://housen.ca/" />
    <meta property="twitter:title" content={title} />
    <meta property="twitter:description" content={description} />
    <meta property="twitter:image" content={imageUrl} />

  </Helmet>
);
export default MetaDecorator;
