// Library
import Image from "next/image";
import { Row, Col, Container } from "react-bootstrap";
// Components
import WhiteButton from "../Button/WhiteButton";

// Image
import awardImage1 from "../../public/profile/award1.jpg";
import awardImage2 from "../../public/profile/award2.jpg";

const ProfileSection = () => {
  return (
    <div className="profile-wrapper">
      <div className="profile">
        <Container>
          <Row>
            <Col>
              <div className="text-wrp">
                <h1 className="profile-title">Search Realty Corp. Brokerage</h1>
                <p className="profile-desc-1">
                  Work with an award-winning team of real estate agents ranked
                  as Canada’s{" "}
                  <strong className="a-bold">
                    <a href="">#1 Fastest Growing Real Estate Brokerage</a>
                  </strong>{" "}
                  three years running! Ranked in Canadian Business & Maclean’s
                  2018, 2019, & 2020{" "}
                  <strong className="a-bold">
                    <a href="">Growth500e</a>
                  </strong>{" "}
                  list of Canada’s Fastest-Growing Companies.
                </p>
                <p className="profile-desc-2">
                  Connecting home buyers and sellers with Search Realty agents
                  in real-time! Search Realty is a community-focused,
                  future-facing real estate brokerage that is committed to
                  providing the best tools and technology to serve the evolving
                  needs of today’s home buyers, sellers, and REALTORS®.
                </p>
                <WhiteButton text="LEARN MORE »" className="profile-btn" />
              </div>
            </Col>
            <Col lg={5}>
              <div className="image-wrp">
                <div className="award-img-1">
                  <Image
                    src={awardImage1}
                    alt="awardImage1"
                    layout="responsive"
                    objectFit="contain"
                  />
                </div>
                <div className="award-img-2">
                  <Image
                    src={awardImage2}
                    alt="awardImage2"
                    layout="responsive"
                    objectFit="contain"
                  />
                </div>
              </div>
            </Col>
          </Row>
        </Container>
      </div>
    </div>
  );
};

export default ProfileSection;
