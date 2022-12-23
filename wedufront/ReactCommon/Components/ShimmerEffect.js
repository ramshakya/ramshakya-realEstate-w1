import React from "react";
import Style from "../../styles/css/ReactCommon/shimmer.module.css";
function ShimmerEffect(props) {
  let count = props.count ? props.count : 1;
  const renderFeaturedShimmer = () => {
    let h = [];
    for (let i = 0; i < count; i++) {
      h.push(
        <div key={i} className="row">
          <div className="gsc_col-xs-12 gsc_col-sm-4">
            <div className={`${Style.skeleton} ${Style.title}`}> </div>
          </div>
          <div className="gsc_col-xs-12 gsc_col-sm-8">
            <div className={`${Style.skeleton} ${Style.text}`}> </div>
            <div className={`${Style.skeleton} ${Style.text}`}> </div>
            <div className={`${Style.skeleton} ${Style.text}`}> </div>
          </div>
        </div>
      );
    }
    return h;
  };
  const cardView = () => {
    let h = [];
    for (let i = 0; i < count; i++) {
      h.push(
        <div key={i} className={`${props.columnCls} border ml-2`}>
          <div className={`${Style.skeleton} ${Style.thumbnail}`}></div>
          <div className={`${Style.skeleton} ${Style.title}`}></div>
          <div className={`${Style.skeleton} ${Style.text}`}></div>
          <div className={`${Style.skeleton} ${Style.text}`}></div>
        </div>
      );
    }
    return h;
  };
  const rowShimmer = () =>{
    let h = [];
    for (let i = 0; i < count; i++) {
      h.push(
        <div key={i} className="row mb-3 border">
          <div className={`${Style.skeleton} ${Style.gridView} w-100 `}></div>
          <div className={`${Style.skeleton} ${Style.text}`}></div>
          <div className={`${Style.skeleton} ${Style.text}`}></div>
        </div>
      );
    }
    return h;
  }
  let functionName = "";
  if (props.type === "featureElement") {
    functionName = renderFeaturedShimmer;
  } else if (props.type === "cardView") {
    functionName = cardView;
  } else {
    functionName = rowShimmer;
  }
  return (
    <div className="container" >
      <div className="row">{functionName()}</div>
      <div className={Style.shimmerWrapper}>
        <div className={Style.shimmer}></div>
      </div>
    </div>
  );
}

export default ShimmerEffect;
