import React from "react";
import Details from "../../ReactCommon/Components/PropertyDetails";
import {
  detailPage,
  image_base_url,
  front_url,
} from "./../../constants/GlobalConstants";
import API from "../../ReactCommon/utility/api";
import Head from "next/head";
class PropertyDetails extends React.Component {
  constructor(props) {
    super(props);
    console.log("PropertyDetails=====>>>>=", props.stateData);
    this.state = {
      ShimmerState: false,
    };
    this.states = false;
  }
  componentDidMount() {
    localStorage.removeItem("propertyFetched");
  }
  render() {
    return (
      <>
        <Head>
          <title>{this.props.infoMeta.title}</title>
          {/* <title>{
                this.props.infoMeta.description
            }</title> */}
          <meta name="Description" content={this.props.infoMeta.description} />
          <meta
            name="og_title"
            property="og:title"
            content={this.props.infoMeta.title}
          />
          <meta
            name="og:description"
            content={this.props.infoMeta.description}
          />
          <meta
            name="og_image"
            property="og:image"
            content={
              !this.props.infoMeta.isSold
                ? this.props.infoMeta.imgUrl
                : front_url + "/images/blogs.png"
            }
          />
          <meta
            name="og:image:alt"
            content={
              !this.props.infoMeta.isSold
                ? this.props.infoMeta.imgUrl
                : front_url + "/images/blogs.png"
            }
          />

          <meta
            property="og:image:secure_url"
            content={
              !this.props.infoMeta.isSold
                ? this.props.infoMeta.imgUrl
                : front_url + "/images/blogs.png"
            }
          />
          <meta property="og:image:type" content="image/jpeg" />
          <meta property="og:image:width" content="400" />
          <meta property="og:image:height" content="300" />

          <meta name="twitter:title" content={this.props.infoMeta.title} />
          <meta
            name="twitter:description"
            content={this.props.infoMeta.description}
          />
          <meta
            name="twitter:image"
            content={
              !this.props.infoMeta.isSold
                ? this.props.infoMeta.imgUrl
                : front_url + "/images/blogs.png"
            }
          />
          <meta name="twitter:image:alt" content={this.props.infoMeta.imgAlt} />
          <link rel="canonical" href={this.props.infoMeta.canonical} />
          <link rel="alternate" href={this.props.infoMeta.canonical} />
          <meta
            name="og_url"
            property="og:url"
            content={this.props.infoMeta.canonical}
          />
        </Head>
        {<Details {...this.props} metaInfo={this.props.setMetaInfo} />}
      </>
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
  let imgUrl =
    image_base_url +
    "mls_images/" +
    listingId +
    "/Photo-" +
    listingId +
    "_0.jpeg";
  let description = "";

  let infoMeta = {
    keyword: "Home for Sale & Listing in Canada",
    title: title + " | Wedu",
    imgAlt: "Home for Sale & Listing in Canada",
    url: "Home-for-Sale-&-Listing-in-Canada",
    imgUrl: imgUrl,
    buy: true,
    description: description,
    isSold: false,
    canonical: canonical,
  };
  let stateData = {};
  try {
    await API.jsonApiCall(detailPage, payload, "POST", null, {
      "Content-Type": "application/json",
    }).then((res) => {
      let status = "Buy";
      let detailsData = res.details;
      detailsData = Object.keys(detailsData).length ? detailsData : false;
      let isSold = detailsData ? res.details.Status : 0;
      isSold = isSold == "U" ? true : false;
      let temp_status = detailsData ? res.details.S_r : 0;
      status = temp_status === "Lease" ? "Rent" : status;
      description =
        "Wedu provides the latest listings of condos, apartments, and houses for " +
        status +
        " throughout Canada. View our comprehensive " +
        status +
        " listings today!";
      let link = [
        { text: "Home", link: "/" },
        { text: detailsData ? detailsData.Addr : "", link: "" },
      ];
      description=detailsData.Ad_text;
      let ids = detailsData ? res.details.id : 0;
      infoMeta.description = isSold ? res.metaDesc : description;
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
    let link = [
      { text: "Home", link: "/" },
      { text: title, link: "" },
    ];
    stateData = {
      details: {},
      slug: payload.SlugUrl,
      breadcrumb: link,
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
