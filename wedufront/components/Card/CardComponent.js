// Library
import { Card } from "react-bootstrap";
import WhiteButton from "../Button/WhiteButton";

const CardComponent = (props) => {
  const { title, src, text } = props;
  return (
    <div>
      <Card className="custom-card">
        <Card.Img variant="top" src={src} className="custom-img-card" />
        <Card.Body>
          <Card.Title className="custom-title-card">{title}</Card.Title>
          <Card.Text className="custom-text-card">{text}</Card.Text>
          <WhiteButton text="LEARN MORE »" className="card-btn" />
        </Card.Body>
      </Card>
    </div>
  );
};

export default CardComponent;
