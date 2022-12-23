// Library
import Image from "next/image";
import { Container, Row, Col } from "react-bootstrap";
import communityImage from "../../public/community/photo_2021-06-11_12-05-15.jpg";

const CommunitySection = () => {
  return (
    <div className="community">
      <Container className="community-wrapper">
        <Row>
          <Col>
            <div className="title-wrapper">
              <h1 className="community-title">Featured MLS Listings</h1>
              <hr />
            </div>
          </Col>
        </Row>
        <Row>
          <div className="community-wrapper">
            <Image
              src={communityImage}
              alt="community"
              layout="responsive"
              objectFit="contain"
            />
          </div>
        </Row>
      </Container>
    </div>
  );
};

export default CommunitySection;
