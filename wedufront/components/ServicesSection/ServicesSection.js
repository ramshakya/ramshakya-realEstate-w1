import { Container, Row, Col } from "react-bootstrap";
import BlogCard from '../Card/BlogCard2';
import ShimmerEffect from "../../ReactCommon/Components/ShimmerEffect";
const ServicesSection = (props) => {
  const { blogs } = props;
  return (
    <div>
      <Container className="service-wrapper">
        <Row>
          <Col>
            <div className="title-wrapper">
              <h6 className="service-title">{props.heading}</h6>
              <hr />
            </div>
          </Col>
        </Row>
        <Row>
          {props.isLoading &&
            <ShimmerEffect type="cardView" columnCls={"col-lg-3"} count={4} />
          }
          {
            blogs.length &&
            blogs.map((item, index) => {
              let d = new Date(item.created_at).toDateString();
              return (
                <Col lg={4} key={index}>
                  <BlogCard
                    image={item.MainImg}
                    title={item.Title}
                    label='Most Popular'
                    date={d}
                    url={'/blogs/' + item.Url}
                    content={item.Content}
                  />
                </Col>
              );
            })}
        </Row>
      </Container>
    </div>
  );
};
export default ServicesSection;