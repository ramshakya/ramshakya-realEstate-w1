import { Badge } from "react-bootstrap";

const CardFluid = (props) => {
  const {
    className,
    isSale,
    isRent,
    isLease,
    isOpenHouse,
    price,
    address,
    province,
    badge,
    type,
    imgSrc,
    desc,
  } = props;
  return (
    <div className={`card mb-3 card-fluid ${className}`}>
      <div className="row g-0">
        <div className="col-md-4">
          <div className="card-img-wrapper">
            {/* Sale Ribbon */}
            {isSale ? <div className="for-sale-ribbon">For Sale</div> : ""}
            {/* Rent Ribbon */}
            {isRent ? <div className="for-rent-ribbon">For Rent</div> : ""}
            {/* Lease Ribbon */}
            {isLease ? <div className="for-lease-ribbon">For Lease</div> : ""}
            <img
              src={imgSrc}
              className="img-fluid rounded-start card-img-fluid"
            />
            <div className="price">
              ${price}
              {isRent ? <span className="for-rent"> Monthly</span> : ""}
              {isLease ? <span className="for-lease"> /ft</span> : ""}
            </div>
          </div>
        </div>
        <div className="col-md-8">
          <div className="card-body card-body-fluid">
            <p className="property-type m-0 mb-2">{type}</p>
            <h5 className="card-title card-title-fluid">{address}</h5>
            <p className="card-text card-text-fluid">{province}</p>
            <p className="card-text card-desc-fluid">{desc}</p>
            {badge.map((item) => {
              return (
                <div className="badge-wrapper">
                  {!item.bedroom == "" ? (
                    <Badge bg="secondary" className="me-1">
                      Bedroom {item.bedroom}
                    </Badge>
                  ) : (
                    ""
                  )}
                  {!item.bathroom == "" ? (
                    <Badge bg="secondary" className="me-1">
                      Bathroom {item.bathroom}
                    </Badge>
                  ) : (
                    ""
                  )}
                  {!item.size == "" ? (
                    <Badge bg="secondary" className="me-1">
                      {item.size}ft
                    </Badge>
                  ) : (
                    ""
                  )}
                </div>
              );
            })}
          </div>
        </div>
      </div>
    </div>
  );
};

export default CardFluid;
