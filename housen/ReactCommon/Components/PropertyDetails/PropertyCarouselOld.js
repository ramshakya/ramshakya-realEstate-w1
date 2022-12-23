import React from "react";
import Image from "next/image";
import ReactCarousel from "../ReactCarousel";
import { Row, Col, Card } from "react-bootstrap";
import { ReactTabs, Tab } from "../ReactTab";
import Constants from "../../../constants/Global";
class PropertyCarousel extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      openPopUp: false,
      selectedSingleImageCarousel: null,
      defaultImages: "",
    };
    // https://via.placeholder.com/650x950
    this.viewPropertySlider = this.viewPropertySlider.bind(this);
    // this.mapChunks = this.mapChunks.bind(this);
    this.handlePopUp = this.handlePopUp.bind(this);
    this.handleClose = this.handleClose.bind(this);
  }
  handleClose() {
    document.body.style.overflow = "auto";
    this.setState({
      openPopUp: false,
    });
  }

  handlePopUp(index, addIndex) {
    if (!this.props.emailIsVerified) {
      console.log("------------Email Not Verified ------");
      return false;
    }
    document.body.style.overflow = "hidden";
    this.setState({
      openPopUp: true,
      selectedSingleImageCarousel: index + addIndex,
    });
  }

  viewPropertySlider() {
    let imageArr = JSON.parse(JSON.stringify(this.props.propertyImage));

    let obj = {
      s3_image_url: this.state.defaultImages,
    };
    // if(imageArr.length<=0){
    //   imageArr.push(obj);
    //   this.props.propertyImage.push(obj);
    // }
    // if(imageArr.length<=1){
    //   imageArr.push(obj);
    //   this.props.propertyImage.push(obj);
    // }
    // if(imageArr.length<=2){
    //   imageArr.push(obj);
    //   this.props.propertyImage.push(obj);
    // }
    let h = [];
    if (this.props.showSingleFirstSlideImage) {
      if (!this.props.isLogin && !this.props.emailIsVerified) {
        imageArr = imageArr.splice(0, 2);
        const firstSliderArr = imageArr.splice(1, this.props.firstSliderRow);
        h.push(
          <Row style={{ height: this.props.sliderHeight }}>
            {this.props.propertyImage.length == 1 ? (
              <>
                <Col lg={12} style={{ paddingRight: "5px" }}>
                  <div className="w-100 h-100 position-relative">
                    {this.props.propertyImage[0] && (
                      <Image
                        src={
                          Constants.image_base_url +
                          this.props.propertyImage[0].s3_image_url
                        }
                        layout={"fill"}
                        // alt="Property-Detail"
                        alt={this.props.propertyImage[0].s3_image_url}
                        objectFit={"cover"}
                        onClick={() => {
                          this.handlePopUp(0, 0);
                        }}
                        placeholder="blur"
                        blurDataURL={this.props.propertyImage[0].s3_image_url}
                        priority={true}
                      
                      />
                    )}
                  </div>
                </Col>
              </>
            ) : (
              <>
                <Col lg={6} style={{ paddingRight: "5px" }} className="">
                  <div className="w-100 h-100 position-relative">
                    {this.props.propertyImage[0] && (
                      <Image
                        src={
                          Constants.image_base_url +
                          this.props.propertyImage[0].s3_image_url
                        }
                        layout={"fill"}
                        alt="Property-Detail"
                        objectFit={"cover"}
                        onClick={() => {
                          this.handlePopUp(0, 0);
                        }}
                        placeholder="blur"
                        blurDataURL={
                          Constants.image_base_url +
                          this.props.propertyImage[0].s3_image_url
                        }
                        priority={true}
                        
                      />
                    )}
                  </div>
                </Col>
                <Col
                  lg={6}
                  style={{
                    padding: "11px 50px 36px 43px",
                    backgroundColor: "#0009",
                  }}
                >
                  <Card className="mt-9">
                    <Card.Body>
                      <div className="">
                        <div>
                          <h5 className="sliderLoginBox-heading">
                            {" "}
                            <svg
                              viewBox="0 0 24 24"
                              width="24"
                              height="24"
                              className="xs-ml1"
                              aria-hidden="true"
                            >
                              <path d="M18 8h-1V6A5 5 0 007 6v2H6a2 2 0 00-2 2v10c0 1.1.9 2 2 2h12a2 2 0 002-2V10a2 2 0 00-2-2zM9 6a3 3 0 116 0v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"></path>
                            </svg>
                            Login - Free Account Required
                          </h5>
                          <p className="sliderLoginBox">
                            Join Thousands of Canadians Searching For Homes on
                            Housen.ca Every Month!
                          </p>
                          <ul className="LoginBenefit sliderLoginBox-list">
                            <li>
                              Instant Access to All Photos, Virtual Tours, &
                              More!
                            </li>
                            <li>
                              {" "}
                              Full Access to{" "}
                              <span style={{ color: "red" }}>
                                Listing Sold History{" "}
                              </span>{" "}
                              (GTA) & Details
                            </li>
                            <li>
                              Save Homes & Searches, Add Listing/Community Watch
                              Alerts
                            </li>
                          </ul>
                          <div className=" text-white">
                            <h6 className="join-and-signIn-head">
                              <button
                                onClick={this.props.signInToggle}
                                className={`  join-signIn-toggle btn  primary-btn-cls`}
                              >
                                View Full Listing & Photos &nbsp;
                                <svg
                                  viewBox="0 0 24 24"
                                  height="24"
                                  width="24"
                                  className="fill-current xs-mr1"
                                >
                                  <circle cx="12" cy="12" r="3"></circle>
                                  <path d="M20 4h-3.17l-1.24-1.35A2 2 0 0014.12 2H9.88c-.56 0-1.1.24-1.48.65L7.17 4H4a2 2 0 00-2 2v12c0 1.1.9 2 2 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm-8 13a5 5 0 110-10 5 5 0 010 10z"></path>
                                </svg>
                              </button>{" "}
                            </h6>
                          </div>
                        </div>
                        <div className="w-100 h-100 position-relative ">
                          {this.props.propertyImage[1] && (
                            <Image
                              src={
                                this.props.propertyImage[0].s3_image_url
                                  ? Constants.image_base_url +
                                    this.props.propertyImage[1].s3_image_url
                                  : this.state.defaultImages
                              }
                              layout={"fill"}
                              alt="Property-Detail"
                              objectFit={"cover"}
                              onClick={() => {
                                this.handlePopUp(0, 0);
                              }}
                              placeholder="blur"
                              blurDataURL={
                                Constants.image_base_url +
                                this.props.propertyImage[0].s3_image_url
                              }
                              priority={true}
                              
                            />
                          )}
                        </div>
                      </div>
                    </Card.Body>
                  </Card>
                </Col>
              </>
            )}
          </Row>
        );
      } else {
        const firstSliderArr = imageArr.splice(1, this.props.firstSliderRow);
        h.push(
          <Row style={{ height: this.props.sliderHeight }}>
            <Col lg={6} style={{ paddingRight: "5px" }}>
              <div className="w-100 h-100 position-relative">
                {this.props.propertyImage[0] && (
                  <Image
                    src={
                      Constants.image_base_url +
                      this.props.propertyImage[0].s3_image_url
                    }
                    layout={"fill"}
                    alt="Property-Detail"
                    objectFit={"cover"}
                    onClick={() => {
                      this.handlePopUp(0, 0);
                    }}
                    placeholder="blur"
                    blurDataURL={
                      Constants.image_base_url +
                      this.props.propertyImage[0].s3_image_url
                    }
                    priority={true}
                    
                  />
                )}
              </div>
            </Col>
            <Col lg={6}>
              <Row style={{ height: this.props.sliderHeight }}>
                {this.firstSlideCreation(firstSliderArr, 2, 1)}
              </Row>
            </Col>
          </Row>
        );
      }
      imageArr.splice(0, 1);
      for (let i = 0; i < imageArr.length; i = i + this.props.imageToShow) {
        const chunk = imageArr.splice(0, this.props.imageToShow);
        h.push(
          <Row style={{ height: this.props.sliderHeight }}>
            {this.firstSlideCreation(
              chunk,
              this.props.inRowImage,
              this.props.firstSliderRow + 1 + i
            )}
          </Row>
        );
      }
    }
    return h;
  }

  firstSlideCreation = (firstSliderArr, colCls, indexToAdd) => {
    const data = firstSliderArr.map((img, index) => {
      let startingPont = colCls;
      let endingPont = firstSliderArr.length;
      return (
        <>
          <Col
            lg={12 / colCls}
            className={`${
              startingPont === index + 1 ||
              (endingPont === index + 1 && endingPont % startingPont === 0)
                ? "property-div-img1 m-0"
                : "property-div-img m-0"
            }`}
          >
            <div className="w-100 h-100 position-relative">
              <Image
                src={Constants.image_base_url + img.s3_image_url}
                layout={"fill"}
                alt="Property-Detail"
                objectFit={"cover"}
                onClick={() => {
                  this.handlePopUp(index, indexToAdd);
                }}
                placeholder="blur"
                blurDataURL={Constants.image_base_url + img.s3_image_url}
                
              />
            </div>
          </Col>
        </>
      );
    });
    return data;
  };

  render() {
    return (
      <>
        {this.state.openPopUp && (
          <PropertyPopup
            selectedSingleImageCarousel={this.state.selectedSingleImageCarousel}
            propertyImage={this.props.propertyImage}
            handleClose={this.handleClose}
          />
        )}
        <div style={{ height: "600px" }}>
          <ReactCarousel show={1}>{this.viewPropertySlider()}</ReactCarousel>
        </div>
      </>
    );
  }
}

