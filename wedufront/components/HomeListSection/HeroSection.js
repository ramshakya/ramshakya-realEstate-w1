import React from "react";
import Image from 'next/image'
import { front_url } from '../../constants/GlobalConstants'
const HeroSection = (props) => {
    const { banner } = props;
    return (
        <>
            <div className="bg-hero">
                {banner &&
                // 
                    // <Image
                    //     src={banner}
                    //     alt={"Property Image"}
                    //     // layout='fill'
                    //     width={400}
                    //     height={358}
                    //     layout="responsive"
                    //     objectFit='cover'
                    //     className="img-fluid"
                    //     // placeholder="blur"
                    //     // blurDataURL = {`${front_url}images/hero-img.png`}
                    //     priority={true}
                    // />
                    <img src={banner} alt="Homes for Sale and Real Estate Get Listings" className="img-fluid " />
                }
                {/* <img src="images/hero-img.png" alt="Homes for Sale and Real Estate Get Listings" className="img-fluid " /> */}
            </div>
            <div className="dots-pattern">
                <img src="images/dots.png" height={200} width={200} className="img-fluid" alt="image" />
            </div>
        </>
    )
}
export default HeroSection;