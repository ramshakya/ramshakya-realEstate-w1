import { useState, useEffect, useRef, Component } from "react";
class GoogleMap extends Component {
  constructor(props) {
    super(props);
  }
  static defaultProps = {
    config: {}
  };
  componentDidMount() {

    // const script = document.createElement("script");
    //   script.async = true;
    //   script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyBM8LFkdq9fISqWRWFt1u-rqsALyptk1g4";
    //   script.onload = () => document.head.appendChild(script);
  }
  componentDidUpdate() {
    if(this.props.resize){
         this.map = this._createMap();
     }else{
     }
  }  

  _createMap = () => {
    const { mapCanvas } = this.refs;
    const { config } = this.props;
    console.log("latlang",config.mapOptions)
    if (config.type === "street") {
      return new google.maps.StreetViewPanorama(mapCanvas, config.mapOptions);
    }

    return new google.maps.Map(mapCanvas, config.mapOptions);
  };
  _createInfoWindow = (marker, location) => {
    const { title, text, imgUrl } = location.infoWindowContent;

    const infoWindowTemplate = `
      <div className="info-window" style="background-image: url(${imgUrl})"}>
        <h4>${title}</h4>
        <p>${text}</p>
      </div>
    `;

    const infoWindow = new google.maps.InfoWindow({
      content: infoWindowTemplate
    });

    marker.addListener("click", function () {
      infoWindow.open(this.map, marker);
    });
  };

  render() {
    return (
      <div className="google-map" ref="mapCanvas">
        LOADING MAP...
      </div>
    );
  }
}
export default GoogleMap