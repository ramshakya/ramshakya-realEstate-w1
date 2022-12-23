import React from "react";
import Head from "next/head";
import Details from "../../ReactCommon/Components/PropertyDetails";
import { detailPage, front_url, image_base_url } from "../../constants/Global";
import API from "./../../ReactCommon/utility/api";
class PropertyDetails extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      ShimmerState: false,
    };
    this.states = false;
  }
  render() {
    this.props.pageName("property details");
    let infoMeta = this.props.infoMeta ? this.props.infoMeta : {};
    return (
      <div>
        <Head>
          <title>{infoMeta.title}</title>
          <meta name="Description" content={infoMeta.description} />
          <meta name="og_title" property="og:title" content={infoMeta.title} />
          <meta name="og:description" content={infoMeta.description} />
          <meta
            name="og_image"
            property="og:image"
            content={
              !infoMeta.isSold
                ? infoMeta.imgUrl
                : front_url + "images/blogs.png"
            }
          />
          <meta
            property="og:image:secure_url"
            content={
              !infoMeta.isSold
                ? infoMeta.imgUrl
                : front_url + "images/blogs.png"
            }
          />
          <meta property="og:image:type" content="image/jpeg" />
          <meta property="og:image:width" content="400" />
          <meta property="og:image:height" content="300" />
          <meta
            name="og:image:alt"
            content={
              !infoMeta.isSold
                ? infoMeta.imgUrl
                : front_url + "images/blogs.png"
            }
          />
          <meta name="twitter:title" content={infoMeta.title} />
          <meta name="twitter:description" content={infoMeta.description} />
          <meta
            name="twitter:image"
            content={
              !infoMeta.isSold
                ? infoMeta.imgUrl
                : front_url + "images/blogs.png"
            }
          />
          <meta name="facebook:title" content={infoMeta.title} />
          <meta name="facebook:description" content={infoMeta.description} />
          <meta
            name="facebook:image"
            content={
              !infoMeta.isSold
                ? infoMeta.imgUrl
                : front_url + "images/blogs.png"
            }
          />
          <meta name="facebook:image:alt" content={infoMeta.imgAlt} />
          <meta name="twitter:image:alt" content={infoMeta.imgAlt} />
          

          <link rel="canonical" href={this.props.infoMeta.canonical} />
          <link rel="alternate" href={this.props.infoMeta.canonical} />
          <meta
            name="og_url"
            property="og:url"
            content={this.props.infoMeta.canonical}
          />



        </Head>
        {
          <Details
            {...this.props}
            cb={this.ShimmerState}
            metaInfo={this.props.setMetaInfo}
          />
        }
      </div>
    );
  }
}
export default PropertyDetails;
export async function getServerSideProps(context) {
  let hrefUrl = context.req.url;
  let canonical = front_url + hrefUrl;
  let slug = hrefUrl.replace("/propertydetails/", "");
  let payload = {};
  payload.SlugUrl = slug;
  let title = "";
  slug = slug.split("-");
  slug.forEach((el) => {
    title = title + " " + el;
  });
  let listingId = slug[slug.length - 1];
  payload.listingId = listingId;
  let imgUrl = "https://housen.ca/images/search_backgroundnew(2).jpg";
  let infoMeta = {
    keyword: "Home for Sale & Listing in Canada",
    title: title + " | Housen",
    description:
      "Housen provides the latest listings of condos, apartments, and houses for Buy throughout Canada. View our comprehensive Buy listings today!",
    imgAlt: "Home for Sale & Listing in Canada",
    url: "Home-for-Sale-&-Listing-in-Canada",
    imgUrl: imgUrl,
    buy: true,
    isSold: false,
    canonical: canonical,
  };
  let stateData = {};
  try {
    await API.jsonApiCall(detailPage, payload, "POST", null, {
      "Content-Type": "application/json",
    }).then((res) => {
      console.log("=============res==========", res);
      let link = [
        { text: "Home", link: "/" },
        { text: res.details ? res.details.Addr : "", link: "" },
      ];
      let ids = res.details ? res.details.id : 0;
      let isSoldStatus = res.details ? res.details.Status : 0;
      let isSold = res.details ? res.details.Status : 0;
      let details = res.details ? res.details : 0;
      let img = details ? details.properties_images : false;
      if (img && img.length) {
        img = img[0];
        img = img
          ? image_base_url + img.s3_image_url
          : "https://housen.ca/images/search_backgroundnew(2).jpg";
        img = img.replace("storage//", "storage/");
        infoMeta.imgUrl = img;
      }
      isSold = isSoldStatus == "U" ? true : false;
      infoMeta.isSold = isSold;
      stateData = {
        details: res,
        slug: payload.SlugUrl,
        breadcrumb: link,
        shareLink: hrefUrl,
        isSold: isSold,
        checkDetailData: ids ? false : true,
      };
    });
  } catch (error) {
    console.log("===============error=========", error);
    stateData = {
      details: {},
      slug: payload.SlugUrl,
      shareLink: hrefUrl,
      checkDetailData: false,
      error: error,
    };
  }
  return {
    props: {
      infoMeta: infoMeta,
      stateData: stateData,
    }, // will be passed to the page component as props
  };
}
