// Library
import { Container } from "react-bootstrap";
import SlickCarousel from "../Carousel/SlickCarousel";

// Components
import RedButton from "../Button/RedButton";

const Jumbotron = () => {
  return (
    <div>
      <div className="jumbotron jumbotron-fluid bg-dark">
        <Container className="container shop-content">
          <div className="shop">
            <div className="shop-wrapper">
              <h1>Search MLS Listings®</h1>
              <h4>
                Toronto Mississauga Vaughan Richmond Hill Oakville
                <br />
                Hamilton Kitchener and Surrounding Areas
              </h4>
              <p>
                Results based on Canada's banking guidelines and your income.
              </p>
              <RedButton className="jt-btn" size="sm">
                Shop Now »
              </RedButton>
            </div>
          </div>
        </Container>
        <Container className="container">
          <SlickCarousel className="custom-carousel" />
        </Container>
      </div>
    </div>
  );
};

export default Jumbotron;
