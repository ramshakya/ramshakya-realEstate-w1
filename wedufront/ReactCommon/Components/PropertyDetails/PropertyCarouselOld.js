import React from "react";
import Image from 'next/image'
import ReactCarousel from "../ReactCarousel";
import { Row, Col } from "react-bootstrap";
import { ReactTabs, Tab } from "../ReactTab";
import Constant from "./../../../constants/GlobalConstants";
class PropertyDetails extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      openPopUp: false,
      selectedSingleImageCarousel: null,
      defaultImages: Constant.defaultImage

    };
    // 
    this.viewPropertySlider = this.viewPropertySlider.bind(this);
    this.handlePopUp = this.handlePopUp.bind(this);
    this.handleClose = this.handleClose.bind(this);
    console.log(this.props,"this.props")
  }
  handleClose() {
    document.body.style.overflow = "auto";
    this.setState({
      openPopUp: false,
    });
  }

  handlePopUp(index, addIndex) {
    document.body.style.overflow = "hidden";
    this.setState({
      openPopUp: true,
      selectedSingleImageCarousel: index + addIndex,
    });
  }

  viewPropertySlider() {
    let imageArr = JSON.parse(JSON.stringify(this.props.propertyImage));
    let obj = {
      s3_image_url: Constant.defaultImage
    }
    let h = [];
    if (this.props.showSingleFirstSlideImage) {
      const firstSliderArr = imageArr.splice(1, this.props.firstSliderRow);
      h.push(
        <Row style={{ height: this.props.sliderHeight }}>
          {
            this.props.propertyImage.length == 1 ?  
              <Col lg={12} style={{ paddingRight: "5px" }}>
                <div className="w-100 h-100 position-relative">
                  {this.props.propertyImage[0] &&
                    <Image
                      src={Constant.image_base_url + this.props.propertyImage[0].s3_image_url}
                      layout={'fill'}
                      // alt="Property-Detail"
                      alt={this.props.alt}
                      objectFit={"cover"}
                      onClick={() => {
                        this.handlePopUp(0, 0);
                      }}
                      placeholder="blur"
                      blurDataURL={this.props.propertyImage[0].s3_image_url}
                      priority={true}
                      quality='1'
                    />}
                </div>
              </Col>
             : <>
              {this.props.propertyImage.length > 0 &&
                <Col lg={6} style={{ paddingRight: "5px" }}>
                  <div className="w-100 h-100 position-relative">
                    {this.props.propertyImage[0] &&
                      <Image
                        src={Constant.image_base_url + this.props.propertyImage[0].s3_image_url}
                        layout={'fill'}
                        // alt="Property-Detail"
                        alt={this.props.alt}
                        objectFit={"cover"}
                        onClick={() => {
                          this.handlePopUp(0, 0);
                        }}
                        placeholder="blur"
                        blurDataURL={this.props.propertyImage[0].s3_image_url}
                        priority={true}
                        quality='1'
                      />}
                  </div>
                </Col>
              }
              {this.props.propertyImage.length < 1 &&
                <Col lg={12} style={{ paddingRight: "5px" }}>
                  <div className="w-100 h-100 position-relative">

                    <Image
                      src={Constant.defaultImage}
                      layout={'fill'}
                      alt={this.props.alt}
                      objectFit={"cover"}
                      onClick={() => {
                        this.handlePopUp(0, 0);
                      }}
                      placeholder="blur"
                      blurDataURL={Constant.defaultImage}
                      priority={true}
                      quality='1'
                    />
                  </div>
                </Col>
              }
              {this.props.propertyImage.length > 1 &&
                <Col lg={6}>
                  <Row style={{ height: this.props.sliderHeight }}>

                    {this.firstSlideCreation(firstSliderArr, 2, 1)}
                  </Row>
                </Col>
              }
            </>
          }
        </Row>
      );
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
        <Col
          key={index}
          lg={12 / colCls}
          className={`${startingPont === index + 1 ||
            (endingPont === index + 1 && endingPont % startingPont === 0)
            ? "property-div-img1 m-0"
            : "property-div-img m-0"
            }`}
        >
          <div className="w-100 h-100 position-relative">
            <Image
              src={Constant.image_base_url + img.s3_image_url ? Constant.image_base_url + img.s3_image_url : Constant.defaultImage}
              layout={'fill'}
              alt={this.props.alt}
              objectFit={"cover"}
              onClick={() => {
                this.handlePopUp(index, indexToAdd);
              }}
              placeholder="blur"
              blurDataURL={img.s3_image_url}
              quality='1'
            /></div>

        </Col>
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
            alt={this.props.alt}
          />
        )}
        <div style={{ height: "600px" }} className="SliderbBgColor">
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
          <label for="fixCloseBtn" className="cursor-pointer mt-2">
            <h3 id="fixCloseBtn" style={{ position: 'absolute', right: '178%' }}>X</h3>
          </label>
        </div>
      </div>
      <div className="position-relative d-flex justify-content-center">
        <div className="position-absolute img-sections">
          <ReactTabs>
            <Tab label="Property Details">
              <ReactCarousel
                show={1}
                selectedCarouselIndex={props.selectedSingleImageCarousel}
              >
                {props.propertyImage &&
                  Array.isArray(props.propertyImage) &&
                  props.propertyImage.map((image,k) => {
                    return (
                      <div className="d-flex justify-content-center" key={k}>
                        <div className="w-75">
                          <img
                            src={image.s3_image_url ? Constant.image_base_url + image.s3_image_url : Constant.defaultImage}
                            alt={props.alt}
                            style={{
                              width: "",
                              height: "calc(100vh - 150px)",
                              objectFit: "cover",
                            }}
                            className="img-fluid w-100"
                            loading="lazy"
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
export default PropertyDetails;