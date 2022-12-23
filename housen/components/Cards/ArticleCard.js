import Constants from "../../constants/Global"
import Image from "next/image";
import Link from "next/link";
const ArticleCard=(props)=>{
	const {image,title,label,date,url,content} = props;
	return(
			<>	
			<Link href={url}>
			<a href={url} className="blog-link">
				<div className="blog-container">
	                <div className="blog-img-container">
	                <Image
                        src={image?image:Constants.defaultImage}
                        layout={'responsive'}
                        width={600}
                        height={400}
                        alt="blogs"
                        objectFit={"cover"}
                        placeholder="blur"
                        blurDataURL={image?image:Constants.defaultImage}
                        priority={true}
                        quality='50'
                        
                    />
	                        {/*<img src={image?image:Constants.defaultImage} */}
	                </div>
	                <div className="blog-content">
	                    <div className="blog-label">{label}</div>
	                    <p className="blog-post-date">{date}</p>
	                    <h3 className={`text-bold blog-title ${props.isOne?"fnt-size-30":""}`}>{title}</h3>
						{
							props.isOne &&
						<div className="blog-post-content px-3  " dangerouslySetInnerHTML={{ __html: content.substring(0,180)+'...' }} />
						}

	                </div>
	            </div>
	            </a>
		</Link>
			</>
		)
}	
export default ArticleCard