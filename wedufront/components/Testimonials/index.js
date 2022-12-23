import ReactCarousel from './../../ReactCommon/Components/ReactCarousel';
import detect from "../../ReactCommon/utility/detect";
import { useState, useEffect } from "react";
import ShimmerEffect from "../../ReactCommon/Components/ShimmerEffect";
const Testimonials = (props) => {
    const [showCard, setShowCard] = useState(3);
    let cardsData = [];
    if (props.Testimonials) {
        cardsData = props.Testimonials;
    }
    useEffect(() => {
        if (detect.isMobile()) {
            setShowCard(1);
        }
    }, []);
    const renderCards = () => {
        if(cardsData && cardsData.length){
            return cardsData.map((card, index) => {
                return (
                    <div className="col-md-12" key={'test' + index}>
                        <div className="card-holder">
                            <div className="card">
                                <div className="card-img">
                                    <img src={card.Image}
                                        alt={'Image'} className="img-rounded testimonialImage" loading="lazy" />
                                </div>
                                <div className="card-content">
                                    <h6 className="text-400 h3" >{card.Name}</h6>
                                    <div className="content-holder testContent" dangerouslySetInnerHTML={{ __html: card.Description }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )
            });
        }
    }
    return (
        <section>
            <div className="testimonials">
                <div className="container-fluid">
                    <div className="holders" >
                        <br />
                        <div className="title-wrapper pb-4">
                            <h6 className=" h1 text-center text-400">
                                Testimonials
                            </h6>
                            <hr />
                        </div>
                        <div className="cards-container row">
                            <div className="container">
                                {props.isLoading &&
                                    <ShimmerEffect type="cardView" columnCls={"col-lg-3"} count={4} />
                                }
                                <ReactCarousel show={showCard}>
                                    {
                                        renderCards()
                                    }
                                </ReactCarousel>
                                <br />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};
export default Testimonials;