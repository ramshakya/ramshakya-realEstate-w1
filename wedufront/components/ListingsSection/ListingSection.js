// Library
import { Container, Row, Col } from "react-bootstrap";
import RedButton from "../Button/RedButton";

const ListingSection = () => {
  return (
    <div className="listing">
      <Container className="listing-wrapper">
        <Row>
          <Col>
            <div className="title-wrapper">
              <h1 className="listing-title">Featured MLS Listings</h1>
              <hr />
            </div>
          </Col>
        </Row>
        <Row>
          <Col className="listing-btn-wrapper">
            <RedButton
              className="listing-btn d-flex justify-content-center"
              size="lg"
            >
              View All Featured Listings
            </RedButton>
          </Col>
        </Row>
      </Container>
    </div>
  );
};

export default ListingSection;
