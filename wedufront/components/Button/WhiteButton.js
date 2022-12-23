import { Button } from "react-bootstrap";

const WhiteButton = (props) => {
  const { children, size, className, type, setChangeCard } = props;
  return (
    <div>
      <Button
        className={`custom-button-white ${className}`}
        size={size}
        type={type}
        onClick={setChangeCard}
      >
        {children}
      </Button>
    </div>
  );
};

export default WhiteButton;
