import { Card, Badge } from "react-bootstrap";

const CardRow = (props) => {

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
  } = props;
  return (
    <>
    <Card className={`${className} mb-3 card-row`}>
      <div className="card-img-wrapper">
        <Card.Img src={imgSrc.s3_image_url} alt="1" className="card-img-row card-img-rows" />
        {/* Sale Ribbon */}
        {isSale ? <div className="for-sale-ribbon">For Sale</div> : ""}
        {/* Rent Ribbon */}
        {isRent ? <div className="for-rent-ribbon">For Rent</div> : ""}
        {/* Lease Ribbon */}
        {isLease ? <div className="for-lease-ribbon">For Lease</div> : ""}
        {/* Open House Ribbon */}
        {isOpenHouse ? (
          <div className="open-house">
            <Badge className="open-houses">Open House</Badge>
          </div>
        ) : (
          ""
        )}
        {/* Price */}
        <div className="price">
          ${price}
          {isRent ? <span className="for-rent"> Monthly</span> : ""}
          {isLease ? <span className="for-lease"> /ft</span> : ""}
        </div>
      </div>
      <Card.Body className="pt-2 card-body-row">
        <p className="property-type m-0 mb-2">{type}</p>
        <Card.Title className="m-0 card-title-row">{address}</Card.Title>
        <Card.Text className="mb-1 card-text-row">{province}</Card.Text>
        {badge.map((item) => {
          return (
            <div className="badge-wrapper">
              {!item.Br == "" ? (
                <Badge bg="secondary" className="me-1">
                  Bedroom {item.Br}
                </Badge>
              ) : (
                ""
              )}
              {!item.Bath_tot == "" ? (
                <Badge bg="secondary" className="me-1">
                  Bathroom {item.Bath_tot}
                </Badge>
              ) : (
                ""
              )}
              {!item.Sqft == "" ? (
                <Badge bg="secondary" className="me-1">
                  {item.Sqft}ft
                </Badge>
              ) : (
                ""
              )}
            </div>
          );
        })}
      </Card.Body>
    </Card>
    <style>
      {`
      .open-houses{
       background-color:#fd8b8f !important;
      }
      .card-body-row{
        border-top: 3px solid #fd8b8f !important;
      }
      `}
    </style>
    </>
  );
};

export default CardRow;