const PropertyPopup = (props) => {
  return (
    <div className="position-fixed property-popup">
      <div className="position-relative">
        <div
          className=" position-absolute text-white d-flex flex-row-reverse padding30 opacity-5 cursor-pointer"
          onClick={() => {
            props.handleClose();
          }}
        >
          <label for="fixCloseBtn" className="cursor-pointer mt-3">
            <h3
              id="fixCloseBtn"
              style={{ position: "absolute", right: "178%" }}
            >
              X
            </h3>
          </label>
        </div>
      </div>
      <div className="position-relative d-flex justify-content-center">
        <div className="position-absolute">
          <ReactTabs>
            <Tab label="Property Images">
              <ReactCarousel
                show={1}
                selectedCarouselIndex={props.selectedSingleImageCarousel}
              >
                {props.propertyImage &&
                  Array.isArray(props.propertyImage) &&
                  props.propertyImage.map((image) => {
                    return (
                      <div className="d-flex justify-content-center">
                        <div className="w-100">
                          <img
                            src={Constants.image_base_url + image.s3_image_url}
                            alt="Property-Detail"
                            style={{
                              width: "",
                              height: "calc(100vh - 70px)",
                              objectFit: "contain",
                            }}
                            className="img-fluid w-100"
                          />
                        </div>
                      </div>
                    );
                  })}
              </ReactCarousel>
            </Tab>
            <Tab label=""></Tab>
          </ReactTabs>
        </div>
      </div>
    </div>
  );
};
export default PropertyCarousel;
