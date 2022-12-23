import React, { Component } from 'react';
import GoogleMapReact from 'google-map-react';
import {REACT_APP_GOOGLE_API_KEY} from "./../../constants/GlobalConstants"
const AnyReactComponent = ({ text }) => <div><h4>{text}</h4></div>;

class SimpleMap extends Component {
  static defaultProps = {
    center: {
      lat: 59.95,
      lng: 30.33
    },
    zoom: 11
  };

 
  render() {
     let ApiKey = REACT_APP_GOOGLE_API_KEY;
      if(typeof window !== 'undefined'){
        if(localStorage.getItem('websetting')){
          let websetting = JSON.parse(localStorage.getItem('websetting'));
          if(websetting.GoogleMapApiKey!=null){
            ApiKey = websetting.GoogleMapApiKey;
          }  
        } 
        
    }
    return (
      // style={{ height: '25rem', width: '100%' }}
      <div  className="mapContainerHome">
        <GoogleMapReact
          bootstrapURLKeys={{ key: ApiKey }}
          defaultCenter={this.props.center}
          defaultZoom={this.props.zoom}
        >
          <AnyReactComponent
            lat={59.955413}
            lng={30.337844}
            text="delhi"
          />
        </GoogleMapReact>
      </div>
    );
  }
}

export default SimpleMap;