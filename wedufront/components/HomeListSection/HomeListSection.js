// Library
import { useState, useEffect, useRef } from "react";
import Constants from '../../constants/GlobalConstants'
const defaultImage = Constants.defaultImage
let flag = false;
const HomeListSection = () => {
  var formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  });
  const [data, setData] = useState([]);
  const [useE, setUseE] = useState(true);
  const [paginateData, setPaginateData] = useState();
  const [propTyedata, setPropTypeData] = useState([]);
  const [input, setInput] = useState('');
  const [propTypeVal, setTypeVal] = useState('');
  const requestOptions = {
    method: 'GET',
    headers: { 'Content-Type': 'application/json' }
  };
  useEffect(() => {
    propListData();
     
  }, [])
  const propListData = async () => {
    await fetch(Constants.HomepropertyApi, requestOptions).then((response) =>
      response.text()).then((res) => JSON.parse(res))
      .then((json) => {
        setData(json.listData)
        setPropTypeData(json.PropertyType)
        //console.log("======>>>propTyedata>>", propTyedata);
        setPaginateData(json.pagination)
        setUseE(false);
      }).catch((err) => console.log({ err }));
  }
  function getInputData(e) {
    setInput(e.target.value)
    //console.log("====>>>>>input", e.target.value);

  }
  function PropertyType(e) {
    //console.log("=========>>>>>eeeeeeeeeeeeeee", e);
    setTypeVal(e.target.value)
  }

  return (
    <div className="">
      {/*<!-- site hero area start  --> */}
      <section className="hero-section">
        <div className="container">
          {/*<!-- counter row  --> */}
          <div className="row">
            <div className="col-sm-12 col-md-7 col-lg-5">
              <div className="prop-count">
                <div className="row">
                  {/*<!-- single count block  --> */}
                  <div className="col-4 col-sm-4 col-md-4">
                    <span
                      data-purecounter-start="0"
                      data-purecounter-end="850"
                      className="purecounter fz-40"
                    >850</span
                    >
                    <p className="fz-21">Properties Sold</p>
                  </div>
                  {/*<!-- single count block  --> */}
                  <div className="col-4 col-sm-4 col-md-4">
                    <span
                      data-purecounter-start="0"
                      data-purecounter-end="22"
                      className="purecounter fz-40"
                    >22</span
                    >
                    <p className="fz-21">Awards Gained</p>
                  </div>
                  {/*<!-- single count block  --> */}
                  <div className="col-4 col-sm-4 col-md-4">
                    <span
                      data-purecounter-start="0"
                      data-purecounter-end="21"
                      className="purecounter fz-40"
                    >21</span
                    >
                    <p className="fz-21">Years Experiencs</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {/*<!-- blackBox row  --> */}
          <div className="row">
            <div className="col-sm-12 col-md-10 col-lg-7">
              <div className="box-block-wrapper mt-3">
                <h1>Creating Modern Properties Is Our Speciality</h1>
                <p className="my-5 fz-21">
                  With a lot of experience we will help you to create the modern
                  properties you want
              </p>
                {/*<!-- search box area --> */}
                <form action="" className="search-boxform">
                  <div className="search-box-wrapper">
                    <div className="row">
                      {/*<!-- location area  --> */}
                      <div className="col-lg-3 col-md-3 col-xs-12">
                        {/* <select className="form-select" aria-label="Default select">
                          <option  >Location</option>
                          <option value="1">Bali</option>
                          <option value="2">Moscow</option>
                          <option value="3">Egypt</option>
                        </select>
                        <div className="selected-option">
                          <a href="#">Bali</a>
                        </div> */}
                        
                        <div className=""><span className="search-title ml-1">Search</span></div>
                        <input className="form-control form-input mt-1" placeholder="Address, Neighbourhood, City, Postal Code or MLS#" value={input} onInput={getInputData} />
                      </div>
                      {/*<!-- location area  --> */}
                      <div className="col-lg-3 col-md-3 col-xs-12">
                        <select className="form-select" aria-label="Default select" onChange={PropertyType}>
                          <option  >Property Type</option>
                          {
                            propTyedata.map((item, key) => {
                              // console.log("itemsss==>",item.PropertyType);
                              return (
                                <option value={item.PropertyType}>{item.PropertyType}</option>
                              )
                            })
                          }

                        </select>
                        <div className="selected-option">
                          <span>{propTypeVal}</span>
                        </div>
                      </div>
                      {/*<!-- location area  --> */}
                      <div className="col-lg-3 col-md-3 col-xs-12">
                        <select className="form-select" aria-label="Default select">
                          <option  >Average Price</option>
                          <option value="1">$5000-$7000</option>
                          <option value="2">$4000-$7000</option>
                          <option value="3">$2000-$7000</option>
                        </select>
                        <div className="selected-option">
                          <a href="#">$5000-$7000</a>
                        </div>
                      </div>
                      {/*<!-- Button area  --> */}
                      <div className="col-lg-3 col-md-3 search-btn-area col-xs-12">
                        <button className="common-btn search-btn">Search</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div className="bg-hero">
          <img src="images/hero-img.png" alt="image" className="img-fluid" />
        </div>
        <div className="dots-pattern">
          <img src="images/dots.png" className="img-fluid" alt="image" />
        </div>
      </section>
      {/*<!-- site hero area start  --> */}

      {/*<!-- property section area start  --> */}
      <section className="property-section">
        <div className="container-fluid">
          {/*<!-- title row --> */}
          <div className="row justify-content-center">
            <div className="col-12 col-lg-7">
              <div className="title text-center">
                <h1 className="px-5 mb-5">
                  We Provide The Best Based On The Property That You Like
              </h1>
              </div>
            </div>
          </div>
          {/*<!-- card row --> */}
          <div className="row">
            {/*<!-- single-card  --> */}
            {
              data.map((item, key) => {
                return (
                  // 
                  <div className="col-sm-6 col-md-4 mt-5">
                    <a href="#" className="prop-card">
                      <div className="card-img">
                        {
                          item.srcImg.s3_image_url ? <img className="img-fluid home-image border-radius" alt="image" src={item.srcImg.s3_image_url} /> : <img className="img-fluid border-radius" alt="image" src={defaultImage} alt="image" />
                        }
                      </div>
                      <div className="cardAddr d-flex align-items-center">
                        <span className="span me-2">
                          <i className="fas fa-map-marker-alt"></i>
                        </span>
                        {/* <span className="addr"> JI. Elgin St. Celina, Delaware</span> */}
                        {/* <span className="addr"> {item.StreetName} {item.StandardAddress} {item.County}</span> */}
                        <span className="addr">  {item.StandardAddress}, {item.County}</span>

                      </div>
                      <div
                        className="
                  card-area-price
                  d-flex
                  align-items-center
                  justify-content-between
                  my-2
                "
                      >
                        <span className="area"> {item.City} </span>
                        <span className="price">{formatter.format((item.ListPrice))}</span>
                      </div>
                      <div className="cardName-cert d-flex justify-content-between">
                        <div className="d-flex align-items-center">
                          <span className="span me-2">
                            <i className="fas fa-user"></i>
                          </span>
                          <span className="cardname">{item.ListAgentFullName ? item.ListAgentFullName : "Not Given"}</span>
                        </div>

                        <div className="d-flex align-items-center">
                          <span className="span imgSpan me-2">
                            <img src="images/certified.png" className="img-fluid" alt="" />
                          </span>
                          <span className="certified"> Certified</span>
                        </div>
                      </div>
                    </a>
                  </div>

                )
              })
            }
            {/*<!-- single card end  --> */}
            <div className="col-sm-12 col-md-12 mt-5">
              {/* {paginateData?parse(paginateData):""} */}
            </div>
          </div>
        </div>
      </section>
      {/*<!-- property section area end  --> */}
    </div>
  );
};

export default HomeListSection;
