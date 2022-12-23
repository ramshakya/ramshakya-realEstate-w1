import React, {useEffect,useState} from "react";
import Link from "next/link";
import { Modal, Button } from "react-bootstrap";
import ReactCarousel from './ReactCarousel';

const PopupModel = (props) =>{
	const [show, setShow] = useState(false);
  	// const handleClose = () => setShow(false);
  	const handleClose=()=>{
  		setShow(false);
  		props.handleClose();
  	}
  	const handleShow = () => setShow(true);
  	useEffect(()=>{
  		if(props.show){
  			setShow(true);
  		}
  		
  	},[props.show])
	return(
			<>	
			
				<Modal show={show} className={`largeModel ${props.className}`}  size="lg" onHide={handleClose}>
				  <Modal.Header closeButton>
				   	
				  </Modal.Header>
				  <Modal.Body>
				  	<ReactCarousel show={1} selectedCarouselIndex={props.selectedCarouselIndex}>
					   	{
					      props.imageArray.map((item) => {
					        return (
					            <>
					               <img src={item}
					                            
					                className={`preConsSlider`} alt="" title="" loading="lazy" />
					            </>)
					        })
					    }
					</ReactCarousel>
				  </Modal.Body>
				  
				</Modal>
				
			</>
		);
	

}
export default PopupModel;