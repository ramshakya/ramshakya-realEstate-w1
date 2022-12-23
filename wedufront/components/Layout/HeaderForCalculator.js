import React, { useEffect } from "react";
import Head from "next/head";
export default function Layout(props) {
  const { title, children } = props;
  return (
    <div>
      <Head>
        <title>{title}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="true" />
        <link
          href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap"
          rel="stylesheet"
        />
        {/* <link
          href="//db.onlinewebfonts.com/c/9d25ea5c587d0f9d470aa9a3634735ea?family=ETmodules"
          rel="stylesheet"
          type="text/css"
        /> */}
        <script type="text/javascript" src="https://www.ratehub.ca/assets/js/widget-loader.js"></script>
      </Head>
    </div>
  );
}
